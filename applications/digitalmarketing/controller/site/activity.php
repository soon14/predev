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
class digitalmarketing_ctl_site_activity extends b2c_frontpage{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        $this->member = $this->get_current_member();
    }
    public function index(){

    }

    public function detail($id){

        $activity = $this ->app->model('activity')->dump($id);
        if(!$activity){
            $this->splash('error' ,'' ,'活动不存在');
        }
        $activity['prize'] = $this ->app->model('activity')->getList('*' ,array('activity_id'=>$id ,'nums|than'=>0));
        $activity['member_lv'] = explode(',' ,$activity['member_lv']);
        $activity['prize'] = utils::array_change_key( $activity['prize'], 'prize_grade');
        foreach($activity['prize'] as &$v){
            if($v['prize_type']=='coupon'){
                $v['item'] =app::get('b2c')->model('coupons')->getRow('*' ,array('cpns_id'=>$v['addon']['coupon']));
            }elseif($v['prize_type']=='product'){
                $v['item'] =app::get('b2c')->model('products')->getRow('*' ,array('product_id'=>$v['addon']['product']));
            }

        }
        $this->pagedata['data'] = $activity;
        if($this ->member){
            $this ->pagedata['chance'] = vmc::singleton('digitalmarketing_prize') ->get_chance($this->member['member_id'] ,$id);
            $member_score = app::get('b2c')->model('member_integral')->amount($this->member['member_id']);
            $this->member['score'] = $member_score;
            $this ->pagedata['member'] = $this->member;
        }
        $this->page('site/activity/detail.html');
    }
    public function sel_delivery()
    {
        //查询用户所有的收货地址信息
        $model_member_addr = app::get('b2c')->model('member_addrs');
        $member_addr_condition = [
            'member_id' => $this->member['member_id']
        ];
        $member_addr_infos = $model_member_addr->getList('*', $member_addr_condition);
        $this->pagedata['member_addr_infos'] = $member_addr_infos;
        $this->display('site/sel_delivery.html');
    }
}