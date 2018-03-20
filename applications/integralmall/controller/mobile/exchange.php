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

class integralmall_ctl_mobile_exchange extends b2c_mfrontpage
{
    public $title = '积分兑换确认';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->verify_member();
        $this->set_tmpl('integralmall_exchange');
    }
    public function index()
    {
        //获取参数 货品ID
        $params = $this->_request->get_params();
        $product_id = $params[0];
        $quantity = $params[1];
        $goods_stage = vmc::singleton('b2c_goods_stage');
        $data_detail = $goods_stage->detail($product_id, $msg); //引用传递
        if (!$data_detail) {
            //vmc::singleton('mobile_router')->http_status(404);
            $this->splash('error', null, $msg);
        }
        $user_obj = new b2c_user_object();
        $member_info = $user_obj->get_current_member();
        $this->pagedata['member_info'] = $member_info;
        $this->pagedata['goods'] = $data_detail;
        $mdl_relgoods = $this->app->model('relgoods');
        $this->pagedata['relgoods'] = $mdl_relgoods->dump($data_detail['goods_id']);
        $this->_process_pagedata();
        $this->page('mobile/exchange/index.html');
    }
    public function crate_order()
    {
        $order_logger = vmc::singleton('b2c_order_log');
        $member_id = $this->app->member_id;
        $order_logger->set_operator(array(
            'ident' => $member_id,
            'name' => '会员',
            'model' => 'members',
        ));
        $params = utils::_filter_input($_POST);
        if (!$params['addr_id']) {
            $this->splash('error', null, '无收货人信息');
        }
        if (!$params['product_id']) {
            $this->splash('error', null, '未知积分抵扣商品');
        }
        if (!$params['quantity']) {
            $params['quantity'] = 1;
        }
        $product_id = $params['product_id'];
        $quantity = $params['quantity'];
        //订单标准数据
        $order_sdf = array(
            'order_type' => 'integralmall',
            'member_id' => $member_id,
            'memo' => $params['memo'],
            'pay_app' => 'integraldeduction',
            'dlytype_id' => $params['dlytype_id'],
            'createtime' => time() ,
            'need_shipping' => $params['need_shipping'],
            'need_invoice' => $params['need_invoice'],
            'invoice_title' => $params['invoice_title'],
            'platform' => 'mobile',
        );

        $consignee = app::get('b2c')->model('member_addrs')->getRow('name,area,addr,zip,tel,mobile,email', array(
            'member_id' => $member_id,
            'addr_id' => $params['addr_id'],
        ));
        if (!$consignee) {
            $this->splash('error', null, '收货地址不正确');
        }
        $order_sdf['consignee'] = $consignee;
        $cart_result = $this->_organize_cartresult($product_id, $quantity, $errmsg);
        if (!$cart_result) {
            $this->splash('error', null, $errmsg);
        }
        $db = vmc::database();
        //开启事务
        $this->transaction_status = $db->beginTransaction();
        $order_create_service = vmc::singleton('b2c_order_create');
        //&$order_sdf、&$msg
        if (!$order_create_service->generate($order_sdf, $cart_result, $msg)) {
            $db->rollback(); //事务回滚
            $msg = $msg ? $msg : '数据组织失败';
            $this->splash('error', null, $msg);
        }
        if (!$order_create_service->save($order_sdf, $msg)) {
            $db->rollback(); //事务回滚
            $msg = $msg ? $msg : '数据保存失败';
            $this->splash('error', null, $msg);
        }
        $goods_id = $cart_result['objects']['goods'][0]['item']['product']['goods_id'];
        $mdl_relgoods = app::get('integralmall')->model('relgoods');
        $popularity = $mdl_relgoods->getColumn('popularity',array('goods_id'=>$goods_id));
        $popularity_val = $popularity[0]+$quantity;
        $update_popularity = $mdl_relgoods->update(array('popularity' => $popularity_val), array('goods_id' => $goods_id));
        $db->commit($this->transaction_status); //事务提交
        $order_logger->set_order_id($order_sdf['order_id']);
        $order_logger->success('create', '订单创建成功', $params);
        $this->splash('success', null, '兑换成功', true, $order_sdf);
    }
    private function _process_pagedata()
    {
        //会员收获地址读取
        $member_id = $this->app->member_id;
        $mdl_maddr = app::get('b2c')->model('member_addrs');
        if ($member_addrs = $mdl_maddr->getList('*', array(
            'member_id' => $member_id,
        ), 0, -1, '`is_default` ASC,updatetime DESC,`addr_id`')) {
            $def_addr = $member_addrs[0]; //会员默认收货地址
            $member_addrs = utils::array_change_key($member_addrs, 'addr_id');
            $addr_id = $_POST['addr_id'];
            if ($addr_id) {
                $member_addrs[$addr_id]['selected'] = 'true';
                $area = $member_addrs[$addr_id]['area'];
            } else {
                $area = $def_addr['area'];
                $member_addrs[$def_addr['addr_id']]['selected'] = 'true';
            }
            $area_id = array_pop(explode(':', $area));
            $this->pagedata['member_addrs'] = $member_addrs;
        }
        //根据地区获得送货方式
        $mdl_dltype = app::get('b2c')->model('dlytype');
        $dlytypes = $mdl_dltype->getAvailable($area_id);
        foreach ($dlytypes as $key => $item) {
            if ($item['has_cod'] == 'true') {
                unset($dlytypes[$key]);
            }
        }
        $dlytypes = utils::array_change_key($dlytypes, 'dt_id');
        $dlytype_id = $_POST['dlytype_id'];
        if ($dlytypes[$dlytype_id]) {
            $dlytypes[$dlytype_id]['selected'] = 'true';
        } else {
            $dlytypes[key($dlytypes) ]['selected'] = 'true';
        }

        $this->pagedata['dlytypes'] = $dlytypes;
    }
     /**
      * 模拟组织积分商城购物车数据.
      */
     private function _organize_cartresult($product_id, $quantity, &$error_msg)
     {
         $user_obj = new b2c_user_object();
         $member_info = $user_obj->get_current_member();
         $product = app::get('b2c')->model('products')->dump($product_id);
         if (!$product) {
             $error_msg = '未知商品';

             return false;
         }
         $relgoods = app::get('integralmall')->model('relgoods')->getRow('*', array(
             'goods_id' => $product['goods_id'],
             'marketable' => 'true',
         ));
         if (!$relgoods) {
             $error_msg = '非积分商城商品,或未在积分商城上架';

             return false;
         }
         $final_deduction = $relgoods['deduction'] * $quantity;
         if ($member_info['integral'] < $final_deduction) {
             $error_msg = '积分不足';

             return false;
         }
         $obj_math = vmc::singleton('ectools_math');
         $product_amount = $obj_math->number_multiple(array(
             $product['price'],
             $quantity,
         ));
         $obj_ident = 'goods_'.$product['product_id'];
         $cart_result = array(
             'objects' => array(
                 'goods' => array(
                     array(
                         'obj_ident' => $obj_ident,
                         'obj_type' => 'goods',
                         'params' => array(
                             'item' => array(
                                 'product_id' => $product['product_id'],
                             ),
                             'warning' => false,
                         ),
                         'quantity' => $quantity,
                         'is_fastbuy' => true,
                         'member_id' => $member_info['member_id'],
                         'item' => array(
                             'product' => $product,
                         ),
                         'gain_score' => 0,
                     ),
                 ),
             ),
             'gain_score' => 0,
             'goods_count' => $quantity,
             'object_count' => 1,
             'weight' => $product['weight'] * $quantity,
             'cart_amount' => $product_amount,
             'member_id'=>$member_info['member_id']
         );

         //修改购物车商品项成交价
         $cart_result['objects']['goods'][0]['item']['product']['buy_price'] = 0;
         //购物车优惠总计同步
         $cart_result['goods_promotion_discount_amount'] = $product_amount;
         $cart_result['promotion_discount_amount'] = $product_amount;

         $pitem = array(
             'tag' => '积分购买', //促销规则标签
             'name' => '[积分商城]积分抵扣商品金额',
             'desc' => "抵扣{$product_amount},消耗{$final_deduction}积分.",
             'rule_id' => '0',
             'solution' => "抵扣{$product_amount},消耗{$final_deduction}积分." ,
             'save' => $product_amount, //节省小计
         );
         $cart_result['promotions']['goods'][$obj_ident][] = $pitem;
         $cart_result['integraldeduction']['score_u'] = $final_deduction;
         $cart_result['integraldeduction']['deduction'] = $final_deduction;
         $cart_result['finally_cart_amount'] = 0;
         $cart_result['free_shipping'] = 'true';//积分商城包邮

         return $cart_result;
     }
}
