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


class preselling_ctl_mobile_product extends b2c_mfrontpage
{
    public $title = '商品详情';
    public function __construct($app)
    {
        parent::__construct($app);

        $this->goods_stage =
            vmc::singleton('b2c_goods_stage');
        $this->verify_member();
        if ($this->app->member_id = vmc::singleton('b2c_user_object')->get_member_id()) {
            $this->goods_stage->set_member($this->app->member_id);
        }
    }

    public function index()
    {
        //获取参数 货品ID
        $params = $this->_request->get_params();
        $time = time();
        $mdl_activity = $this->app->model('activity');
        if(!$activity = $mdl_activity->getRow('*',array('activity_id'=>$params[0]))) {
            vmc::singleton('mobile_router')->http_status(404);
        };

        if(!in_array($mdl_activity->get_member_lv($this->app->member_id),explode(',',$activity['member_lv_ids']))) {
            $this->splash('error','','你暂时无法参与此活动');
        }
        if(!$params[1]) {
            $product_id = $activity['product_id'];
        }else{
            $product_id = $params[1];
        }
        $product_ids = array_keys($activity['conditions']);

        if(!in_array($product_id,$product_ids)) {
            $this->splash('error','','未知活动商品');
        }
        $data_detail = $this->detail($product_id,$product_ids, $msg); //引用传递
        if (!$data_detail) {
            vmc::singleton('mobile_router')->http_status(404);
        }
        $current_product = $activity['conditions'][$product_id];
        $obj_goods_stock = vmc::singleton('b2c_goods_stock');
        $obj_goods_stock->is_available_stock($data_detail['product']['bn'],1,$stock);
        if($current_product['restrict_number'] > 0) {
            $mdl_orders = $this->app->model('orders');
            $buy_number = $mdl_orders->sum_product(array(
                'product_id'=>$product_id,
                'activity_id'=>$activity['activity_id'],
                'member_id'=>$this->app->member_id,
                'status|noequal' => '3',
            ));
            $current_product['restrict_number'] = $current_product['restrict_number']-$buy_number;
            if($current_product['restrict_number'] > $stock) {
                $current_product['restrict_number'] = $stock;
            }
        }else{
            $current_product['restrict_number'] = $stock;
        }


        $this->pagedata['current_product'] = $current_product;
        $this->pagedata['data_detail'] = $data_detail;
        $activity['surplus_balance_time'] = $activity['balance_endtime']-$time;
        $activity['surplus_deposit_time'] = $activity['deposit_endtime']-$time;
        $this->pagedata['activity'] = $activity;
        $this->pagedata['current_time'] = $time;
        //设置模板
        if ($data_detail['goods_setting']['mobile_template']) {
            //设置模板页
            $this->set_tmpl_file($data_detail['goods_setting']['mobile_template']);
        }
        $this->page('mobile/product/index.html');
    }



    /**
     * 获得商品及默认货品详情.
     *
     * @param $pkey string|int g+商品id 或  货品id
     * @param &$msg string 错误反馈
     */
    private function detail($pkey,$product_ids,&$msg)
    {
        if (!$pkey) {
            $msg = '缺少参数';

            return false;
        }
        $this->mdl_products = app::get('b2c')->model('products');
        $this->mdl_goods = app::get('b2c')->model('goods');
        if (substr($pkey, 0, 1) == 'g') {
            //传入了商品ID
            $data_detail = $this->mdl_goods->dump(substr($pkey, 1), '*', 'default');
            foreach ($data_detail['product'] as $key => $product) {
                if ($product['is_default'] == 'true') {
                    $current_product = $product;
                    break;
                }
            }
            if (!isset($current_product)) {
                $current_product = current($data_detail['product']);
            }
        } else {
            //任务传入了货品ID
            $product = $this->mdl_products->dump($pkey);
            $data_detail = $this->mdl_goods->dump($product['goods_id'], '*', 'default');
            $current_product = $data_detail['product'][$pkey];
        }
        
        if (!$data_detail || !$current_product) {
            $msg = 'NOT FOUND';

            return false;
        }
        //获得扩展属性
        $mdl_gtype = app::get('b2c')->model('goods_type');
        $gtype_obj = $mdl_gtype->dump($data_detail['type']['type_id']);
        foreach ($gtype_obj['props'] as $key => $value) {
            if (!$data_detail['props']['p_'.$key]['value']) {
                continue;
            }
            $prop = array(
                'label' => $value['name'],
            );
            if ($value['type'] == 'select') {
                $prop['value'] = $value['options'][$data_detail['props']['p_'.$key]['value']];
            } else {
                $prop['value'] = $data_detail['props']['p_'.$key]['value'];
            }
            $props[] = $prop;
        }
        $data_detail['props'] = $props;

        $current_product_sprc_desc = explode(':::', $current_product['spec_desc']);
        $spec_options = false;

        if ($data_detail['spec_desc'] && count($data_detail['spec_desc']) > 0) {
            foreach ($data_detail['spec_desc']['v'] as $key => $value) {
                unset($data_detail['spec_desc']['v'][$key]);
                foreach (explode(',', $value) as $value) {
                    $data_detail['spec_desc']['v'][$key][$value] = array(
                        'label' => $value,
                    );
                }
            }
        }

        foreach ($data_detail['product'] as $key => $product) {
            /*规格选项计算 BEGIN*/
            $spec_desc_arr = explode(':::', $product['spec_desc']);
            $diff_spec = array_diff_assoc($spec_desc_arr, $current_product_sprc_desc);
            if (count($diff_spec) == 1) {
                if(in_array($product['product_id'],$product_ids)) {
                    $data_detail['spec_desc']['v'][key($diff_spec)][current($diff_spec)]['product_id'] = $product['product_id'];
                    $data_detail['spec_desc']['v'][key($diff_spec)][current($diff_spec)]['sku_bn'] = $product['bn'];
                    $data_detail['spec_desc']['v'][key($diff_spec)][current($diff_spec)]['marketable'] = $product['marketable'];
                    if ($data_detail['goods_setting'] && $data_detail['goods_setting']['spec_info_vimage'] && $data_detail['goods_setting']['spec_info_vimage'] == $data_detail['spec_desc']['t'][key($diff_spec)]) {
                        $data_detail['spec_desc']['v'][key($diff_spec)][current($diff_spec)]['p_image_id'] = $product['image_id'];
                    }
                }
            }
            if (count($diff_spec) == 0) {
                foreach ($current_product_sprc_desc as $key => $value) {
                    if(in_array($product['product_id'],$product_ids)) {
                        $data_detail['spec_desc']['v'][$key][$value]['product_id'] = $product['product_id'];
                        $data_detail['spec_desc']['v'][$key][$value]['sku_bn'] = $product['bn'];
                        $data_detail['spec_desc']['v'][$key][$value]['marketable'] = $product['marketable'];
                        $data_detail['spec_desc']['v'][$key][$value]['current'] = 'true';
                        if ($data_detail['goods_setting'] && $data_detail['goods_setting']['spec_info_vimage'] && $data_detail['goods_setting']['spec_info_vimage'] == $data_detail['spec_desc']['t'][$key]) {
                            $data_detail['spec_desc']['v'][$key][$value]['p_image_id'] = $product['image_id'];
                        }
                    }
                }
            }
            /*规格选项计算 END*/
        }
        /*foreach($data_detail['product'] as $key => $detail_product) {
            if(!in_array($detail_product['product_id'],$product_ids)) {
                unset($data_detail['product'][$key]);
            }
        }*/
        //数据格式化
        foreach ($data_detail['spec_desc']['v'] as $key => $value) {
            $data_detail['spec_desc']['v'][$key] = array_values($value);
        }
        //只给当前货品数据
        $data_detail['product'] = $current_product;

        //默认图
        $product_image_id = $data_detail['product']['image_id'];
        if ($data_detail['product'] && $product_image_id) {
            foreach ($data_detail['images'] as $k => $i) {
                if ($i['image_id'] == $product_image_id) {
                    unset($data_detail['images'][$k]);
                }
            }
            array_unshift($data_detail['images'], array('image_id' => $product_image_id));
            $data_detail['image_default_id'] = $product_image_id;
        } else {
            $data_detail['images'] = array_values($data_detail['images']);
        }


        //商品满意度星级
        $goods_avg_mark = app::get('b2c')->model('goods_mark')->avg_mark($data_detail['goods_id']);
        $data_detail['mark_star'] = isset($goods_avg_mark[$data_detail['goods_id']]) ? $goods_avg_mark[$data_detail['goods_id']]['num'] : 5;
        //商品详情数据格式化服务扩展
        foreach( vmc::servicelist('b2c.goods.stage.detail.pre_return') as $services ) {
            if ( is_object($services) ) {
                if ( method_exists($services, 'exec') ) {
                    $services->exec($data_detail); //&
                }
            }
        }

        return $data_detail;
    }


}
