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


class vshop_ctl_admin_profit extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        // header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index($page = 1, $keyword = false)
    {

        $pagelimit = 20;
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_products = app::get('b2c')->model('products');
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

        $mdl_relprofit = $this->app->model('relprofit');
        $rel_items = $mdl_relprofit->getList('*', $product_filter, $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_relprofit->count($product_filter);

        $product_ids = array_keys(utils::array_change_key($rel_items, 'product_id'));
        $products = $mdl_products->getList('*', array('product_id' => $product_ids));

        $this->pagedata['rel_items'] = utils::array_change_key($rel_items, 'goods_id', true);
        $this->pagedata['products'] = utils::array_change_key($products, 'product_id');
        $this->pagedata['items_count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=vshop&ctl=admin_relprofit&act=index&p[0]='.time().'&in_page=true',
            'token' => time(),
        );
        if ($_GET['in_page']) {
            $this->display('admin/profit/items.html');
        } else {
            $this->page('admin/profit/index.html');
        }
    }
    public function update_products($action = 'save')
    {
        $this->begin();
        $product_ids = $_POST['product_id'];
        $products = app::get('b2c')->model('products')->getList('*', array('product_id' => $product_ids));
        $mdl_relprofit = $this->app->model('relprofit');
        if (!$products) {
            $this->end(false);
        }
        foreach ($products as $key => $product) {
            $data = array(
                'goods_id' => $product['goods_id'],
                'product_id' => $product['product_id'],
            );
            if (!$mdl_relprofit->{$action}($data)) {
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
                'share' => $data[2],
            );
            $import_data[$row['bn']] = $row;
            $bn_arr[] = $data[0];
        }
        $products = app::get('b2c')->model('products')->getList('*', array('bn' => $bn_arr));
        if (!$products) {
            $this->end(false);
        }
        $mdl_relprofit = $this->app->model('relprofit');
        foreach ($products as $key => $product) {
            $data = array(
                'goods_id' => $product['goods_id'],
                'product_id' => $product['product_id'],
                'share' => $import_data[$product['bn']]['share'],
            );
            $exsit_row = $mdl_relprofit->getRow('id',array('product_id'=>$product['product_id']));
            if($exsit_row){
                $data['id'] = $exsit_row['id'];
            }else{

            }
            if (!$mdl_relprofit->save($data)) {
                $this->end(false);
            }
        }
        $this->end(true);
    }

    public function update_item()
    {
        $this->begin();
        $mdl_relprofit = $this->app->model('relprofit');
        $this->end($mdl_relprofit->save($_POST));
    }
}
