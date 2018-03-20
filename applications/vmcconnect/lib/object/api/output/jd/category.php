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

class vmcconnect_object_api_output_jd_category extends vmcconnect_object_api_output_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'category';
    }
    
    // category.write.add - 添加分类 
    public function write_add() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $_result =($params && isset($params['result'])) ? $params['result'] : array();
        
        $res = array();
        $res['sellerCatAddResponse'] = array(
            'code' => $params['ocde'],
            'create_time' => ($_result && isset($_result['create_time'])) ? $_result['create_time'] : null,
            'cid' => ($_result && isset($_result['category_id'])) ? $_result['category_id'] : null,
        );
        
        return $res;
    }

    // category.write.delete - 删除分类 
    public function write_delete() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $_result =($params && isset($params['result'])) ? $params['result'] : array();
        
        $res = array();
        $res['sellerCatDeleteResponse'] = array(
            'code' => $params['ocde'],
            'modified' => ($_result && isset($_result['modified'])) ? $_result['modified'] : null,
            'cid' => ($_result && isset($_result['category_id'])) ? $_result['category_id'] : null,
        );
        
        return $res;
    }

    // category.write.update - 更新分类 
    public function write_update() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $_result =($params && isset($params['result'])) ? $params['result'] : array();
        
        $res = array();
        $res['sellerCatUpdateResponse'] = array(
            'code' => $params['ocde'],
            'modified' => ($_result && isset($_result['modified'])) ? $_result['modified'] : null,
            'cid' => ($_result && isset($_result['category_id'])) ? $_result['category_id'] : null,
        );
        
        return $res;
    }

    // category.read.getAll - 获取所有类目信息 
    public function read_getAll() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $item_cats  = array();
        $_tmp =($params && isset($params['result'])) ? $params['result'] : array();
        if($_tmp){
            foreach ($_tmp as $_v){
                $item_cats[] = array(
                    'item_cat' => $_v,
                );
            }
        }
        
        $res = array();
        $res['categorySearchResponse'] = array(
            'code' => $params['ocde'],
            'total' => count($item_cats),
            'item_cats' => $item_cats,
        );
        
        return $res;
    }

    // category.read.getFront - 获取前台展示的分类 
    public function read_getFront() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $shopCats  = array();
        $_tmp =($params && isset($params['result'])) ? $params['result'] : array();
        if($_tmp){
            foreach ($_tmp as $_v){
                $item_cats[] = array(
                    'shopCat' => $_v,
                );
            }
        }
        
        $res = array();
        $res['sellerCatsGetResponse'] = array(
            'code' => $params['ocde'],
            'total' => count($item_cats),
            'shopCats' => $item_cats,
        );
        
        return $res;
    }

    // category.read.findById - 获取单个类目信息 
    public function read_findById() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $res = array(
            'code' => $params['ocde'],
            'category' => ($params && isset($params['result'])) ? $params['result'] : array(),
        );
        
        return $res;
    }

    // category.read.findByPId - 查找子类目列表 
    public function read_findByPId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        
        $shopCats  = array();
        $_tmp =($params && isset($params['result'])) ? $params['result'] : array();
        if($_tmp){
            foreach ($_tmp as $_v){
                $item_cats[] = array(
                    'category' => $_v,
                );
            }
        }
        
        $res = array(
            'code' => $params['ocde'],
            'total' => count($item_cats),
            'categories' => $item_cats,
        );
        
        return $res;
    }

}
