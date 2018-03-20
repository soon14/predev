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


class store_finder_store_goods
{
    private $render;

    public function __construct($app) {
        $this->app = $app;
        $this->render = $this->app->render();
    }

    /**
     * 商品下拉详情
     *
     * @param int $gid 商品id
     *
     * @return mixed
     */
    public function detail_products($gid)
    {

        $mdl_products = app::get('b2c')->model('products');

        $products = $mdl_products->getList('*', array('goods_id' => $gid));

        $mdl_stock = app::get('b2c')->model('stock');

        $sku_bn = array_keys(utils::array_change_key($products, 'bn'));
        $stock_list = $mdl_stock->getList('*', array('sku_bn' => $sku_bn));
        $stock_list = utils::array_change_key($stock_list, 'sku_bn');
        $this->render->pagedata['data_detail'] = app::get('b2c')->model('goods')->dump($gid, '*', 'default');
        $this->render->pagedata['products'] = $products;
        $this->render->pagedata['stock_list'] = $stock_list;
        $this->render->pagedata['gpromotion_openapi'] = vmc::openapi_url('openapi.goods', 'promotion', array('goods_id' => $gid));
        $this->render->pagedata['store_goods'] = app::get('store') ->model('relation_goods')->getRow('*' ,array('goods_id'=>$gid));
        return $this->render->fetch('admin/goods/detail/detail.html', 'store');
    }
}
