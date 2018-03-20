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

class integralmall_ctl_mobile_detail extends b2c_mfrontpage
{
    public $title = '积分兑换商品详情';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->verify_member();
        $this->set_tmpl('integralmall_detail');
    }
    public function index()
    {
        //获取参数 货品ID
        $goods_stage = vmc::singleton('b2c_goods_stage');
        $params = $this->_request->get_params();
        $data_detail = $goods_stage->detail($params[0], $msg); //引用传递
        if (!$data_detail) {
            vmc::singleton('mobile_router')->http_status(404);
            //$this->splash('error', null, $msg);
        }
        $user_obj = new b2c_user_object();
        $member_info = $user_obj->get_current_member();
        $this->pagedata['member_info'] = $member_info;
        $this->pagedata['data_detail'] = $data_detail;
        $mdl_relgoods = $this->app->model('relgoods');
        $this->pagedata['relgoods'] = $mdl_relgoods->dump($data_detail['goods_id']);
        $this->page('mobile/detail/index.html');
    }

}
