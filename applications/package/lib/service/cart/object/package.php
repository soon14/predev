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


class package_service_cart_object_package implements b2c_interface_cart_object
{
    private $app;
    private $member_ident; // 用户标识
    private $mdl_cartobjects;
    /**
     * 构造函数.
     *
     * @param $object $app  // service 调用必须的
     */
    public function __construct()
    {
        $this->session = vmc::singleton('base_session');
        $this->session->start();
        $this->member_info = vmc::singleton('b2c_cart_stage')->get_member();
        $this->mdl_cartobjects = app::get('b2c')->model('cart_objects');
        if (!empty($this->member_info)) {
            $this->member_id = $this->member_info['member_id'];
        }
        $this->obj_math = vmc::singleton('ectools_math');
    }
    public function get_type()
    {
        return 'package';
    }
    public function get_part_type()
    {
        return array();
    }
    /**
     * 购物车项参数组织.
     */
    private function _params($object)
    {
        $mdl_products = app::get('b2c')->model('products');
        $package_product_id = $object['package_product_id'];
        foreach($object['package_goods'] as $key=>$goods){
            if($package_product_id[$goods['goods_id']]){
                $object['package_goods'][$key]['product_id'] = $package_product_id[$goods['goods_id']];
            }else{
                $product = $mdl_products->getRow('product_id',array('goods_id'=>$goods['goods_id'],'is_default'=>'true'));
                if(!empty($product)){
                    $object['package_goods'][$key]['product_id'] = $product['product_id'];
                }
            }
        }
        return $object;
    }
    /**
     * 添加购物车项($object = array('package'=>array('product_id'=>int,'package_id'=>int))).
     *
     * @return bool
     */
    public function add_object($object, &$msg = '', $append = true, $is_fastbuy = false)
    {
        $object = $object['package'];
        $rule_id = $object['rule_id'];
        $mdl_rule = app::get('package')->model('rules');
        //主商品加入购物车
        $cart_stage = vmc::singleton('b2c_cart_stage');
        $goods_object = array(
            'goods' => array(
                'product_id' => $object['product_id'],
                'num' => $object['num'],
            ),
        );
        $ident = $cart_stage->add('goods', $goods_object, $msg,$is_fastbuy);
        if(!$ident){
            return false;
        }
        //组合商品加入购物车end
        $rule = $mdl_rule->getRow('*',array('id'=>$rule_id));
        if(empty($rule)){
            $msg = '没有找到该组合套餐';
            return false;
        }
        $arr_save = array(
            'obj_ident' => 'package_'.$rule_id,
            'obj_type' => 'package',
            'params' => $this->_params(array(
                'package_name'=>$rule['name'],
                'package_product_id' => $object['package_product_id'],
                'package_goods' => $rule['package_goods'],
            )) ,
            'quantity' => $object['num'],
        );
        if ($is_fastbuy) {
            $arr_save['is_fastbuy'] = 'true';
        }
        $arr_save['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $arr_save['member_ident'] = md5($this->member_id);
            $arr_save['member_id'] = $this->member_id;
        }
        if ($append && !$is_fastbuy) {
            // 如果存在相同组合商品 则追加
            $filter = array(
                'obj_ident' => $arr_save['obj_ident'],
                'member_ident' => $arr_save['member_ident'],
            );
            if ($arr_save['member_id']) {
                $filter['member_id'] = $arr_save['member_id'];
            }
            if ($cart_object = $this->mdl_cartobjects->getRow('*', $filter)) {
                $arr_save['quantity'] += $cart_object['quantity'];
                if(is_array($_SESSION['CART_DISABLED_IDENT'])){
                    $_SESSION['CART_DISABLED_IDENT'] = array_diff($_SESSION['CART_DISABLED_IDENT'],array($cart_object['obj_ident']));
                }
            }else{
                $arr_save['time'] = time();//纪录首次加入时间
            }
        }
        if ($this->member_id) {
            $arr_save['member_ident'] = md5($this->member_id);
            $arr_save['member_id'] = $this->member_id;
        }
        if (!$this->_check($arr_save, $msg)) { //验证加入项
            return false;
        }
        $is_save = $this->mdl_cartobjects->save($arr_save);
        if (!$is_save) {
            $msg = ('购物车状态保存异常');

            return false;
        }
        return $arr_save;
    }
    //更新购物车项数量
    public function update($ident, $quantity, &$msg)
    {
        $arr_save = array(
            'obj_ident' => $ident,
            'obj_type' => 'package',
        );
        $arr_save['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $arr_save['member_ident'] = md5($this->member_id);
            $arr_save['member_id'] = $this->member_id;
        }
        $cart_object = $this->mdl_cartobjects->getRow('*', $arr_save);

        if (floatval($quantity) == floatval($cart_object['quantity'])) {
            return $arr_save['obj_ident'];
        }
        $cart_object['quantity'] = floatval($quantity);

        if (!$this->_check($cart_object, $msg)) { //验证加入项
            return false;
        }
        $is_save = $this->mdl_cartobjects->save($cart_object);
        if (!$is_save) {
            $msg = ('购物车状态保存异常');

            return false;
        }

        return $cart_object['obj_ident'];
    }
    /**
     * 指定的购物车商品项.
     *
     * @param string $sIdent
     * @param bool   $rich   // 是否只取cart_objects中的数据 还是完整的sdf数据
     *
     * @return array
     */
    public function get($ident = null, $rich = false, $is_fastbuy = false)
    {
        if (empty($ident)) {
            return $this->getAll($rich, $is_fastbuy);
        }
        $filter = array(
            'obj_ident' => $ident,
            'member_ident' => $this->member_ident,
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }
        $cart_objects = $this->mdl_cartobjects->getList('*', $filter);
        if (empty($cart_objects)) {
            return array();
        }
        if ($rich) {
            $cart_objects = $this->_get_rich($cart_objects);
        }

        return $cart_objects;
    }
    // 购物车里的所有组合套餐项
    public function getAll($rich = false, $is_fastbuy = false)
    {
        $filter = array(
            'obj_type' => 'package',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }
        $cart_objects = $this->mdl_cartobjects->getList('*', $filter);
        if (!$rich) {
            return $cart_objects;
        }

        return $this->_get_rich($cart_objects);
    }
    // 删除购物车中指定商品项
    public function delete($sIdent = null, $is_fastbuy = false)
    {
        if (!$sIdent || empty($sIdent)) {
            return $this->deleteAll();
        }
        $filter = array(
            'obj_ident' => $sIdent,
            'obj_type' => 'package',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }

        return $this->mdl_cartobjects->delete($filter);
    }
    // 清空购物车中商品项数据
    public function deleteAll($is_fastbuy = false)
    {
        $filter = array(
            'obj_type' => 'goods',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }

        return $this->mdl_cartobjects->delete($filter);
    }
    // 小计购物车
    public function count(&$cart_result)
    {
        if (empty($cart_result['objects']['package'])) {
            return false;
        }
        $cart_result['object_count'] = count($cart_result['objects']['package']);
        //[objects]['goods']['item']['product']
        foreach ($cart_result['objects']['package'] as $package) {
            if ($package['disabled'] == 'true') {
                $cart_result['object_count']-=1;
                continue;
            } //该项被禁用
            foreach($package['params']['package_goods'] as $product){

                //购物车重量
                $count_weight = $this->obj_math->number_multiple(array(
                    $product['weight'],
                    $product['quantity'] * $package['quantity'],
                ));
                $cart_result['weight'] = $this->obj_math->number_plus(array(
                    $cart_result['weight'], $count_weight
                ));
                //购物车单项小记
                $count_cart_amount = $this->obj_math->number_multiple(array(
                    $product['package_price'],
                    $product['quantity'] * $package['quantity'],
                ));
                $cart_result['cart_amount'] = $this->obj_math->number_plus(array(
                    $cart_result['cart_amount'],
                    $count_cart_amount,
                ));
                $cart_result['goods_count'] += $product['quantity'] * $package['quantity'];
            }
        }
    }
    /**
     * 获得购物车丰富的详细的数据.
     *
     * @param array $package_objects 购物车组合套餐标准数据
     *
     * @return array 包含货品详细数据,购物车项状态数据
     */
    private function _get_rich($cart_objects)
    {
        $mdl_product = app::get('b2c')->model('products');
        $mdl_goods = app::get('b2c')->model('goods');
        foreach ($cart_objects as $key => $package) {
            $cart_objects[$key]['disabled'] = 'false';
            $cart_objects[$key]['price'] = 0.000;
            $product_id_arr = array();
            foreach($package['params']['package_goods'] as $package_key => $package_product){
                $package_goods['warning'] = '';
                $product = $mdl_product->getRow('*', array(
                    'product_id' => $package_product['product_id'],
                ));
                $goods_info = $mdl_goods->getRow('goods_id,brand_id,cat_id,type_id,marketable,image_default_id,score,nostore_sell,min_buy', array(
                    'goods_id' => $package_product['goods_id'],
                ));
                $product = array_merge($product,$goods_info);
                if($product['marketable'] == 'false'){
                    $product['warning'] = '商品已下架';
                    $cart_objects[$key]['disabled'] = 'true'; //不能参与结算
                }
                if ($product['nostore_sell'] != '1' && !vmc::singleton('b2c_goods_stock')->is_available_stock(
                $product['bn'],
                $package_product['quantity'],$abs_stock)) {
                    $product['warning'] = '库存不足,当前最多可售数量:'.$abs_stock;
                    $cart_objects[$key]['disabled'] = 'true'; //不能参与结算
                }
                if(!$product['image_id']){
                    $product['image_id'] = $product['image_default_id'];
                }
                $package_product = array_merge($package_product,$product);
                $cart_objects[$key]['price'] += $package_product['package_price'] * $package_product['quantity'];
                $cart_objects[$key]['params']['package_goods'][$package_key] = $package_product;
            }
        }
        return $cart_objects;
    }
    //加入\更新购物车时验证
    private function _check($object, &$msg)
    {
        return true;
    }
}
