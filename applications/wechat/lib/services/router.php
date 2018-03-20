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
class wechat_services_router
{
    public function __construct()
    {
    }
    public function exec($request)
    {
        return true;
        //TODO 静候微信支持
        $app = $request->get_app_name();
        $ctl = $request->get_ctl_name();
        $act = $request->get_act_name();
        $params = $request->get_params();
        $is_wechat = base_mobiledetect::is_wechat();
        $is_wechat && $ver_count = intval(implode('', explode('.', $is_wechat)));
        //if ('LOCAL TEST') {
        if($ver_count && $ver_count>653 && app::get('wechat')->getConf('wxxcx_autorouter')){
            //微信入口小程序拦截 @Wechat Version must than 6.5.3
            $ctl_match = $app.'_'.$ctl;
            //商品详情
            if (in_array($ctl_match,array('b2c_mobile_product','b2c_site_product')) && $act == 'index') {
                $_id = $params[0];
                if (substr($_id, 0, 1) == 'g') {
                    $product_list = app::get('b2c')->model('products')->getList('product_id', array('goods_id' => substr($_id, 1)), 0, -1, 'is_default');
                    $_id = $product_list[0]['product_id'];
                }
                $wxxcx_page = '/pages/product/product?product_id='.$_id.'&'.http_build_query($_GET);
            }
            //列表
            if (in_array($ctl_match,array('b2c_mobile_list','b2c_site_list')) && $act == 'index') {
                $wxxcx_page = '/pages/gallery/gallery?'.http_build_query($_GET);
            }

            if ($wxxcx_page) {
                $qrcode_url = vmc::singleton('wechat_xcxstage')->get_qrcode_url($wxxcx_page);
                if($qrcode_url){
                    vmc::singleton('base_component_response')->set_redirect($qrcode_url)->send_headers();
                    exit;
                }
            }
        }
    }


}
