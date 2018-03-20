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

class fpurchase_ctl_site_cart extends b2c_frontpage
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        vmc::singleton('base_session')->start();
        $this->cart_stage = vmc::singleton('b2c_cart_stage');
        if ($this->app->member_id = vmc::singleton('b2c_user_object')->get_member_id()) {
            $this->cart_stage->set_member_id($this->app->member_id);
        }
        $this->_response->set_header('Cache-Control', 'no-store');
    }
    public function add()
    {
        $cart_object = $_POST['cart_obj'];
        $added_count = 0;
        foreach ($cart_object as $product_id => $num) {
            if (!isset($num) || $num == '') {
                continue;
            }
            $object = array(
                'goods' => array(
                    'product_id' => $product_id,
                    'num' => $num,
                ),
            );
            if($this->cart_stage->add('goods', $object, $msg)){
                $added_count++;
            }
        }
        if($added_count){
            $this->splash('success', null);
        }else{
            $this->splash('error', null);
        }

    }
}
