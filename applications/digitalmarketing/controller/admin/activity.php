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

class digitalmarketing_ctl_admin_activity extends desktop_controller{
    public function index(){
        $now =time();
        $sql ="select count(1) as _count ,`type` from vmc_digitalmarketing_activity where from_time<=$now AND to_time>$now group by `type`";
        $numbers = vmc::database()->select($sql);
        $numbers = utils::array_change_key($numbers ,'type');
        $marketing =array(
            '1' =>array(
                'name' =>'幸运大转盘',
                'desc' =>'常见转盘式抽奖',
                'activity_num'=> $numbers[1]['_count']
            ),
            '2'=>array(
                'name' =>'水果机',
                'desc' =>'水果方格转盘抽奖',
                'activity_num'=> $numbers[2]['_count']
            ),
            '3'=>array(
                'name' =>'刮刮卡',
                'desc' =>'通过刮开卡片进行抽奖',
                'activity_num'=> $numbers[3]['_count']
            ),
            '4'=>array(
                'name' =>'摇一摇',
                'desc' =>'让用户摇一摇进行抽奖',
                'activity_num'=> $numbers[4]['_count']
            ),
        );
        $this->pagedata['marketing'] =$marketing;

        $this ->page('admin/activity/index.html');
    }

    public function activity($type){
        if ($this->has_permission('digitalmarketing_activity_edit')) {
            $custom_actions[] = array(
                'label' => ('新建营销') ,
                'icon'=>'fa-plus',
                'href' => 'index.php?app=digitalmarketing&ctl=admin_activity&act=edit&p[0]='.$type,
            );
        }
        $this->finder('digitalmarketing_mdl_activity' ,array(
            'title'=>'营销活动',
            'actions'=>$custom_actions,
            'base_filter'=>array('type'=>$type)
        ));
    }

    public function edit($type=0 ,$id){
        $this ->pagedata['type'] = $type;
        if($id){
            $subsdf = array(
                'prize'=>array('*')
            );
            $activity = $this ->app->model('activity')->dump($id ,'*' ,$subsdf);
            $activity['member_lv'] = explode(',' ,$activity['member_lv']);
            $activity['prize'] = utils::array_change_key( $activity['prize'], 'prize_grade');
            foreach($activity['prize'] as &$v){
                if($v['prize_type']=='coupon'){
                    $v['item'] =app::get('b2c')->model('coupons')->getRow('*' ,array('cpns_id'=>$v['addon']['coupon']));
                }elseif($v['prize_type']=='product'){
                    $v['item'] =app::get('b2c')->model('products')->getRow('*' ,array('product_id'=>$v['addon']['product']));
                }

            }
            $activity['url'] = app::get('site')->router() ->gen_url(array(
                'app'=>'digitalmarketing',
                'ctl'=>'site_activity',
                'act'=>'detail',
                'args'=>array($activity['activity_id']),
                'full'=>1
            ));
            $this->pagedata['activity']=$activity;
            $this->pagedata['type'] =$activity['type'];
        }
        $marketing =array(
            '1' =>array(
                'name' =>'幸运大转盘',
            ),
            '2'=>array(
                'name' =>'水果机',
            ),
            '3'=>array(
                'name' =>'刮刮卡',
            ),
            '4'=>array(
                'name' =>'摇一摇',
            ),
        );
        $this ->pagedata['type_name'] = $marketing[$type]['name'];
        $this ->pagedata['prize'] = array(
            '1'=>array(
                'name'=>'一等奖',
            ),
            '2'=>array(
                'name'=>'二等奖',
            ),
            '3'=>array(
                'name'=>'三等奖',
            ),
            '4'=>array(
                'name'=>'普通奖',
            )
        );
        $this ->pagedata['member_level'] = app::get('b2c')->model('member_lv')->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');
        $this ->pagedata['cpns_filter'] = array('cpns_type'=>1);
        $this ->page('admin/activity/edit.html');
    }

    public function save(){
        $mdl_activity = $this->app->model('activity');
        $data = $_POST;
        if($data['point']<=0){
            $this ->splash('error' ,'','中奖概率必须大于0');
        }

        $data['from_time']= strtotime($data['from_time']);
        $data['to_time']= strtotime($data['to_time']);
        $data['member_lv'] = empty($data['member_lv']) ? null : implode(',', $data['member_lv']);
        if(!$data['activity_id']){
            $data['createtime'] = time();
            $data['bn'] =$mdl_activity->apply_bn();
            $data['opt_id'] =vmc::singleton('desktop_user')->get_id();
        }
        foreach ($data['prize'] as $k=>$v) {
            $data['prize'][$k]['prize_grade'] = $k;
        }
        $this ->begin();
        $mdl_activity->has_many['prize'] = 'prize:contrast';
        if($mdl_activity->save($data)){
            $data['url'] = app::get('site')->router() ->gen_url(array(
                'app'=>'digitalmarketing',
                'ctl'=>'site_activity',
                'act'=>'detail',
                'args'=>array($data['activity_id']),
                'full'=>1
            ));
            $img_url = vmc::openapi_url('openapi.qrcode', 'encode');
            $data['qrcode'] = $img_url.'?txt='.urlencode( $data['url'] );
            $this ->end(true,$data);
        }
        $this ->end(false);
    }


    public function get_coupon(){
        $coupon = app::get('b2c')->model('coupons')->getRow('*' ,array('cpns_id'=>$_POST['cpns_id']));
        $this ->splash('success' ,'',$coupon);
    }

    public function get_product(){
        $product = app::get('b2c')->model('products')->getRow('*' ,array('product_id'=>$_POST['product_id']));
        $this ->splash('success' ,'',$product);
    }


    public function partin($activity_id){
        $this->finder('digitalmarketing_mdl_partin' ,array(
            'title'=>'参与记录',
            'base_filter'=>array('activity_id'=>$activity_id)
        ));
    }

    public function win($activity_id){
        $this->finder('digitalmarketing_mdl_win' ,array(
            'title'=>'中奖记录',
            'base_filter'=>array('activity_id'=>$activity_id)
        ));
    }
}
