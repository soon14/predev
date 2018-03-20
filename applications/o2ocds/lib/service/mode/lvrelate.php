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
class o2ocds_service_mode_lvrelate{
    private $member_lv;
    private $from_id;
    public function __construct($app)
    {
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
        $this ->member_lv = app::get('b2c')->model('member_lv')->getList("member_lv_id", array('disabled' => 'false'), 0, -1,
            'experience ASC');
        if(!$this ->member_lv){
            return true;
        }
        foreach($this ->member_lv as $k => $v){
            $this ->member_lv[$k] = $v['member_lv_id'];
        }

    }


    /*
     * 分佣模式2
     * 分佣值与会员等级相关
     */
    public function create($order_items ,$member_info){
        //没有此模式
        return true;
        if($this ->app ->getConf('mode') != 2 ){
            return false;
        }
        //全局佣金设置
        foreach($this ->member_lv as $k =>$lv){
            $base_o2ocds['lv'.$k] =  $this->app->getConf('lv'.$k);
        }
        unset($k ,$lv);
        $this -> from_id =$member_info['member_id'];
        //product 单品佣金
        $o2ocds = $this->app->model('products_extend')->getList('product_id ,lv_o2ocds_value',
            array("product_id" => array_keys($order_items)));
        $o2ocds = utils::array_change_key($o2ocds, "product_id");

        foreach ($order_items as $k => $v) {
             //类型佣金设置
            $type_o2ocds = $this->app->model('products_extend')->get_lv_o2ocds_value($order_items[$k]['goods_id']);
            foreach($this ->member_lv as $kk =>$lv){
                $order_items[$k]['o2ocds']['lv'.$kk] = array(
                    $o2ocds[$k]['lv_o2ocds_value']['lv'.$kk] ? $o2ocds[$k]['lv_o2ocds_value']['lv'.$kk] : ($type_o2ocds['lv'.$kk] ? $type_o2ocds['lv'.$kk] :$base_o2ocds['lv'.$kk]),
                    $o2ocds[$k]['lv_o2ocds_value']['lv'.$kk] ? 'product' : ($type_o2ocds['lv'.$kk] ? 'type' : 'base')
                );
                //o2ocds_value>=1 ,则不为比例
                $order_items[$k]['o2ocds'.$kk] += ($order_items[$k]['o2ocds']['lv'.$kk][0] < 1 ? $v['amount'] * $order_items[$k]['o2ocds']['lv'.$kk][0] :$v['nums']*$order_items[$k]['o2ocds']['lv'.$kk][0]);
            }
            unset($kk ,$lv);
        }
        $order_o2ocds = array(); //订单佣金
        foreach($this ->member_lv as $kk =>$lv){
            foreach($order_items as $k => $v){
                $order_o2ocds['o2ocds'.$kk] += $order_items[$k]['o2ocds'.$kk];
            }
            $order_o2ocds['all_o2ocds'] += $order_o2ocds['o2ocds'.$kk];
        }
        unset($v);
        $parents = explode(',', $member_info['parents']);//上级，上上级...
        foreach($parents as $_k =>$_v){
            if($_v <1){
                unset($parents[$_k]);
            }
        }
        $order_fund = 0;
        //订单佣金流向
        $orderlog_achieve = array();
        foreach($parents as $_k =>$_v){
            $orderlog_achieve[$_k]['member_id'] = $_v;
            $current_o2ocds = $this -> _cal_o2ocds($order_o2ocds ,$_v , $parents);
            $orderlog_achieve[$_k]['achieve_fund'] = $current_o2ocds[1];
            $orderlog_achieve[$_k]['parent_type'] = 'lv'.$current_o2ocds[0];
            $order_fund +=  $orderlog_achieve[$_k]['achieve_fund'];
        }

        //订单佣金明细
        foreach ($order_items as $k => $v) {
            foreach($parents as $_k =>$_v){
                $current_o2ocds = $this -> _cal_o2ocds($v ,$_v , $parents);
                $order_items[$k]['o2ocds_items']['lv'.$current_o2ocds[0]] = $current_o2ocds[1];
                $order_items[$k]['product_fund'] += $current_o2ocds[1];
            }
            unset($order_items[$k]['goods_id'], $order_items[$k]['amount']);
        }
        unset($v ,$_v);
        //订单佣金基础信息
        $orderlog = array(
            'order_fund' => $order_fund,
            'items' => $order_items,
            'achieve' => $orderlog_achieve
        );
        return $orderlog;

    }


    /**
     * 计算每个上级应获取到的佣金
     * @param $o2ocds
     * @param $member_id
     * @param array $parents 所有上级，从低到高
     * @return array
     */
    private function _cal_o2ocds($o2ocds ,$member_id ,$parents =array() ){
        $status =1;
        $lv = $this ->_get_member_lv($member_id);
        if($lv ===false){
            return false;
        }
        if($status == 1){//累加
            $from_member_lv = $this ->_get_member_lv($this -> from_id);
            $o2ocds_lv = 0;
            $reverse = array_reverse($parents);
            foreach($reverse as $_k =>$_v){
                if($member_id == $_v){
                    if($reverse[$_k+1]>0){
                        $next_lv = $this ->_get_member_lv($reverse[$_k+1]);
                        $step = $lv - $next_lv;
                    }else{
                        $step = $lv-$from_member_lv;
                    }
                    for($i=0 ; $i<$step ;$i++){
                        $o2ocds_lv += $o2ocds['o2ocds'.($lv-$i)];
                    }
                }
            }
        }else{
            $o2ocds_lv = $o2ocds['o2ocds'.$lv];
        }
        return array($lv ,$o2ocds_lv);

    }

    /*
     * 获取会员等级 ，只返回0，1，2，3...数字
     */
    private function _get_member_lv($member_id){
        if($member_id<1){
            return false;
        }
        $member = app::get('b2c') ->model('members')->getRow('member_lv_id' ,array('member_id' => $member_id));
        foreach($this ->member_lv as $k =>$v){
            if($member['member_lv_id'] == $v){
                return $k;
            }
        }
        return false;
    }
}