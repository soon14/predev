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

class vmcconnect_tasks_base {

    protected $app, $hook_name, $tb_prefix, $hook_status = null;
    protected $_pre_method = 'vmc.';

    public function __construct($app) {
        $this->app = app::get('vmcconnect');
        $this->tb_prefix = vmc::database()->prefix;
        $this->get_hook_status();
    }

    public function get_hook_status() {
        if (!is_null($this->hook_status)) return $this->hook_status;
        $get_conf = $this->app->getConf('vmcconnect-hook-conf');
        $_conf = $get_conf ? unserialize($get_conf) : null;
        $this->hook_status = ($_conf && isset($_conf['hook_enable']) && $_conf['hook_enable'] == 'true') ? true : false;
        unset($get_conf, $_conf);
        return $this->hook_status;
    }

    public function get_hook_item_status($name) {
        $name = trim($name);
        if (!$name) return false;

        $get_conf = $this->app->getConf('vmcconnect-hook-conf');
        $_conf = $get_conf ? unserialize($get_conf) : null;


        $status = ($_conf && isset($_conf['hook_items']) && in_array($name, $_conf['hook_items'])) ? true : false;
        return $status;
    }

    protected function get_task($task_id) {
        if (!$task_id) return false;
        $task = $this->app->model('hooktasks')->dump($task_id);
        if (!$task) return false;
        $task['task_data'] = unserialize($task['task_data']);
        return $task;
    }

    protected function _chek_hook($name) {
        $_obj_map = vmc::singleton('vmcconnect_object_hook_map');
        $sys_allow_items = $_obj_map->sys_allow_hook_items();
        if (!$sys_allow_items) return false;
        return (($sys_allow_items && in_array($name, $sys_allow_items)) ? true : false);
    }

    function run_task(&$task) {
        if (!$task) return false;
        if ($task['task_status'] > 9) return $task;
        $task_id = $task['task_id'];
        $this->hook_name = implode('.', explode('_', trim($task['task_type'])));
        if (!$this->get_hook_item_status($this->hook_name)) return false;

        $get_task_rows = $this->_get_task_rows($this->hook_name);

        if (!$get_task_rows) {
            return true;
        }
        $all_send = true;
        $_sends = array();
        foreach ($get_task_rows as $_key => $_row) {
            $_out_datas = $this->_send($_row, $task);
            if (!$_out_datas || $_out_datas['error']) {
                $all_send = false;
            } else {
                $this->save_send($_row, $task, $_out_datas, 'succ');
            }
        }
        return $all_send;
    }

    protected function _get_task_rows($hook_name) {
        $hook_name = trim($hook_name);
        if (!$hook_name) return false;

        $_sql = "select app.app_id, app.app_secret, hook.hook_id, hook.hook_url, hook.hook_addon, hook.hook_msg_tpl "
                . " from " . $this->tb_prefix . "vmcconnect_apps app "
                . " join " . $this->tb_prefix . "vmcconnect_app_items item on app.app_id = item.app_id and item.app_allow_type = 'hook' and app_item = '" . $hook_name . "' "
                . " join " . $this->tb_prefix . "vmcconnect_hooks hook on hook.app_id = app.app_id "
                . " join " . $this->tb_prefix . "vmcconnect_hook_items hook_item on hook_item.hook_id = hook.hook_id and hook_item.hook_item = '" . $hook_name . "' "
                . " where app_status = 1 "
                . " and app.app_hook_status = 1 "
                . " and hook.hook_status = 1 ";
        $rows = vmc::database()->exec($_sql);
        $res = ($rows && isset($rows['rs'])) ? $rows['rs'] : null;
        return $res;
    }

    public function app_secret($app_id) {
        static $app_secrets;
        if ($app_secrets && isset($app_secrets[$app_id]))
                return $app_secrets[$app_id];

        $_row = $this->app->model('apps')->getRow('app_secret', array(
            'app_id' => $app_id
        ));
        if (!$_row) return false;
        $app_secrets[$app_id] = $_row['app_secret'];
        return $app_secrets[$app_id];
    }

    public function hook_url($hook_id) {
        static $hook_urls;
        if ($hook_urls && isset($hook_urls[$app_id]))
                return $hook_urls[$hook_id];

        $_row = $this->app->model('hooks')->getRow('hook_url', array(
            'hook_id' => $hook_id
        ));
        if (!$_row) return false;
        $hook_urls[$hook_id] = $_row['hook_url'];
        return $hook_urls[$hook_id];
    }

    public function sign_str($params, $secret) {
        $secret = trim($secret);
        if (!$params || !strlen($secret)) return false;
        ksort($params);

        $sign_str = '';
        $sign_arr = array();
        foreach ($params as $k => $v) {
            $sign_arr[] = $k . (is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES) : $v);
        }
        $_sign = $secret . implode('', $sign_arr) . $secret;
        $sign_str = strtoupper(md5($_sign));
        unset($sign_arr);
        return $sign_str;
    }

    protected static function http_build_query($post) {
        $res = null;
        if ($post && is_array($post)) {
            $_tmp = array();
            foreach ($post as $key => $val) {
                $_tmp[] = $key . '=' . $val;
            }
            $res = implode('&', $_tmp);
        }
        return $res;
    }

    protected function _send($hook, $task) {
        
        
        $msg_tpl = trim($hook['hook_msg_tpl']);
        !strlen($msg_tpl) && $msg_tpl = 'def';

        $_post_json_arr = $task['task_data'] ? $task['task_data'] : array();
        ksort($_post_json_arr);

        $hook_method = $this->_pre_method . $this->hook_name;

        $_params = array();
        $_params['app_key'] = $hook['app_id'];
        $_params['hook_key'] = $hook['hook_id'];
        $_params['timestamp'] = date('Y-m-d H:i:s');
        $_params['method'] = $hook_method;
        $_params['vmc_method_json'] = $_post_json_arr;
        $ori_out_datas = $_params;

        $code_out_datas = vmc::singleton('vmcconnect_object_hook_output_stage')->set_msg_tpl($msg_tpl)->out_encode($_params, $hook_method);
        ksort($code_out_datas);

        vmc::singleton('vmcconnect_object_hook_output_stage')->addSign($code_out_datas, $this->sign_str($code_out_datas, $hook['app_secret']));
        
        if (method_exists($hook_stage, 'send')) {
            $send_res = call_user_func_array(array($hook_stage, 'send'), array($code_out_datas, $hook, $_params));
            if(!$send_res) return $send_res;
        }

        $_url = $hook['hook_url'];

        $_post_data = $this->http_build_query($code_out_datas);
        $http_client = vmc::singleton('base_httpclient');
        $res = $http_client->post($_url, $_post_data);
        
        return $code_out_datas;
    }

    public function save_send($hook, $task, $data, $status = null) {
        $setDatas = array();
        $setDatas['app_key'] = $hook['app_id'];
        $setDatas['hook_key'] = $hook['hook_id'];
        $setDatas['task_id'] = $task['task_id'];
        $setDatas['task_type'] = $task['task_type'];

        $setDatas['app_secret'] = $hook['app_secret'];
        $setDatas['send_params'] = serialize($data) ? serialize($data) : null;
        $setDatas['send_date'] = time();
        switch ($status) {
            case 'succ':
                $setDatas['act_res'] = 1;
                break;
            case 'fail':
                $setDatas['act_res'] = 0;
                break;
        }

        return $this->app->model('hooktask_items')->save($setDatas);
    }

    protected function _exec_task($data, $task = null) {
        if (!$this->get_hook_status()) return false;

        $task && $task_id = $task['task_id'];
        !$task_id && $task_id = isset($data['task_id']) && is_numeric($data['task_id']) ? $data['task_id'] : 0;
        if (!$task_id) return true;
        !$task && $task = $this->get_task($task_id);
        if (!$task) return true;

        //如果执行的是在query_mysql表中的，则进行run_task
        $query_mysql_obj = app::get('system')->model('queue_mysql');
        $task_res = $query_mysql_obj->getlist('params', array('queue_name' => 'normal'));
        $task_res = array_keys(utils::array_change_key($task_res, 'params'));

        foreach ($task_res as $key => $val) {
            $task_res[$key] = unserialize($val);
            if (!in_array('task_id', array_keys($task_res[$key]))) {
                unset($task_res[$key]);
            }
        }
        $task_res = array_keys(utils::array_change_key($task_res, 'task_id'));
        if (in_array($task['task_id'], $task_res)) {
            $res = $this->run_task($task);
        }
        return $res;
    }

    //执行失败
    public function exception_handling($data, $task = null) {

        if (!$this->get_hook_status()) return false;
        $task && $task_id = $task['task_id'];

        !$task_id && $task_id = isset($data['task_id']) && is_numeric($data['task_id']) ? $data['task_id'] : 0;
        if (!$task_id) return true;
        !$task && $task = $this->get_task($task_id);

        if (!$task) return true;
        //①添加到hook日志内部
        $this->queue_run_task($task);
    }

    public function queue_run_task($task) {
//        var_dump($task);die;
        if (!$task) return false;
        $warning_conf = $this->app->getConf('vmcconnect-warning-conf');
        if (!$warning_conf['warning_enable']) return true;  //如果不启用警报设置，则直接返回

        $task_id = $task['task_id'];
        $this->hook_name = implode('.', explode('_', trim($task['task_type'])));

        $get_task_rows = $this->_get_task_rows($this->hook_name);   //找到需要发送消息的对象

        if (!$get_task_rows) {
            return true;
        }

        //②统计到fail_count数据库表内
        $fail_count_obj = $this->app->model('failure_count');
        $hooks_obj = @$this->app->model('hooks');

        $pretype = "vmcconnect_tasks_";
        foreach ($get_task_rows as $_key => $_row) {

            $data = array();
            $_out_datas = $this->_send($_row, $task) ? $this->_send($_row, $task) : null;
            $this->save_send($_row, $task, $_out_datas, 'fail');
            //将设置的预警号码取出来
            $res = $hooks_obj->getlist('hook_alert_phone', array('hook_id' => $_row['hook_id'], 'app_id' => $_row['app_id']));

            //先做判断，数据库中是否存在
            $old_res = $fail_count_obj->getlist('*', array('app_key' => $_row['app_id'], 'hook_key' => $_row['hook_id'], 'worker' => $pretype . $task['task_type']));
            if ($old_res) {
                //如果存在旧数据,则做更新操作，获取id
                $data['failure_count'] = $old_res[0]['failure_count'] + 1;
                $data['item_id'] = $old_res[0]['item_id'];
                $data['remove'] = 'true';
                $data['warning_count'] = $old_res[0]['warning_count'] > 0 ? $old_res[0]['warning_count'] : 0;
                //如果fail次数达到设置次数的整数倍，则warning加1
                if ($data['failure_count'] % $warning_conf['hook_alert'] == 0) {
                    $data['warning_count'] = $old_res[0]['warning_count'] + 1;
                    $data['remove'] = 'false';
                    //如果warning_count次数+1，则向设置的联系人发送短信通知并加入队列去执行
                    //挂载服务去执行队列任务
                    $service_key = 'vmcconnect.queue.warning';

                    //组装需要加入队列的参数
                    $queue_data = array();
                    $queue_data['type'] = 'hook';
                    $queue_data['worker'] = $task['task_type'];
                    $queue_data['alert_phone'] = $res[0]['hook_alert_phone'];

                    foreach (vmc::servicelist($service_key) as $service) {

                        if (method_exists($service, 'exec')) {
                            if (!$service->exec($task, $queue_data)) {
                                return false;
                            }
                        }
                    }
                }
                $data['app_key'] = $_row['app_id'];
                $data['hook_key'] = $_row['hook_id'];
                $data['service_type'] = 'hook';
                $data['alert_phone'] = $res[0]['hook_alert_phone'];
            } else {
                //如果不存在，则为新的添加，组装数组
                $data['app_key'] = $_row['app_id'];
                $data['hook_key'] = $_row['hook_id'];
                $data['service_type'] = 'hook';
                $data['worker'] = $pretype . $task['task_type'];
                $data['failure_count'] = 1;
                $data['remove'] = 'true';
                $data['warning_count'] = 0;

                $data['alert_phone'] = $res[0]['hook_alert_phone'];
            }

            $res = $fail_count_obj->save($data);
            if (!$res) return false;
        }

        return true;
    }

}
