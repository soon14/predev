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

class gift_promotion_solutions_givegift implements b2c_interface_promotion_solution
{
    public $name = '送赠品'; // 名称
    public $type = 'general';  //goods\order\general
    public $desc_pre = '[赠]';
    public $desc_post = '';
    public $desc_tag = '送赠品';
    private $description = '';
    private $save = 0;
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function config($aData = array())
    {
        $render = $this->app->render();
        $render->pagedata['data'] = $aData;
        $render->pagedata['type'] = $this->type;
        $render->pagedata['base_url'] = vmc::base_url(1);
        $render->pagedata['sid'] = substr(md5(uniqid()), 0, 6);
        $render->pagedata['input_object_name_prefix'] = 'action_solution[gift_promotion_solutions_givegift]';
        $render->pagedata['input_object_basefilter'] = array();
        return $render->fetch('admin/sales/input/object_gift.html');
    }

    public function apply(&$cart_object, $solution, &$cart_result)
    {
        $gift_arr = $cart_object['gift']?$cart_object['gift']:array();
        foreach ($solution['product_id'] as $product_id) {
            $multiple = $cart_object['quantity'];
            if(is_numeric($solution['quantity'])){
                //每满限定
                $multiple = floor($multiple/$solution['quantity']);
            }
            $gif_quantity = $solution['gift_num'][$product_id] * $multiple;
            if(is_numeric($solution['gift_num_max'][$product_id]) && $gif_quantity > $solution['gift_num_max'][$product_id]){
                //最多赠送限定
                $gif_quantity  = $solution['gift_num_max'][$product_id];
            }
            $gift = array(
                'product_id'=>$product_id,
                'quantity' =>$gif_quantity
            );
            $gift_arr[] = $gift;
        }
        $cart_object['gift'] = $gift_arr;
        $this->setString($gift_arr);

    }

    public function apply_order(&$cart_object, $solution, &$cart_result)
    {
        $gift_arr = $cart_result['gift']?$cart_result['gift']:array();
        foreach ($solution['product_id'] as $product_id) {
            $gift = array(
                'product_id'=>$product_id,
                'quantity' =>$solution['gift_num'][$product_id]
            );
            $gift_arr[] = $gift;
        }
        $cart_result['gift'] = $gift_arr;
        $this->setString($gift_arr);
    }

    public function setString($gift_arr)
    {
        $render = app::get('gift')->render();
        $mdl_products = app::get('b2c')->model('products');
        $gift_arr_ck = utils::array_change_key($gift_arr,'product_id');
        if(!empty($gift_arr_ck)){
            $pids = array_keys($gift_arr_ck);
            $products = $mdl_products->getList('*',array('product_id'=>$pids));
            if($products){
                $products = utils::array_change_key($products,'product_id');
            }
        }
        $router = app::get('site')->router();
        foreach ($gift_arr as $key => $value) {
            $gift_arr[$key]['url'] =$router->gen_url(array('app'=>'b2c','ctl'=>'site_product','act'=>'index','args'=>array($value['product_id']),'full'=>'true'));
            $gift_arr[$key]['product'] = $products[$value['product_id']];
        }
        $render->pagedata['gift_items'] = $gift_arr;
        $this->description = $render->fetch('common/gift_incart.html');
    }

    public function getString()
    {
        return $this->description;
    }

    public function get_status()
    {
        return true;
    }

    public function setSave($save)
    {
        $this->save = $save;
    }
    public function getSave()
    {
        return $this->save;
    }

    public function get_desc_tag()
    {

        if (isset($this->cart_display)) {
            $desc_tag['display'] = $this->cart_display;
        } else {
            $desc_tag['display'] = true;
        }
        $desc_tag['name'] = $this->desc_tag;

        return $desc_tag;
    }
}
