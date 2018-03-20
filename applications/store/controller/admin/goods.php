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


class store_ctl_admin_goods extends store_ctl_admin_controller
{
    public function __construct($app) {
        parent::__construct($app);
    }

    /**
     * 店铺商品列表首页
     */
    public function index()
    {
        $finder_params['finder_extra_view'] = array(
            array(
                'app'=>'store',
                'view'=>'/admin/finder/top_filter.html',
                'extra_pagedata' => $this->can_cashier_store_ids,
            )
        );
        $finder_params['title'] = ('店铺商品列表');
        $group[] = array(
            'label' => ('打印价签') ,
            'data-submit' => 'index.php?app=store&ctl=admin_goods&act=print_price',
            'data-target' => '_ACTION_MODAL_',
        );
        $group[] = array(
            'label' => '_SPLIT_'
        );
        if ($this->has_permission('collections')) {
            $group[] = array(
                'label' => ('将选中项加入集合') ,
                'data-submit' => 'index.php?app=b2c&ctl=admin_goods&act=batch_edit&p[0]=collections',
                'data-target' => '_ACTION_MODAL_',
            );
            $group[] = array(
                'label' => ('将当前结果加入集合') ,
                'data-submit-result' => 'index.php?app=b2c&ctl=admin_goods&act=batch_edit&p[0]=collections',
                'data-target' => '_ACTION_MODAL_',
            );
        }
        $group[] = array(
            'label' => ('在门店上架销售') ,
            'data-submit' => 'index.php?app=store&ctl=admin_goods&act=batch_marketable',
        );
        $group[] = array(
            'label' => ('在门店下架') ,
            'data-submit' => 'index.php?app=store&ctl=admin_goods&act=batch_unmarketable',
        );
        $group[] = array(
            'label' => ('将库存批量设置为10000') ,
            'data-submit' => 'index.php?app=store&ctl=admin_goods&act=batch_stockfix',
        );
        $finder_params['actions'][] = array(
            'label' => ('批量操作') ,
            'group' => $group,
        );

        $finder_params['use_buildin_export'] = true;
        $finder_params['use_buildin_set_tag'] = true;
        $finder_params['use_buildin_filter'] = true;

        $this->finder('store_mdl_storegoods', $finder_params);
    }



    /**
     * 打印价签
     */
    public function print_price(){
        $stock_id = $_POST['goods_id'];
        if(empty($stock_id)){
            exit('请选择商品');
        }
        $this->pagedata['total'] = count($stock_id);
        $this->pagedata['filter'] = htmlspecialchars(serialize($_POST));
        $this->display('admin/goods/print/products.html');
    }

    public function do_print(){
        $params = $_POST;
        $tpl = array(
            '1' =>'admin/goods/print/normal.html',
            '2' =>'admin/goods/print/across.html',
        );
        $limit = array(
            '1' =>12,
            '2' => 4
        );
        $filter = unserialize(trim($params['filter']));
        $products = app::get('b2c') ->model('products') ->getList('*' ,$filter);
        $goods = app::get('b2c') ->model('goods') ->getList('goods_id,address,seo_info,name' ,$filter);

        $goods = utils::array_change_key($goods ,'goods_id');
        $res = array();
        foreach($products as $k =>$v){
            $v['address'] = $goods[$v['goods_id']]['address'];
            $v['name'] = $goods[$v['goods_id']]['name'];
            $key = (int)floor($k/$limit[$params['type']]);
            $res[$key][] = $v;
        }
        unset($products ,$v );
        $this ->pagedata['res'] = $res;
        $this ->display($tpl[$params['type']]);
    }

    public function batch_marketable(){
        $this->begin();
        if(!isset($_POST['goods_id']) ||empty($_POST['goods_id'])){
            $this->end(false);
        }
        $mdl_store_rel_goods = app::get('store')->model('relation_goods');
        $this->end($mdl_store_rel_goods->update(array('store_enable'=>'1'),array('goods_id'=>$_POST['goods_id'])));
    }

    public function batch_unmarketable(){
        $this->begin();
        if(!isset($_POST['goods_id']) ||empty($_POST['goods_id'])){
            $this->end(false);
        }
        $mdl_store_rel_goods = app::get('store')->model('relation_goods');
        $this->end($mdl_store_rel_goods->update(array('store_enable'=>'0'),array('goods_id'=>$_POST['goods_id'])));
    }

    public function batch_stockfix(){
        $this->begin();
        if(!isset($_POST['goods_id']) ||empty($_POST['goods_id'])){
            $this->end(false);
        }
        $mdl_stock = app::get('b2c')->model('stock');
        $mdl_product = app::get('b2c')->model('products');
        $product_list = $mdl_product->getList('bn',array('goods_id'=>$_POST['goods_id']));
        $sku_bn = array_keys(utils::array_change_key($product_list, 'bn'));
        $this->end($mdl_stock->update(array('quantity'=>'10000'),array('sku_bn'=>$sku_bn)));
    }


    public function ajax_get_goods_store(){
        $store_id = $_POST['store_id'];
        $this ->pagedata['store_list'] = app::get('store')->model('store')->getList('*',array('store_id' =>$store_id));
        $this->display('admin/goods/detail/ajax_store.html');
    }

}
