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


class sale_ctl_mobile_sale extends b2c_mfrontpage
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    
    public function get_status($product_id)
    {
        if(!vmc::singleton('b2c_user_object')->is_login()){
            echo $this->fetch('mobile/product/nologin.html');
        }else{
            if(!$product_id){
                echo '没有product_id';
                exit;
            }else{
                $this->pagedata['product_id'] = $product_id;
            }
            $mdl_sales = $this->app->model('sales');
            $sale = $mdl_sales->getRow('*',array('goods_id'=>$_POST['goods_id'],'status'=>'0'));
            $this->pagedata['now'] = $now  = time();
            $this->pagedata['sale'] = $sale;
            $page = vmc::singleton('sale_sale')->get_status($sale,$now);
            echo $this->fetch('mobile/product/status/'.$page.'.html');
        }
    }
    public function save_reserve(){
        $msg = '';
        $mdl_reserve = $this->app->model('reserve');
        $reserve_data = array(
                'tel'=>$_POST['tel'],
                'goods_id'=>$_POST['goods_id'],
                'sale_id'=>$_POST['sale_id'],
                'createtime'=>time()
        );
        if($mdl_reserve->save($reserve_data,null,false,$msg)){
            $this->splash('success',null,'预约成功');
        }else{
            $this->splash('error',null,$msg);
        }
    }
    public function reserve($sale_id,$goods_id){
        if(!$sale_id){
            echo '没有活动id';
            return false;
        }else{
            $this->pagedata['sale_id'] = $sale_id;
        }
        $this->verify_member();
        $mdl_reserve = $this->app->model('reserve');
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        $reserve = $mdl_reserve->getRow('*',array('goods_id'=>$goods_id,'member_id'=>$member_id,'sale_id'=>$sale_id));
        if(!empty($reserve)){
            $this->title = '您已经预约过此活动';
            $this->splash('success');
        }else{
            $this->pagedata['goods_id'] = $goods_id;
            $this->page('mobile/product/reserve_form.html');
        }
    }
}
