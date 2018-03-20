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

class supplier_ctl_admin_relgoods extends desktop_controller
{

    public function __construct($app)
    {
        parent::__construct($app);
        $this->supplier_id = $_GET['supplier_id'];
        if(!$this->supplier_id){
            die('未知供应商');
        }
    }

    public function index($page = 1, $keyword = false)
    {
        $pagelimit = 20;
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_products = app::get('b2c')->model('products');

        $product_filter = array('supplier_id'=>$this->supplier_id);
        if ($keyword) {
            $goods_range = $mdl_goods->getList('goods_id', array('keyword' => $keyword));
            if (!$goods_range) {
                $goods_range = $mdl_goods->getList('goods_id', array('gid|has' => $keyword));
            }
            if (!$goods_range) {
                $goods_range = $mdl_goods->getList('goods_id', array('product_filter' => array('bn|has' => $keyword)));
            }
            $goods_range = $goods_range ? $goods_range : array();
            $goods_range_gids = array_keys(utils::array_change_key($goods_range, 'goods_id'));
            $product_filter['goods_id'] = $goods_range_gids;
        }

        $mdl_relgoods = $this->app->model('relgoods');
        $rel_items = $mdl_relgoods->getList('*', $product_filter, $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_relgoods->count($product_filter);

        $product_ids = array_keys(utils::array_change_key($rel_items, 'product_id'));
        $products = $mdl_products->getList('*', array('product_id' => $product_ids));

        $this->pagedata['rel_items'] = utils::array_change_key($rel_items, 'goods_id', true);
        $this->pagedata['products'] = utils::array_change_key($products, 'product_id');
        $this->pagedata['items_count'] = $count;
        $this->pagedata['supplier'] = $this->app->model('supplier')->dump($this->supplier_id);
        $this->pagedata['page'] = $page;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=supplier&ctl=admin_relgoods&act=index&p[0]='.time().'&in_page=true&supplier_id='.$this->supplier_id ,
            'token' => time(),
        );
        if ($_GET['in_page']) {
            $this->display('/admin/relgoods/items.html');
        } else {
            $this->page('/admin/relgoods/index.html');
        }
    }
    public function update_products($action = 'save')
    {
        $this->begin();

        $product_ids = $_POST['product_id'];
        $products = app::get('b2c')->model('products')->getList('*', array('product_id' => $product_ids));
        $mdl_relgoods = $this->app->model('relgoods');
        if (!$products) {
            $this->end(false);
        }
        foreach ($products as $key => $product) {
            $data = array(
                'supplier_id'=>$this->supplier_id,
                'goods_id' => $product['goods_id'],
                'product_id' => $product['product_id'],
            );
            if($action!='delete' && $mdl_relgoods->count(array('product_id'=>$product['product_id']))){
                $this->end(false,$product['name'].'('.$product['bn'].')已绑定供应商');
            }
            if (!$mdl_relgoods->{$action}($data)) {
                $this->end(false);
            }
        }
        $this->end(true);
    }

    public function update_products_bycsv($plan_id)
    {
        $this->begin();
        $csv_file = $_FILES['files']['tmp_name'][0];
        $file = fopen($csv_file, 'r');
        while ($data = fgetcsv($file)) {
            $row = array(
                'bn' => $data[0],
                'purchase_price' => $data[2],
            );
            $import_data[$row['bn']] = $row;
            $bn_arr[] = $data[0];
        }
        $products = app::get('b2c')->model('products')->getList('*', array('bn' => $bn_arr));
        if (!$products) {
            $this->end(false);
        }
        $mdl_relgoods = $this->app->model('relgoods');
        foreach ($products as $key => $product) {
            $data = array(
                'supplier_id' => $this->supplier_id,
                'goods_id' => $product['goods_id'],
                'product_id' => $product['product_id'],
                'purchase_price' => $import_data[$product['bn']]['purchase_price'],
            );
            $exsit_row = $mdl_relgoods->getRow('id',array('supplier_id'=>$this->supplier_id,'product_id'=>$product['product_id']));
            if($exsit_row){
                $data['id'] = $exsit_row['id'];
            }else{
                if($mdl_relgoods->count(array('product_id'=>$product['product_id']))){
                    $this->end(false,$product['name'].'('.$product['bn'].')已绑定供应商');
                }
            }
            if (!$mdl_relgoods->save($data)) {
                $this->end(false);
            }
        }
        $this->end(true);
    }

    public function update_item()
    {
        $this->begin();
        $mdl_relgoods = $this->app->model('relgoods');
        $_POST['supplier_id'] = $this->supplier_id;
        $this->end($mdl_relgoods->save($_POST));
    }
}
