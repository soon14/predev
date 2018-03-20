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

class vmcconnect_object_api_output_jd_goods extends vmcconnect_object_api_output_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'goods';
    }

    // goods.write.add - 新增商品 
    public function write_add() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $_result =($params && isset($params['result'])) ? $params['result'] : array();
        
        $res = array();
        $res['wareAddResponse'] = array(
            'code' => $params['ocde'],
            'created' => ($_result && isset($_result['create_time'])) ? $_result['create_time'] : null,
            'ware_id' => ($_result && isset($_result['goods_id'])) ? $_result['goods_id'] : null,
            'skus' => ($_result && isset($_result['skus'])) ? $_result['skus'] : null,
        );
        
        return $res;
    }

    // goods.write.update - 修改商品 
    public function write_update() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $_result =($params && isset($params['result'])) ? $params['result'] : array();
        
        $res = array();
        $res['wareAddResponse'] = array(
            'code' => $params['ocde'],
            'modified' => ($_result && isset($_result['modified'])) ? $_result['modified'] : null,
            'ware_id' => ($_result && isset($_result['goods_id'])) ? $_result['goods_id'] : null,
            'skus' => ($_result && isset($_result['skus'])) ? $_result['skus'] : null,
        );
        
        return $res;
    }

    // goods.write.upOrDown - 商品上下架 
    public function write_upOrDown() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // goods.read.byId - 获取单个商品 
    public function read_byId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // goods.sku.read.findSkuById - 获取单个SKU 
    public function sku_read_findSkuById() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // goods.sku.stock.read.find - 获取sku库存信息 
    public function sku_stock_read_find() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // goods.sku.stock.write.update - 设置sku库存 
    public function sku_stock_write_update() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
