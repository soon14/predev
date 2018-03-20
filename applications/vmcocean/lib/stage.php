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
define('vmcocean_SDK_PATH', dirname(__FILE__));
require_once vmcocean_SDK_PATH.'/sdk.php';
class vmcocean_stage
{
    public function __construct($app)
    {
        $this->app = $app;
          # 从 VMCOcean Analytics 配置页面中获取的数据接收的 URL
          $SA_SERVER_URL = $app->getConf('data_warehouse_url');
          # 可选参数，当缓存的数据量达到参数值时，批量发送数据
          $SA_BULK_SIZE = 100;
          # 可选参数，发送数据的超时时间，单位毫秒
          $SA_REQUEST_TIMEOUT = 100000;
        if ($app->getConf('debug_model') == 'enabled' || $app->getConf('debug_model') == 'enabledandpost') {
            # 初始化一个 DebugConsumer，用于数据发送
              # DebugConsumer 向logs/alert 中写入数据
              $consumer = new DebugConsumer($SA_SERVER_URL, ($app->getConf('debug_model') == 'enabledandpost'), $SA_REQUEST_TIMEOUT);
        } else {
            # 初始化一个 Consumer，用于数据发送
              $consumer = new BatchConsumer($SA_SERVER_URL, $SA_BULK_SIZE, $SA_REQUEST_TIMEOUT);
        }

          # 使用 Consumer 来构造 VMCOceanAnalytics 对象
          $this->sa = new VMCOceanAnalytics($consumer);

          # 程序结束前调用 close() ，通知 Consumer 发送所有缓存数据
          //$sa->close();
    }

    public function track_event($uid, $event_name, $params)
    {
        if ($this->app->getConf('enabled') != 'true') {
            return false;
        }
        $agent_obj = new Agent(); //Agent class in base static
        if ($agent_obj->isRobot()) {
            return false;
        }
        $device = $agent_obj->device();
        $platform = $agent_obj->platform();
        $browser = $agent_obj->browser();
        $agent_data = array(
            '$os' => $platform,
            '$os_version' => $agent_obj->version($platform),
            '$model' => $device,
            '$browser' => $browser,
            '$browser_version' => $agent_obj->version($browser),
        );
        $params = array_merge($agent_data, $params);
        $this->_filter_params($params);
        try {
            $_return = $this->sa->track($uid, $event_name, $params);
        } catch (Exprection $e) {
            logger::error($e->getMessage());

            return false;
        }

        return $_return;
    }
    public function track_sign($member_id, $uid)
    {
        if ($this->app->getConf('enabled') != 'true') {
            return false;
        }
        try {
            $_return = $this->sa->track_signup($member_id, $uid);
        } catch (Exprection $e) {
            logger::error($e->getMessage());

            return false;
        }

        return $_return;
    }

/**
 * 用户相关.
 */
     //设置用户属性
    public function profile_set($mid, $params)
    {
        if ($this->app->getConf('enabled') != 'true') {
            return false;
        }
        $this->_filter_params($params);
        try {
            $_return = $this->sa->profile_set($mid, $params);
        } catch (Exprection $e) {
            logger::error($e->getMessage());

            return false;
        }

        return $_return;
    }

    public function profile_set_once($mid, $params)
    {
        if ($this->app->getConf('enabled') != 'true') {
            return false;
        }
        $this->_filter_params($params);
        try {
            $_return = $this->sa->profile_set_once($mid, $params);
        } catch (Exception $e) {
            logger::error($e->getMessage());

            return false;
        }

        return $_return;
    }

    public function profile_append($mid, $params)
    {
        if ($this->app->getConf('enabled') != 'true') {
            return false;
        }
        $this->_filter_params($params);
        try {
            $_return = $this->sa->profile_set_once($mid, $params);
        } catch (Exception $e) {
            logger::error($e->getMessage());

            return false;
        }

        return $_return;
    }

    public function profile_increment($mid, $params)
    {
        if ($this->app->getConf('enabled') != 'true') {
            return false;
        }
        $this->_filter_params($params);
        try {
            $_return = $this->sa->profile_set_once($mid, $params);
        } catch (Exception $e) {
            logger::error($e->getMessage());

            return false;
        }
    }

    private function _filter_params(&$params)
    {
        foreach ($params as $key => &$value) {
            if ($value == '') {
                unset($params[$key]);
            }
        }
    }

    public function __destruct()
    {
        $this->sa->close(); //flash data to Hadoop
    }
}
