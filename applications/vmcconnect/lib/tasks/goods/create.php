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
class vmcconnect_tasks_goods_create extends vmcconnect_tasks_base implements base_interface_task{
    
    public function exec($params = null) {
        return $this->_exec_task($params);
        // 移除 挂载 products
        $task_id = isset($data['task_id']) && is_numeric($data['task_id']) ? $data['task_id'] : 0;
        if (!$task_id) return true;
        $task = $this->get_task($task_id);
        if (!$task) return true;
        $goods = $task['task_data']['data'];
        $goods_id = $goods['goods_id'];
        $products = app::get('b2c')->model('products')->getList('*', array('goods_id' => $goods_id));
        $task['task_data']['data']['products'] = $products;
        return $this->_exec_task($data, $task);
    }

}
