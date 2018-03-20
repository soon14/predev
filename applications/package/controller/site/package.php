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


class package_ctl_site_package extends b2c_frontpage
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    /*
       组合商品加入购物车
       @param : 组合活动规则id,主货品id,主货品数量
   */
    public function add($rule_id,$product_id,$num = 1){
        if(!$product_id || !$rule_id){
            $this->splash('error', '', '参数不合法');
        }
        $package_product_id =  array();
        if($_GET['product']){
            $package_product = explode(',',$_GET['product']);
            foreach($package_product  as $key=>$value){
                $tmp = explode('s',$value);
                $package_product_id[$tmp[0]] = $tmp[1];
            }
        }
        unset($_GET);
        $cart_stage = vmc::singleton('b2c_cart_stage');
        $object = array(
            'package'=>array(
                'product_id'=>$product_id,
                'num' => $num,
                'rule_id'=>$rule_id,
                'package_product_id'=>$package_product_id
            )
        );
        $ident = $cart_stage->add('package', $object, $msg);
        if (!$ident) {
            $this->splash('error', '', $msg);
        }
        $forward = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_cart',
            'act' => 'addtocart',
            'args' => array(
                $ident,
            ),
        ));
        $this->splash('success', $forward);
    }
    /*
       组合商品立即购买 
       @param : 组合活动规则id,主货品id,主货品数量
   */
    public function fastbuy($rule_id,$product_id,$num = 1){
        if(!$product_id || !$rule_id){
            $this->splash('error', '', '参数不合法');
        }
        $package_product_id =  array();
        if($_GET['product']){
            $package_product = explode(',',$_GET['product']);
            foreach($package_product  as $key=>$value){
                $tmp = explode('s',$value);
                $package_product_id[$tmp[0]] = $tmp[1];
            }
        }
        unset($_GET);
        $cart_stage = vmc::singleton('b2c_cart_stage');
        $object = array(
            'package'=>array(
                'product_id'=>$product_id,
                'num' => $num,
                'rule_id'=>$rule_id,
                'package_product_id'=>$package_product_id
            )
        );
        $ident = $cart_stage->add('package', $object, $msg,true);
        if (!$ident) {
            $this->splash('error', '', $msg);
        }
        $forward = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_checkout',
            'act' => 'fastbuy',
        ));
        $this->splash('success', $forward);
    }
}
