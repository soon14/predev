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

class vmcconnect_object_api_output_jd_goodattrs extends vmcconnect_object_api_output_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'goodattrs';
    }

    // goodattrs.read.get - 获取商品类型列表 
    public function read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $item_cats  = array();
        
        $_tmp =($params && isset($params['result'])) ? $params['result'] : array();
        if($_tmp){
            foreach ($_tmp as $_v){
                $item_cats[] = array(
                    'item_goodattr' => $_v,
                );
            }
        }
        
        $res = array();
        $res['goodattrsSearchResponse'] = array(
            'code' => $params['ocde'],
            'total' => count($item_cats),
            'item_goodattrs' => $item_cats,
        );
        
        return $res;
    }

    // goodattrs.read.valuesByAttrId - 获取商品类型属性 
    public function read_valuesByAttrId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $res = array(
            'code' => $params['ocde'],
            'goodattr' => ($params && isset($params['result'])) ? $params['result'] : array(),
        );
        
        return $params;
    }

}
