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

class vmcconnect_object_api_input_jd_category extends vmcconnect_object_api_input_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'category';
    }

    // category.write.add - 添加分类 
    public function write_add() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // category.write.delete - 删除分类 
    public function write_delete() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // category.write.update - 更新分类 
    public function write_update() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // category.read.getAll - 获取所有类目信息 
    public function read_getAll() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // category.read.getFront - 获取前台展示的分类 
    public function read_getFront() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // category.read.findById - 获取单个类目信息 
    public function read_findById() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // category.read.findByPId - 查找子类目列表 
    public function read_findByPId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
