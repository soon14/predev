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


class vshop_openapi extends base_openapi
{
    private $req_params = array();
    public function __construct()
    {
        // $user_obj = vmc::singleton('b2c_user_object');
        // $this->member_id = $user_obj->get_member_id();
        // if(empty($this->member_id)){
        //     $this->_failure('未知用户状态');
        // }
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    public function profit($args = array())
    {
        //$member_id = $this->app->member_id;
        // $mdl_shop = app::get('vshop')->model('shop');
        // $vshop = $mdl_shop->getRow('*',);
    }

    public function home($args = array())
    {
        $shop_id = $args['shop_id'];
        $mdl_shop = app::get('vshop')->model('shop');
        $vshop = $mdl_shop->dump($shop_id);
        $mdl_vshop_lv = app::get('vshop')->model('lv');
        $vshop['lv_info'] = $mdl_vshop_lv->dump($vshop['shop_lv_id']);
        $mdl_pickout = app::get('vshop')->model('pickout');
        $goods_id = $mdl_pickout->getColumn('goods_id', array('shop_id' => $shop_id), 0, -1, 'id,order_num');
        if ($goods_id) {
            $goods_list = app::get('b2c')->model('goods')->getList('*', array('goods_id' => $goods_id));
            vmc::singleton('b2c_goods_stage')->gallery($goods_list);
        } else {
            $goods_list = false;
        }

        $vshop['logo_url'] = base_storager::image_path($vshop['logo'], 'm');
        $vshop['banner_url'] = base_storager::image_path($vshop['gallery_image_id'], 'l');
        $this->success(array(
            'vshop' => $vshop,
            'goods_list' => $goods_list,
            'wxxcx_appid'=>app::get('wechat')->getConf('wxxcx_appid')
        ));
    }


}
