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
class vmcconnect_services_stock_update extends vmcconnect_services_base {

    public function exec($data) {
        $params = array('skus' => $data);
        $task_name = 'stock_update';
        $publish_name = 'vmcconnect_tasks_' . $task_name;

        //判断是否有勾选
        $task_name = str_replace('_', '.', $task_name);
        $task_row = $this->_get_task_rows($task_name);

        $hook_conf = app::get('vmcconnect')->getConf('vmcconnect-hook-conf');
        $hook_conf = unserialize($hook_conf);

        //执行判断：①满足存在缓存中  ②满足存在数据库中
        if ($hook_conf && $hook_conf['hook_items'] && is_array($hook_conf['hook_items']) && in_array($task_name, $hook_conf['hook_items']) && $task_row){
            //才可执行队列任务
            $task = $this->save_task($task_name, $params);
            if (!$task) return false;

            $task_id = $task['task_id'];
            system_queue::instance()->publish($publish_name, $publish_name, array('task_id' => $task['task_id']));
        }
        return true;

    }

}
