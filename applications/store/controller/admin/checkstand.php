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


class store_ctl_admin_checkstand extends store_ctl_admin_controller
{
    private $is_across =false;
    public function __construct($app) {
        parent::__construct($app);
        $this->obj_pos_cash = vmc::singleton('store_pos_cash');
        $user_obj = vmc::singleton('b2c_user_object');
        if($user_obj ->is_login()){
            $user_obj ->set_member_session(null);
        }
        $this ->pagedata['across'] = $this ->is_across;
    }

    /**
     * 收银台首页->选择店铺
     */
    public function index(){
        //必须先选择店铺
        $this->select_store();
    }

    /**
     * 收银台首页单独页
     */
    public function single_index(){
        $model_store = app::get('store')->model('store');

        if($_GET['store_id']){
            $this->now_selected_store = (int) $_GET['store_id'];
            $_SESSION['now_selected_store'] = $this->now_selected_store;
            //判断是否可以操作这个店铺
            if(count($this->can_cashier_store_ids) > 0 && in_array($this->now_selected_store, $this->can_cashier_store_ids) == false){
                exit('您不能在这个店铺收银');
            }

        }else{
            if(!$_SESSION['now_selected_store']){
                //查找一个店铺作为默认的
                $default_store = app::get('store')->model('relation_desktopuser') ->getRow('*' ,array('user_id' =>$this ->user->user_id));
                $this->now_selected_store = $default_store['store_id'];
                $_SESSION['now_selected_store'] = $this->now_selected_store;
            }else{
                $this->now_selected_store = $_SESSION['now_selected_store'];
            }

        }

        //查询店铺信息
        $this->pagedata['now_selected_store'] = $this->now_selected_store;
        $this->pagedata['store_info'] = $model_store->getRow('*' ,array('store_id'=>$this->now_selected_store));

        //检查当前用户是否拥有现金收款权限,bool转化为0或1
        $this->pagedata['have_cash_pay_permission'] = intval($this->has_permission('store_cash_collections'));

        //检查当前用户是否有打印店铺收银的权限
        $this->pagedata['have_print_store_report_permission'] = intval($this->has_permission('print_store_report'));

        $this ->pagedata['user'] = $this ->user->user_data;

        $this->pagedata['base_full_url'] = vmc::base_url(1);
        $this ->pagedata['report'] =array(
            'from' => date('Y-m-d 00:00'),
            'to' => date('Y-m-d H:i')
        );

        $this->singlepage('admin/checkstand/index.html', '店铺收银台');


    }

    public function mscreen(){
        $this->pagedata['storeInfo'] = app::get('store')->model('store')->getStoreInfoById($this->now_selected_store);
        $this->singlepage('admin/checkstand/mscreen.html');
    }


    /** 获取最后一次数据加入购物车
     * @param array $products 商品
     * @param array $cpns   优惠券
     * @param int $member_id
     * @param string $is_delivery
     * @param string $addr_id   收货地址
     * @return mixed
     */
    private function _get_cart($products=array() , $cpns = array() , $member_id = 0 ,$is_delivery='N' ,$addr_id=null ,$dlytype =null){
        $cart_object = [];
        $check_params = [];
        $warning_msg = [];
        $msg = '';
        $_SESSION['TEMPORARY_SHOPPING'] = true;//设置临时购物车模式。不操作数据库true
        $cart_stage = vmc::singleton('b2c_cart_stage');
        //设置购物车会员
        if (is_numeric($member_id) && $member_id > 0) {
            $cart_stage->set_member_id($member_id);
            $member_info = $cart_stage->get_member();
            $return_data['member_info'] = $member_info;
            $check_params['member_id'] = $member_id;
            $check_params['addr_id'] = $addr_id;
        }else{
            $cart_stage->set_member_id(null);
        }
        $cart_stage->clean();//每次都重置购物车
        //商品
        foreach ($products as $product_id => $num) {
            $cart_object['goods'][] = array(
                'goods' => array(
                    'product_id' => $product_id,
                    'num' => $num,
                ),
            );
        }
        //优惠券
        foreach ($cpns as $k => $c) {
            $cart_object['coupon'][] = array(
                'coupon' => $c,
            );
        }
        foreach ($cart_object as $key => $objects) {
            foreach ($objects as $object) {
                $add_to_cart_result = $cart_stage->add($key, $object, $msg);
                if (!$add_to_cart_result) {
                    $warning_msg[] = array(
                        'warning' => $msg,
                        'object' => $object[key($object)]
                    );
                }
            }
        }

        $check_params['cart_result'] = $cart_stage->currency_result();
        if($is_delivery !='Y'){
            $check_params['is_delivery'] = 'N';//无需配送
        }else{
            $check_params['dlytype_id'] = $dlytype;
        }
        $checkout_result = vmc::singleton('b2c_checkout_stage')->check($check_params);
        $return_data['checkout_info'] = $checkout_result;
        $return_data['warning_msg'] = $warning_msg;
        return $return_data;
    }

    /**
     * 更新购物车(加入/更新 优惠券,商品)
     */
    public function update_cart(){
        $products = $_POST['products'];
        $coupons = $_POST['coupons'];
        $member_id = $_POST['member_id'];
        $is_delivery = $_POST['is_delivery'];
        $addr_id = $_POST['addr_id'];
        $dlytype = $_POST['dlytype'];
        $cart_service = vmc::singleton('store_service_cart');
        $return_data = $this ->_get_cart($products ,$coupons, $member_id ,$is_delivery ,$addr_id ,$dlytype);
        $this->splash('success', null, '购物车更新成功', null, $return_data);
    }



    /**
     * 收银台创建订单
     */
    public function neworder(){
        $params = utils::_filter_input($_POST);
        $member_id = $params['member_id'];
        $logger = vmc::singleton('b2c_order_log');
        $cart_stage = vmc::singleton('b2c_cart_stage');
        $cart_stage ->set_member_id($member_id);
        $logger->set_operator(array(
            'ident' => $member_id,
            'name' => '会员',
            'model' => 'members',
        ));

        //新订单标准数据
        $order_sdf = array(
            'member_id' => $member_id,
            'memo' => '门店订单',
            'pay_app' => $params['payment']['pay_app_id'],
            'dlytype_id' => $params['dlytype'] ?$params['dlytype']:null,
            'createtime' => time() ,
            'need_shipping' => $params['is_delivery'],
            'platform' => 'store',
        );

        if ($order_sdf['need_shipping'] == 'Y') {
            if (!$order_sdf['dlytype_id']) {
                $logger->fail('create', '未知配送方式', $params);
                $this->splash('error', '' , '未知配送方式');
            }

            //COD FIX
            if ($order_sdf['pay_app'] == '-1' || $order_sdf['pay_app'] == 'cod') {
                $order_sdf['is_cod'] = 'Y';
            } else {
                $dlytype = app::get('b2c')->model('dlytype')->dump($params['dlytype_id']);
                if ($dlytype['has_cod'] == 'true') {
                    $order_sdf['pay_app'] = 'cod';
                    $order_sdf['is_cod'] = 'Y';
                }
            }

            if (!$params['addr_id']) {
                $logger->fail('create', '无收货人信息', $params);
                $this->splash('error', '' , '无收货人信息');
            } else {
                $consignee = app::get('b2c')->model('member_addrs')->getRow('name,area,addr,zip,tel,mobile,email', array(
                    'member_id' => $member_id,
                    'addr_id' => $params['addr_id'],
                ));
                $order_sdf['consignee'] = $consignee;
            }
        }
        if (!$order_sdf['pay_app']) {
            $logger->fail('create', '未知支付方式', $params);
            $this->splash('error', '' ,'未知支付方式');
        }
        //购物车数据
        $cart_result = $cart_stage->currency_result();
        if ($cart_stage->is_empty($cart_result)) {
            $logger->fail('create', '没有可结算商品', $params);
            $this->splash('error', '' , '没有可结算商品');
        }

        $order_create_service = vmc::singleton('b2c_order_create');
        $this ->begin();
        //&$order_sdf、&$msg
        if (!$order_create_service->generate($order_sdf, $cart_result, $msg)) {
            $msg = $msg ? $msg : '数据组织失败';
            $logger->fail('create', $msg, $params);
            $this->end(false, $msg);
        }
        if (!$order_create_service->save($order_sdf, $msg)) {
            $msg = $msg ? $msg : '数据保存失败';
            $logger->fail('create', $msg, $order_sdf);
            $this->end(false, $msg);
        }
        $relation_mdl = $this ->app ->model('relation_orders');
        $relation_data = array(
            'order_id' => $order_sdf['order_id'],
            'store_id' =>$this->now_selected_store,
            'op_id' =>$this ->user ->user_id,
            'op_no' =>$this ->user ->user_data['op_no']
        );
        if(!$relation_mdl ->save($relation_data)){
            $msg = '订单门店信息保存失败';
            $logger->fail('create', $msg, $order_sdf);
            $this->end(false, $msg);
        }
        $logger->set_order_id($order_sdf['order_id']);

        /*
         * 优惠券冻结,优惠券使用记录
         * 未使用成功in_use!="true"的优惠券不做冻结处理，不做记录
         * @see /Applications/b2c/lib/postfilter/promotion.php line 200
         */
        foreach ($cart_result['objects']['coupon'] as $coupon) {
            if ($coupon['params']['in_use'] != 'true') {
                continue;
            }
            $couponlog_data = array(
                'member_id' => $member_id,
                'order_id' => $order_sdf['order_id'],
                'cpns_id' => $coupon['params']['cpns_id'],//优惠券ID
                'memc_code' => $coupon['params']['code'],//优惠券号码
                'cpns_name' => $coupon['params']['name'],//优惠券名称
                'coupon_save' => $coupon['params']['save'],//优惠券在本次订购中抵扣的金额
                'order_total' => $order_sdf['order_total'],//订单应付金额
            );
            vmc::singleton('b2c_coupon_stage')->couponlog($couponlog_data, $msg);
            if ($coupon['params']['cpns_type'] == '1') {
                //需冻结会员账户内的相关B类券
                vmc::singleton('b2c_coupon_stage')->freeze_member_coupon($member_id, $coupon['params']['code'], $msg);
            }
        }

        //清理购物车
    //    $cart_stage->clean();
        $this->end(true, $order_sdf);
    }

    /**
     * 收银台创建订单
     */
    public function neworder_old(){

        $objPosorder = vmc::singleton('store_pos_order');

        //开启事务
        $this->begin();

        //店铺的订单增加店铺操作员id
        $_POST['op_id'] = $this->user->user_id;
        //创建订单
        $createOrderResult = $objPosorder->createOrder($_POST);

        if($createOrderResult === false){

            //事物回滚
            $this->end(false, $objPosorder->getMsg());
        }

        //事务提交
        $this->end(true, '订单创建成功', '', ['order_info' => $objPosorder->getOrderInfo()]);
    }

    /**
     * 查询用户所有的收货地址信息
     */
    public function sel_delivery()
    {
        //验证输入信息
        $member_id = intval($_POST['member_id']);
        if($member_id <= 0){

            $this->splash('error', '', '会员id错误');
        }

        //查询用户所有的收货地址信息
        $model_member_addr = app::get('b2c')->model('member_addrs');
        $member_addr_condition = [
            'member_id' => $member_id
        ];
        $member_addr_infos = $model_member_addr->getList('*', $member_addr_condition);
        if(is_array($member_addr_infos) == false || count($member_addr_infos) == 0){
            $member_addr_infos = [];
        }
        $this->pagedata['member_addr_infos'] = $member_addr_infos;
        $dlytype = $this ->get_dlytype();
        if($this ->is_across){
            $this ->pagedata['dlytype'] = $dlytype['across'];
        }else{
            $this ->pagedata['dlytype'] = $dlytype['normal'];
        }
        $splash_params = [
            'modal_content' => $this->fetch('admin/pos/sel_delivery.html')
        ];
        $this->splash('success', '', '查询成功', '', $splash_params);
    }

    /**
     * 添加/编辑收货地址
     */
    public function edit_delivery(){
        $_POST = utils::_filter_input($_POST);
        $mdl_maddr = app::get('b2c')->model('member_addrs');
        $member_id = intval($_POST['member_id']);
        $action = $_POST['action'];
        $msg = '';
        $addr_data = $_POST['maddr'];
        switch ($action) {
            case 'set_default':
                if (!$mdl_maddr->set_default($addr_data['addr_id'], $member_id)) {

                    $this->splash('error', '', '设置失败');
                }
                $msg = '设置成功';
                break;
            case 'edit':
                $addr_id = $_POST['addr_id'];
                $addr_info = $mdl_maddr->getRow('*', ['addr_id' => $addr_id]);
                if(is_array($addr_info) == false || count($addr_info) == 0){

                    $this->splash('error', '', '获取收货地址信息错误');
                }
                $this->pagedata['addr_info'] = $addr_info;

                $this->splash('success', '', '获取收货地址信息成功', '', ['modal_content' => $this->fetch('admin/pos/sel_delivery.html')]);
                break;
            case 'delete':
                $del_condition = [
                    'member_id' => $member_id,
                    'addr_id' => $addr_data['addr_id']
                ];
                if (!$mdl_maddr->delete($del_condition)) {

                    $this->splash('error', '', '删除失败');
                }
                $msg = '删除成功';
                break;
            case 'save':
                $addr_data = $_POST['maddr'];
                $addr_data['member_id'] = $member_id;
                $save_result = $mdl_maddr->save($addr_data);
                if (!$save_result) {

                    $this->splash('error', '', '保存失败');
                }
                $msg = '保存成功';
                break;
            default:
                $this->splash('error', '', '未知操作');
                break;
        }

        $this->splash('success', '', $msg);
    }

    /**
     * 查询用户所有的优惠卷
     */
    public function sel_coupon()
    {
        //查询用户所有的优惠券
        $member_id = intval($_POST['member_id']);
        if($member_id <= 0){

            $this->splash('error', '', '必须先选择会员');
        }

        //获取当前购物车中的coupon、
        $car_stage = vmc::singleton('b2c_cart_stage');
        $car_stage->set_member_id($member_id);
        $cartAll = $car_stage ->result();

        $mycoupons = [];

        $available_coupons = vmc::singleton('b2c_coupon_stage')->get_member_couponlist($member_id, $mycoupons);
        foreach($available_coupons as $k=>$v){
            foreach($cartAll['objects']['coupon'] as $kk => $vv){
                if(!array_key_exists($vv['coupon'], $available_coupons)){
                    $cpns_detail = app::get('b2c') ->model('coupons') ->getCouponA($vv['coupon']);
                    $cpns_detail[0]['check'] = $vv['params']['in_use'];
                    $available_coupons[$vv['coupon']] = $cpns_detail[0];
                }
                if($vv['params']['in_use'] && $k == $vv['coupon']){
                    $available_coupons[$k]['check'] = true;
                }
            }
        }

        $this->pagedata['available_coupons'] = $available_coupons;

        $this->pagedata['mycoupons'] = $mycoupons;
        $memc_code_arr = array();
        foreach ($mycoupons as $coupon) {
            $memc_code_arr[] = $coupon['memc_code'];
        }
        $couponlogs = app::get('b2c')->model('member_couponlog')->getList('*', array('member_id' => $member_id, 'memc_code' => $memc_code_arr));
        $this->pagedata['couponlogs'] = utils::array_change_key($couponlogs, 'memc_code');

        $this->splash('success', '', '操作成功', '', ['modal_content' => $this->fetch('admin/pos/sel_coupon.html')]);
    }

    /**
     * 添加优惠券到购物车
     */
    public function add_coupon_to_cart(){
        $coupons = utils::_filter_input($_POST['coupons']);
        $member_id = intval($_POST['member_id']);
        $check_params = [];
        $return_data = [];
        $msg = '';

        if ($member_id <= 0) {

            $this->splash('error', '', '错误的会员id');
        }

        $check_params['member_id'] = $member_id;

        $cart_stage = vmc::singleton('b2c_cart_stage');
        $cart_stage->set_member_id($member_id);
        $member_info = $cart_stage->get_member();

        $return_data['member_info'] = $member_info;

        //添加优惠券到购物车
        $add_coupon_to_cart_data = [
            'coupon' => $coupons
        ];
        $add_result = $cart_stage->add('coupon', $add_coupon_to_cart_data, $msg);
        if(!$add_result){

            $this->splash('error', '', '优惠券添加失败');
        }

        $check_params['cart_result'] = $cart_stage->currency_result();
        $checkout_result = vmc::singleton('b2c_checkout_stage')->check($check_params);

        $return_data['checkout_info'] = $checkout_result;

        $this->splash('success', '', '优惠券添加成功', '', $return_data);
    }

    /**
     * 收银台搜索商品
     */
    public function product_filter()
    {
        $product_filter = utils::_filter_input($_POST['filter']);
        if(
            empty($product_filter['search_keyword']) === true
            || is_numeric($product_filter['store_id']) === false
            || $product_filter['store_id'] <= 0
        ){

            $this->splash('error', '', '参数错误');
        }
        if($product_filter['search_type'] === 'barcode'){
            $product_condition = " AND a.barcode LIKE '{$product_filter['search_keyword']}%'";
        }else if($product_filter['search_type'] === 'bn'){
            $product_condition = " AND a.bn LIKE '{$product_filter['search_keyword']}%'";
        }else{
            $product_condition = " AND a.name LIKE '%{$product_filter['search_keyword']}%'";
        }
        $mdl_product = app::get('b2c')->model('products');
        $sql = "SELECT
                a.*,c.store_id
            FROM
                vmc_b2c_products AS a
            JOIN
                vmc_b2c_goods AS b
            ON
                a.goods_id = b.goods_id
            JOIN
                vmc_store_relation_goods AS c
            ON
                b.goods_id = c.goods_id
            WHERE
                c.store_enable = '1' AND
                c.store_id = '{$product_filter['store_id']}'
                {$product_condition} LIMIT 0,5";

        $products = $mdl_product->db->select($sql);
        foreach ($products as $key => $value) {
            $item_group[$value['goods_id']][] = $value;
        }
        $this->pagedata['item_group'] = $item_group;
        $this->display('admin/checkstand/item_group.html');
    }

    /**
     *
     * @param $store_id
     */
    public function item_edit($store_id)
    {
        $product_id = $_GET['product_id'];
        $mdl_product = app::get('b2c')->model('products');
        $filter = array(
            'product_id' => $product_id,
        );
        $product = $mdl_product->getRow('*', $filter);
        $this->pagedata['product'] = $product;

        $this->display('admin/checkstand/item_edit.html');
    }

    /**
     * ajax根据商品条码筛选商品
     */
    public function product_filter_by_barcode(){
        $product_filter = utils::_filter_input($_POST['filter']);
        if(
            empty($product_filter['barcode']) === true
            || is_numeric($product_filter['now_selected_store']) === false
            || $product_filter['now_selected_store'] <= 0
        ){

            $this->splash('error', '', '参数错误');
        }

        $product_condition = " AND a.barcode = '{$product_filter['barcode']}'";

        //查询货品信息
        $mdl_product = app::get('b2c')->model('products');
        $sql = "SELECT
                a.*,c.store_id
            FROM
                vmc_b2c_products AS a
            JOIN
                vmc_b2c_goods AS b
            ON
                a.goods_id = b.goods_id
            JOIN
                vmc_store_relation_goods AS c
            ON
                b.goods_id = c.goods_id
            WHERE
                c.store_enable = '1' AND
                c.store_id = '{$product_filter['now_selected_store']}'
                {$product_condition}";
        $product_info = $mdl_product->db->select($sql);

        if(is_array($product_info) == false || count($product_info) == 0){
            $this->splash('error', '', '没有这个商品');
        }
        $this->splash('success', '', '查找成功', '', ['product' => $product_info['0']]);
    }

    /**
     * ajax根据会员卡号或手机号查询会员
     */
    public function get_member_info(){
        $member_phone_or_card = utils::_filter_input($_POST['member_phone_or_card']);
        if(empty($member_phone_or_card) == true){

            $this->splash('error', '', '请输入会员帐号');
        }
        $member = app::get('pam') ->model('members')->getRow("*" ,array('login_account' => $member_phone_or_card));
        if($member){
            $member_id = $member['member_id'];
        }else{
            $this->splash('error', '', '该会员不存在');
        }
        $model_members = app::get('b2c')->model('members');
        $member_condition = [
            'member_id' => $member_id,
        ];
        $member_info = $model_members->getRow('*', $member_condition);
        if(is_array($member_info) == false || count($member_info) == 0){

            $this->splash('error', '', '没有这个会员');
        }

        //获取会员等级信息
        $model_member_lv = app::get('b2c')->model('member_lv');
        $member_lv_info = $model_member_lv->getRow('name, dis_count', ['member_lv_id' => $member_info['member_lv_id']]);

        $member_info['levelname'] = $member_lv_info['name'];
        $member_info['lv_discount'] = $member_lv_info['dis_count'];

        $this->splash('success', '', '查询成功', '', ['member_info' => $member_info]);
    }

    public function check_limit(){
        $member_id = $_POST['member_id'];
        if(!$member_id){
            $this ->splash('error' ,null ,'请先录入会员信息');
        }
        $products = $_POST['products'];
        $coupons = $_POST['coupons'];
        $member_id = $_POST['member_id'];
        $is_delivery = $_POST['is_delivery'];
        $addr_id = $_POST['addr_id'];

        $checkout = $this ->_get_cart($products ,$coupons, $member_id ,$is_delivery ,$addr_id);
        $this ->splash('success' ,null ,$checkout);
        $this ->splash('error' ,null ,$msg);

    }

    public function save_member(){
        $api_crm = vmc::singleton('apicenter_api_crm');

        if($_POST['act'] == 'bind'){
            //绑卡
            $data = array(
                'login_account' => $_POST['login_account'],
                'member_card' => $_POST['card'],
                'cardtype_name' => $_POST['card_type']
            );
            $msg = $api_crm ->member_card($data);
            if($msg == 'success'){
                $this ->splash('success' ,null ,'绑定成功');
            }
        }else{
            //注册会员
            $data = array(
                'login_account' => $_POST['login_account'],
                'login_password' => $_POST['login_account'],
                'card_number' => $_POST['card'],
                'card_name' => $_POST['card_type'],
            );
            $msg = $api_crm ->member_signup($data , $member_id);
            if($msg == 'success'){
                //获取会员等级信息
                $member_info = app::get('b2c')->model('members')->getRow('*', array('member_id' => $member_id));
                $model_member_lv = app::get('b2c')->model('member_lv');
                $member_lv_info = $model_member_lv->getRow('name, dis_count', array('member_lv_id' => $member_info['member_lv_id']));
                $member_info['levelname'] = $member_lv_info['name'];
                $member_info['lv_discount'] = $member_lv_info['dis_count'];
                $this ->splash('success' ,null ,array('member_info' =>$member_info));
            }
        }
        $this ->splash('error' ,null ,$msg);

    }

    public function get_dlytype(){
        $dlytype_list = app::get('b2c')->model('dlytype')->getList("*" ,array('disable' =>'false'));
        $tags = vmc::database() ->select('select * from  vmc_desktop_tag where tag_name="跨境" AND app_id="b2c" ');
        $tag = $tags[0];
        $tag_rel = array();
        if($tag){
            $tag_rel = app::get('desktop')->model('tag_rel')->getList('rel_id' ,array('tag_id' =>$tag['tag_id']));
            $tag_rel = array_keys(utils::array_change_key($tag_rel ,'rel_id'));
        }
        $normal= $across = array();
        foreach($dlytype_list as $k =>$v){
            if(in_array($v['dt_id'] ,$tag_rel)){
                $across[] = $v;
            }else{
                $normal[] = $v;
            }
        }
        return array(
            'normal' => $normal,
            'across' =>$across
        );

    }
}
