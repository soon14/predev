<?php
// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------
class digitalmarketing_prize
{
    public $app;
    public function __construct($app){
        $this ->app =$app;
    }

    //校验用户是否还可以抽奖
    public function check_activity($member_id ,$activity_id ,&$msg){
        $chance = $this ->get_chance($member_id ,$activity_id);
        $now =time();
        if($now<$chance['from_time']){
            $msg ='该活动还未开始';
            return false;
        }
        if($now>$chance['to_time']){
            $msg ='该活动已经结束';
            return false;
        }
        $member = app::get('b2c') ->model('members') ->getRow('member_lv_id' ,array('member_id'=>$member_id));
        if(!in_array($member['member_lv_id'] ,$chance['member_lv'])){
            $msg ='您的会员等级不能参与该活动';
            return false;
        }
        if((string)$chance['today_chance']!='no_limit' && $chance['today_chance'] <1){
            $msg ='您今天的机会已经用完了';
            return false;
        }
        if((string)$chance['all_chance']!='no_limit' && $chance['all_chance'] <1){
            $msg ='您的机会已经用完了';
            return false;
        }
        if($chance['need_score']>0){
            $member_score = app::get('b2c')->model('member_integral')->amount($member_id);
            if($chance['need_score'] >$member_score){
                $msg ='您的积分不足';
                return false;
            }
            //扣除积分
            $integral_charge = array(
                'member_id'=>$member_id,
                'change_reason'=>'prize_u',//抵扣
                'change'=> -($chance['need_score']),
                'op_model'=>'member',
                'op_id'=>$member_id
            );
            if(!vmc::singleton('b2c_member_integral')->change($integral_charge,$msg)){
                $msg = '积分扣除失败!';
                return false;
            }
        }
        return $chance['activity'];
    }

    //计算用户剩余抽奖次数
    public function get_chance($member_id ,$activity_id){
        $activity_mdl =  $this->app ->model('activity');
        $partin_mdl =  $this->app ->model('partin');
        $activity = $activity_mdl ->dump($activity_id);
        $partin_filter = array('member_id' =>$member_id , 'activity_id' =>$activity_id);
        $nums =$partin_mdl ->count($partin_filter);//参与的总次数
        $partin_filter['createtime|bthan'] = strtotime(date('Y-m-d 00:00:00'));
        $partin_filter['createtime|lthan'] = strtotime(date('Y-m-d 00:00:00' ,strtotime('1 day')));
        $today_nums = $partin_mdl ->count($partin_filter);//今天参与的总次数
        return array(
            'all_chance'=>'no_limit',
            'today_chance' =>$activity['frequency_limit']>0?$activity['frequency_limit']-$today_nums:'no_limit',
            'need_score' =>$activity['use_score'],
            'member_lv' =>explode(',', $activity['member_lv']),
            'today_nums' =>$today_nums,
            'from_time' =>$activity['from_time'],
            'to_time' =>$activity['to_time'],
            'status' =>$activity['status'],
            'activity'=>$activity
        );
    }

    //根据抽奖算法获取奖项
    public function get_prize($activity){
        $prize = $this->app->model('prize') ->getList('*' ,array('activity_id' =>$activity['activity_id'] ,'nums|than'=>0));
        $prize = utils::array_change_key($prize ,'prize_id');
        $arr = array();
        $no_prize =array(
            'prize_id'=>0,
            'item'=>'很遗憾，您没有抽中'
        );
        foreach ($prize as $v) {
            $arr[$v['prize_id']] = $v['nums']*$activity['point'];//有奖的概率
        }
        $prize_total = array_sum($arr);
        $arr[0] =$prize_total*(100-$activity['point'])/$activity['point'];//无奖的概率
        $prize_id =  $this ->get_rand($arr); //根据概率获取奖项id
        if($prize_id>0 && $prize[$prize_id]['nums']> $prize[$prize_id]['win_nums']){
            $win = $prize[$prize_id];
            if($win['prize_type']=='coupon'){
                $win['item'] =app::get('b2c')->model('coupons')->getRow('*' ,array('cpns_id'=>$win['addon']['coupon']));
            }elseif($win['prize_type']=='product'){
                $win['item'] =app::get('b2c')->model('products')->getRow('*' ,array('product_id'=>$win['addon']['product']));
            }
        }else{
            $win = $no_prize;
        }
        return $win;
    }

    public function get_rand($num_arr){
        $sum = array_sum($num_arr);
        //概率数组循环
        foreach ($num_arr as $k => $v) {
            $rand_num = mt_rand(1, $sum);
            if ($rand_num <= $v) {
                $prize_id = $k;
                break;
            } else {
                $sum -= $v;
            }
        }
        unset ($v);
        return $prize_id;
    }

    //记录抽奖
    public function prize_log($member_id , $prize ,$activity){
        $partin =array(
            'prize_id' =>$prize['prize_id'],
            'activity_id' =>$activity['activity_id'],
            'member_id' =>$member_id,
            'is_win' =>$prize['prize_id']>0?'1' :'0',
            'createtime' =>time()
        );
        $activity['partin_nums']+=1;
        if($partin['is_win']=='1'){
            $activity['win_nums']+=1;
            $prize['win_nums'] +=1;
        }
        $partin_mdl =  $this->app ->model('partin');
        $prize_mdl =  $this->app ->model('prize');
        $activity_mdl =  $this->app ->model('activity');
        if($prize['prize_id'] && !$prize_mdl->save($prize)){
            return false;
        }
        if($partin_mdl ->save($partin) && $activity_mdl->save($activity)){
            return $partin;
        }
        return false;
    }


    //用户领奖
    public function award($member_id ,$partin_id ,$addr_id, &$msg, &$win){
        $partin_mdl =  $this->app ->model('partin');
        $activity_mdl =  $this->app  ->model('activity');
        $prize_mdl =  $this->app  ->model('prize');
        $partin  = $partin_mdl ->dump($partin_id);
        if($partin['member_id'] !=$member_id){
            $msg = '获奖数据和会员不对应';
            return false;
        }
        if($partin['is_win'] =='0'){
            $msg = '用户没有中奖，无需发放奖品';
            return false;
        }
        if($partin['status'] =='1'){
            $msg = '该奖品已发放';
            return false;
        }
        $activity = $activity_mdl ->dump($partin['activity_id']);
        $time = time();
        if($time > $activity['to_time']){
            $msg = '超过该活动的兑奖截止时间';
            return false;
        }
        //兑奖逻辑
        $prize = $prize_mdl->dump($partin['prize_id']);
        if(!$prize || $prize['nums']==0){
            $msg = '该奖品不存在';
            return false;
        }
        if($prize['prize_type'] =='score'){
            if(!$this ->give_score($member_id ,$prize['addon']['score'] ,$msg)){
                return false;
            }
            $prize_detail = array(
                'name' =>$prize['addon']['score'].'积分'
            );
        }elseif($prize['prize_type'] =='coupon'){
            if(!$this ->give_coupon($member_id ,$prize['addon']['coupon'] ,1,$msg)){
                return false;
            }
            $prize_detail = app::get('b2c')->model('coupons')->getRow('*' ,array('cpns_id'=>$prize['addon']['coupon']));
        }elseif($prize['prize_type'] =='product'){
            if(!$order = $this ->give_product($member_id ,$prize['addon']['product'] ,$addr_id ,$msg)){
                return false;
            }
            $prize_detail = $order['items'][0];
        }
        $partin['status'] ='1';
        if(!$partin_mdl ->save($partin)){
            $msg = '数据保存失败1';
            return false;
        }
        $win = array(
            'partin_id' => $partin_id ,
            'prize_id' => $prize['prize_id'],
            'prize_type'=> $prize['prize_type'],
            'prize_detail'=>$prize_detail,
            'order_id'=>$order['order_id'],
            'activity_id' => $activity['activity_id'] ,
            'member_id' => $member_id,
            'createtime' => time(),
        );
        if(!$this->app->model('win') ->save($win)){
            $msg = '数据保存失败2';
            return false;
        }
        return true;

    }

    private function give_score($member_id ,$score,&$msg=''){
        $integral_charge = array(
            'member_id'=>$member_id,
            'change_reason'=>'prize_g',//获取积分
            'change'=> $score,
            'op_model'=>'member',
            'op_id'=>$member_id
        );
        if(!vmc::singleton('b2c_member_integral')->change($integral_charge,$msg)){
            $msg = '积分发放失败!';
            return false;
        }
        return true;
    }


    private function give_coupon($member_id,$cpns_id,$cpns_num=1,&$msg=''){
        $cpns_mdl = app::get('b2c')->model('coupons');
        $list = $cpns_mdl->downloadCoupon($cpns_id,$cpns_num, '1', '抽奖发放优惠卷', '用户领取');
        foreach ($list as $v) {
            $memc = array(
                'member_id' => $member_id,
                'cpns_id' => $cpns_id,
                'memc_code' => $v,
                'memc_gen_time' => time(),
            );
            $mdl_member_coupon = app::get('b2c')->model('member_coupon');
            if(!$mdl_member_coupon->save($memc)){
                $msg= '优惠券发送到会员账户失败！';
                return false;
            }
        }
        return true;

    }

    private function give_product($member_id ,$product_id,$addr_id=null  ,&$msg){
        if($addr_id){
            $consignee = app::get('b2c')->model('member_addrs')->getRow('name,area,addr,zip,tel,mobile,email', array(
                'member_id' => $member_id,
                'addr_id' => $addr_id,
            ));
        }else{
            $consignee = app::get('b2c')->model('member_addrs')->getRow('name,area,addr,zip,tel,mobile,email', array(
                'is_default' => 'true',
                'member_id' => $member_id,
            ));
        }
        if(!$consignee){
            $msg = '收货地址不正确';
            return false;
        }
        //新订单标准数据
        $dlytype= app::get('b2c')->model('dlytype')->getRow('*' ,array('dt_type'=>'logistics'));
        $order_sdf = array(
            'member_id' => $member_id,
            'pay_app' => 'cod',
            'dlytype_id' => $dlytype['dt_id'],
            'createtime' => time() ,
            'need_shipping' => 'Y',
            'need_invoice' => 'false',
            'platform' => 'mobile',
            'order_type' =>'prize'
        );
        $order_sdf['consignee'] = $consignee;

        $new_order_id  = app::get('b2c')->model('orders')->apply_id();
        $product = app::get('b2c') ->model('products') ->dump($product_id);
        $sdf = array(
            'order_id' => $new_order_id, //订单唯一ID
            'weight' => $product['weight'], //货品总重量
            'quantity' => 1, //货品总数量
            'ip' => base_request::get_remote_addr() , //下单IP地址
            'memberlv_discount' => 0, //会员身份优惠总额
            'pmt_goods' => 0, //商品级优惠促销总额
            'pmt_order' => 0, //订单级促销优惠总额
            'finally_cart_amount' => 0, //购物车优惠后总额
            'score_g' => 0,//订单可得积分
            'order_total' => 0, //订单应付当前货币总额
            'cost_tax' =>0, //营业税
            'cost_protect' => 0, //保价费
            'cost_payment' => 0, //支付手续费
            'cost_freight' =>0, //运费

        );
        $order_sdf = array_merge($order_sdf, $sdf);

        $order_sdf['items'][] = array(
            'order_id' => $new_order_id,
            'product_id' => $product['product_id'],
            'goods_id' => $product['goods_id'],
            'bn' => $product['bn'],
            'barcode' => $product['barcode'],
            'name' => $product['name'],
            'spec_info' => $product['spec_info'],
            'price' => $product['price'],
            'member_lv_price' => $product['member_lv_price'],
            'buy_price' => $product['buy_price'],
            'amount' => $product['buy_price'] ,
            'nums' => 1,
            'weight' => $product['weight'] ,
            'image_id' => $product['image_id'],
        );
        $order_create_service = vmc::singleton('b2c_order_create');
        if (!$order_create_service->save($order_sdf, $msg)) {
            $msg = $msg ? $msg : '订单保存失败';
            return false;
        }
        return $order_sdf;
    }
}