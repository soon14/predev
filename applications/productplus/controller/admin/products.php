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

class productplus_ctl_admin_products extends desktop_controller
{
    public function index()
    {

        $group[] = array(
            'label' => ('货品上、下架') ,
            'data-submit' => 'index.php?app=productplus&ctl=admin_products&act=batch_edit&p[0]=marketable',
            'data-target' => '_ACTION_MODAL_',
        );
        if ($this->has_permission('product_batch_edit')) {
            $custom_actions[] = array(
                'label' => ('批量操作') ,
                'group' => $group,
            );
        }

        $this->finder('b2c_mdl_products', array(
            'title' => ('货品列表'),
            'use_buildin_filter'=>true,
            'use_buildin_export'=>$this ->has_permission('productplus_export'),
            'use_buildin_recycle' => $this ->has_permission('productplus_delete'),
            'use_buildin_set_tag' => $this->has_permission('productplus_buildin_set_tag'),
            'actions' => $custom_actions,
        ));
    }

    public function edit($product_id)
    {
        $mdl_products = app::get('b2c')->model('products');
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_extend = $this->app->model('extend');
        $product = $mdl_products->dump($product_id);
        $extend = $mdl_extend->dump(array('product_id' => $product_id), '*', 'default');
        $goods = $mdl_goods->dump($product['goods_id'], '*', 'default');
        $this->pagedata['product'] = $product;
        $this->pagedata['goods'] = $goods;
        $this->pagedata['extend'] = $extend;
        $this->page('admin/product/edit.html');
    }

    public function save()
    {
        $this->begin();
        $mdl_products = app::get('b2c')->model('products');
        $mdl_extend = $this->app->model('extend');
        $product = $_POST['product'];
        $extend = $_POST['extend'];
        if (!empty($product) && $product['product_id']) {
            if(!is_numeric($product['mktprice'])){
                unset($product['mktprice']);
            }
            if(empty($product['bn'])){
                $this->end(false,'货号不能为空');
            }
            if ($mdl_products->count(array(
                'bn' => $product['bn'],
                'goods_id|notin' => $product['goods_id'],
            )) > 0) {
                $this->end(false, '系统存在重复货号'.$product['bn']);
            }
            $this->end($mdl_products->save($product));
        } elseif (!empty($extend) && $extend['product_id']) {
            if($extend['images']){
                foreach ($extend['images'] as $key=>$item) {
                    $extend['images'][$key] = array(
                        'target_id'=>$extend['product_id'],
                        'image_id' =>$item
                    );
                }
            }
            $this->end($mdl_extend->save($extend));
        }
    }

    /**
     * 批量编辑.
     */
    public function batch_edit($type = '')
    {
        $filter = $_POST;
        if(empty($filter)){
            echo('<div class="alert alert-warning">您正在操作全部商品!</div>');
            exit;
        }
        switch ($type) {
            case 'marketable':
                break;
        }
        $this->pagedata['filter'] = htmlspecialchars(serialize($filter));
        $this->display('admin/product/batchedit/'.$type.'.html');
    }

    public function batch_save(){
        $this->begin();
        $params = $_POST;
        $type = $params['type'];
        $filter = unserialize(trim($params['filter']));
        $mdl_products = app::get('b2c')->model('products');
        switch ($type) {
            case 'marketable':
                if(!$mdl_products->update($params['goods'],$filter) ){
                    $this->end(false,'保存失败');
                }
                $this->end(true,'保存成功');
                break;
        }

    }

    public function qrcode($pid)
    {
        $mobile_url = vmc::singleton('mobile_router')->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'mobile_product',
            'act' => 'index',
            'args' => array($pid),
            'full' => 1,
            ));
            // image  response
            ectools_qrcode_QRcode::png($mobile_url, false, 0, 7, 10);
    }
}
