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


class store_ctl_admin_stock extends store_ctl_admin_controller
{
    public function __construct($app) {
        parent::__construct($app);

        //必须先选择店铺
        //$this->select_store();
    }

    /**
     * 店铺库存列表
     */
    public function index()
    {

        $finderParams = array(
            'title' => '库存列表',
            'finder_extra_view' => array(
                array(
                    'app' => 'store',
                    'view' => '/admin/finder/top_store_filter.html',
                    'extra_pagedata' => $this->can_cashier_store_ids,
                )
            )
        );

        $model_class_name = 'store_mdl_storestock';


        if(count($this->can_cashier_store_ids) > 0){
            $finder_params['base_filter'] = [
                'store_id' => $this->can_cashier_store_ids
            ];
        }

        $this->finder($model_class_name, $finderParams);
    }

    /**
     * 店铺库存->进货管理
     */
    public function purchases_manage(){
        $finder_params = [
            'title' => '进货单列表',
            'actions' => [
                [
                    'label' => ('添加进货单'),
                    'icon'  => 'fa-plus',
                    'href'  => 'index.php?app=store&ctl=admin_stock&act=edit_purchases_receipts&action=add',
                ],
            ],
            'finder_extra_view' => [
                [
                    'app' => 'store',
                    'view'=>'/admin/finder/top_store_filter.html',
                    'extra_pagedata' => $this->can_cashier_store_ids,
                ]
            ]
        ];
        $model_class_name = 'store_mdl_purchases_receipts';

        if(count($this->can_cashier_store_ids) > 0){
            $finder_params['base_filter'] = [
                'store_id' => $this->can_cashier_store_ids
            ];
        }

        $this->finder($model_class_name, $finder_params);
    }

    /**
     * 店铺库存->损益盘点
     */
    public function income_stock(){
        $finderParams = [
            'title' => '损益单单列表',
            'actions' => [
                [
                    'label' => ('添加损益单'),
                    'icon'  => 'fa-plus',
                    'href'  => 'index.php?app=store&ctl=admin_stock&act=edit_income_receipts&action=add',
                ],
            ],
            'finder_extra_view' => [
                [
                    'app' => 'store',
                    'view'=>'/admin/finder/top_store_filter.html',
                    'extra_pagedata' => $this->can_cashier_store_ids,
                ]
            ]
        ];
        $model_class_name = 'store_mdl_income_receipts';

        if(count($this->can_cashier_store_ids) > 0){
            $finder_params['base_filter'] = [
                'store_id' => $this->can_cashier_store_ids
            ];
        }

        $this->finder($model_class_name, $finderParams);
    }

    /**
     * 添加/修改进货单页面
     */
    public function edit_purchases_receipts(){

        $this->pagedata['userId'] = $this->user->user_id;
        $this->pagedata['store_filter']['store_id'] = $this->can_cashier_store_ids;
        $this->page('admin/stock/edit_purchases_receipts.html');
    }

    /**
     * 添加/修改损益单页面
     */
    public function edit_income_receipts(){
        $this->pagedata['userId'] = $this->user->user_id;
        $this->pagedata['store_filter']['store_id'] = $this->can_cashier_store_ids;
        $this->page('admin/stock/edit_income_receipts.html');
    }

    /**
     * 添加进货单处理方法
     */
    public function edit_purchases_receipts_action(){
        $obj_edit_purchases_receipts = vmc::singleton('store_stock_edit_purchases_receipts');
        $this->begin();
        $addResult = $obj_edit_purchases_receipts->add_purchases_receipts($_POST['purchases_receipts']);
        if($addResult === false){

            $this->end(false, $obj_edit_purchases_receipts->get_msg());
        }

        $this->end(true, '添加进货单成功');
    }

    /**
     * 添加损益单处理方法
     */
    public function edit_income_receipts_action(){
        $obj_edit_income_receipts = vmc::singleton('store_stock_edit_income_receipts');
        $this->begin();
        $addResult = $obj_edit_income_receipts->add_income_receipts($_POST['income_receipts']);
        if($addResult === false){

            $this->end(false, $obj_edit_income_receipts->get_msg());
        }

        $this->end(true, '添加损益单成功');
    }

    /**
     * 库存管理里添加/修改进货单时查询商品方法
     */
    public function ajax_search_goods(){
        $search_type = utils::_filter_input($_POST['search_type']);
        $search_keyword = utils::_filter_input($_POST['search_keyword']);
        $store_id = utils::_filter_input($_POST['store_id']);
        if(empty($search_keyword) === true){

            $this->splash('error', '', '请输入查询关键字');
        }

        //构造查询条件
        $productsCondition = [];
        if($search_type === 'barcode' || empty($search_type) === true || in_array($search_type, ['barcode', 'goods_bn', 'goods_name']) === false){
            $productsCondition['barcode'] = $search_keyword;
        }else if($search_type === 'goods_bn'){
            $productsCondition['bn|has'] = $search_keyword;
        }else{
            $productsCondition['name|has'] = $search_keyword;
        }
        if(is_numeric($store_id) && $store_id > 0){
            $productsCondition['store_id'] = $store_id;
        }

        $obj_get_store_products = new store_goods_get_products();

        //查询店铺货品
        $store_products = $obj_get_store_products->get_store_products('*', $productsCondition);

        $productCount = count($store_products);
        if($productCount > 0){

            //获取店铺货品的商品id
            $gidArray = [];
            $tempProducts = [];
            foreach ($store_products as $key => $store_product) {
                $store_product['stock'] = app::get('b2c')->model('stock') ->getRow("*" ,array('sku_bn' => $store_product['bn']));
                $store_product['productJson'] = json_encode($store_product);
                $tempProducts[$store_product['product_id']] = $store_product;
                $gidArray[] = $store_product['goods_id'];
            }
            $store_products = $tempProducts;

            //查询所有商品的默认图片
            $mdl_goods = app::get('b2c')->model('goods');
            $goodsImages = $mdl_goods->getList('goods_id, image_default_id', ['goods_id' => $gidArray]);

            $this->pagedata['goodsImages'] = utils::array_change_key($goodsImages, 'goods_id');
            $this->pagedata['productInfos'] = $store_products;
        }
        $this->pagedata['productCount'] = $productCount;

        $this->display('admin/stock/goods_item.html');
    }
}
