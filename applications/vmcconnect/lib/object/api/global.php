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

class vmcconnect_object_api_global {

    protected $app, $_ref_obj, $_map_obj, $input_params = array(), $input_encode_params = array(), $output_datas = array(), $output_encode_datas = array();

    public function __construct() {
        header("Content-type: text/html; charset=utf-8");
    }

    public function setApp(&$app) {
        $this->app = $app;
    }

    public function setRefObj(&$obj) {
        $this->_ref_obj = $obj;
    }

    public function getMapObj() {
        if ($this->_map_obj) return $this->_map_obj;
        $this->_map_obj = vmc::singleton('vmcconnect_object_api_map');
        $this->_map_obj->setapp($this->app);
        return $this->_map_obj;
    }

    protected function _input() {
        return vmc::singleton('vmcconnect_object_api_input_stage');
    }

    protected function _output() {
        return vmc::singleton('vmcconnect_object_api_output_stage');
    }

    /*
     * 获取方法
     */

    public function get_request_params() {
        // 如已获取直接返回
        if ($this->input_params) return $this->input_params;

        // 获取 _params
        $this->input_params = $_POST;

        $this->input_encode_params = vmc::singleton('vmcconnect_object_api_input_stage')->get_params($this->input_params);

        // 获取 _app_key
        $_app_key = ($this->input_encode_params && isset($this->input_encode_params['app_key'])) ? $this->input_encode_params['app_key'] : null;
        $this->_ref_obj->set_app_key($_app_key);

        // 获取 _method
        $_method = ($this->input_encode_params && isset($this->input_encode_params['method'])) ? $this->input_encode_params['method'] : null;
        $this->_ref_obj->set_method($_method);


        // 获取 _act_version
        if ($params && isset($this->input_encode_params['v']) && in_array($this->input_encode_params['v'], $this->_ref_obj->get_act_versions())) {
            $__act_version = $this->input_encode_params['v'];
            $this->_ref_obj->set_act_version($__act_version);
        }
        // 获取 _com_tpl
        $_com_tpl = 'def';
        if ($this->input_encode_params && isset($this->input_encode_params['tpl']) && in_array($this->input_encode_params['tpl'], $this->_ref_obj->get_com_tpls())) {
            $_com_tpl = $this->input_encode_params['tpl'];
        }
        $this->_ref_obj->set_com_tpl($_com_tpl);

        // 获取 _out_type
        $_out_type = null;
        if ($this->input_encode_params && isset($this->input_encode_params['format']) && in_array($this->input_encode_params['format'], $this->_ref_obj->get_out_types())) {
            $_out_type = $this->input_encode_params['format'];
        }
        $this->_ref_obj->set_out_type($_out_type);

        $_param_json = array();
        if ($this->input_encode_params && array_key_exists('method_params', $this->input_encode_params)) {
            $_param_json = $this->input_encode_params['method_params'];
            $_param_json && !is_array($_param_json) && $_param_json = json_decode($_param_json, true);
        }

        $this->_ref_obj->set_params($this->input_encode_params);
        $this->_ref_obj->set_method_params($_param_json);
        unset($_param_json);
        return $params;
    }

    /*
     * 生成测试地址
     */

    protected function __build_test() {
        $vmc_param_json = array();
        $vmc_param_json['field'] = '*';
        $vmc_param_json['attrId'] = 1;
        //ksort($vmc_param_json);

        $params = array();
        $params['timestamp'] = time() - 360;
        $params['method'] = 'system.read.setting.info';
        $params['app_key'] = '1';
        $params['v'] = '1.0';
        $params['vmc_param_json'] = $vmc_param_json;

        $params_str = $this->params_str($params);
        myfun::vard(__METHOD__, $params_str);
    }

    /*
     * 检测系统参数
     */

    public function check_sys_params() {

        // $this->__build_test();
        //
        if (!$this->get_app_key()) {
            throw new Exception('缺少商家Id参数', '9');
            return false;
        }
        //
        if (!$this->_ref_obj->get_method()) {
            throw new Exception('缺少方法名参数', '14');
            return false;
        }

        $params = $this->_ref_obj->get_params();
        //
        $timestamp = ($params && isset($params['timestamp'])) ? $params['timestamp'] : null;
        $timestamp && !is_numeric($timestamp) && $timestamp = strtotime($timestamp);
        if (!$timestamp) {
            throw new Exception('缺少时间戳参数', '7');
            return false;
        }

        // 
        if ((time() - $timestamp) > 360) {
            throw new Exception('非法的时间戳参数', '8');
            return false;
        }

        //
        $sign = ($params && isset($params['sign'])) ? $params['sign'] : null;
        if (!$sign) {
            throw new Exception('缺少签名参数', '11');
            return false;
        }

        //
        $app_info = $this->get_app_info();
        if (!$app_info) {
            throw new Exception('无效的商家Id参数', '10');
            return false;
        }

        $app_status = $app_info['app_status'] ? ($app_info['app_api_status'] ? true : false) : false;
        if (!$app_status) {
            throw new Exception('无效的商家Id参数', '10');
            return false;
        }

        if (!$sign) {
            throw new Exception('缺少签名参数', '11');
            return false;
        }

        $ckh_sign = $this->sign_str($this->input_params);

        if ($sign != $ckh_sign) {
            throw new Exception('无效签名', '12');
            return false;
        }

        return true;
    }

    public function get_app_key() {
        $_app_key = $this->_ref_obj->get_app_key();
        if (!$_app_key) return false;
        return $_app_key;
    }

    /*
     * 根据app_key获取app_info
     * 
     * @return array 
     */

    public function get_app_info() {

        $_app_info = $this->_ref_obj->get_app_info();
        if ($_app_info) return $_app_info;

        $_app_key = $this->get_app_key();
        if (!$_app_key) return false;

        $_app_info = app::get('vmcconnect')->model('apps')->get_app($_app_key);
        $this->_ref_obj->set_app_info($_app_info);

        return $_app_info;
    }

    /*
     * 根据app_key获取app_secret
     * 
     * @return array 
     */

    public function get_app_secret() {
        $_app_secret = $this->_ref_obj->get_app_secret();
        if (!$_app_secret) {

            $_app_info = $this->get_app_info();
            if (!$_app_info) return false;

            $_app_secret = trim($_app_info['app_secret']);
            $_app_secret && $this->_ref_obj->set_app_secret($_app_secret);
        }

        return $_app_secret;
    }

    /**
     * 整理需要签名的数据数组
     * @param array $params <p>
     * 传入需要整理的数组
     * </p>
     * @param Throwable $previous [optional] <p>
     * @return <b>array</b> 
     * 将数组整理为已排序可用数组
     * </p>
     */
    public function sort_sign_params($params) {
        if (!$params) return false;
        unset($params['sign']);
        $vmc_param_json = isset($params['vmc_param_json']) ? $params['vmc_param_json'] : array();
        !is_array($vmc_param_json) && $vmc_param_json = json_decode($vmc_param_json, true);
        !$vmc_param_json && $vmc_param_json = array();
        //ksort($vmc_param_json);
        isset($params['vmc_param_json']) && $params['vmc_param_json'] = $vmc_param_json;
        ksort($params);
        return $params;
    }

    /*
     * 根据数组获取签名字符串
     */

    public function sign_str($params) {
        $params = $this->sort_sign_params($params);
        if (!$params) return false;

        $_app_secret = $this->get_app_secret();
        if (!$_app_secret) throw new Exception('无效的商家Id参数', '10');

        $sign_str = '';
        $sign_arr = array();
        foreach ($params as $k => $v) {
            $sign_arr[] = $k . (is_array($v) ? json_encode($v) : $v);
        }
        $sign_str = strtoupper(md5($_app_secret . implode('', $sign_arr) . $_app_secret));
        unset($sign_arr);
        return $sign_str;
    }

    /*
     * 生成带签名的数组
     */

    public function bind_params($params) {
        $sign_str = $this->sign_str($params);
        if (!$sign_str) throw new Exception('无效的商家Id参数', '10');
        $params['sign'] = $sign_str;
        unset($sign_str);
        return $params;
    }

    /*
     * 生成URL传值字符串
     */

    public function params_str($params) {
        $params = $this->bind_params($params);
        if (!$params) return false;

        $_build_params = array();
        foreach ($params as $k => $v) {
            $_build_params[] = $k . '=' . (is_array($v) ? json_encode($v) : $v);
        }
        $params_str = implode('&', $_build_params);
        unset($_build_params);
        return $params_str;
    }

    /*
     * 检测方法权限
     */

    protected function _check_app_method($rel_method) {
        $rel_method = trim($rel_method);

        $_obj_map = vmc::singleton('vmcconnect_object_api_map');
        $sys_allow_items = $_obj_map->sys_allow_api_items();
        if (!in_array($rel_method, $sys_allow_items)) return false;


        $_app_key = $this->get_app_key();
        if (!$_app_key) return false;

        $app_allow_items = app::get('vmcconnect')->model('app_items')->get_allow_api_items($_app_key);
        if (!in_array($rel_method, $app_allow_items)) return false;

        return true;
    }

    /*
     * 检测方法权限
     */

    public function get_call_method($method) {
        //$method = strtolower(trim($method));
        $method = trim($method);
        $pre_method = $this->_ref_obj->get_pre_method();
        //$method = $pre_method . $method;
        if (strpos($method, $pre_method) === 0) {
            $method = substr($method, strlen($pre_method));
        }
        return $method;
    }

    public function app_com_tpl() {
        $_app_info = $this->get_app_info();
        if (!$_app_info) return false;


        $_app_com_tpl = $_app_info['app_com_tpl'];
        return $_app_com_tpl;
    }

    public function method_com_tpl() {
        $_com_tpl = $this->_ref_obj->get_com_tpl();
        !strlen($_com_tpl) && $_com_tpl = $this->app_com_tpl();
        !strlen($_com_tpl) && $_com_tpl = 'def';
        return $_com_tpl;
    }

    /*
     * 检测方法参数
     */

    public function call_method() {

        $_method = $this->_ref_obj->get_method();
        $this->_ref_obj->set_method($_method);

        if (!$_method) {
            throw new Exception('请求被禁止', '3');
        }

        $_method_params = $this->_ref_obj->get_method_params();
        $this->_ref_obj->set_method_params($_method_params);

        $_method_com_tpl = $this->method_com_tpl();

        $_call_method = $this->get_call_method($_method);
        $_rel_method = $this->getMapObj()->get_rel_method($_call_method, $_method_com_tpl);

        $method_array = ($_rel_method) ? explode('.', $_rel_method) : null;
        if (!$method_array || count($method_array) < 2)
                throw new Exception('不存在的方法名', '15');

        $_tmp_cls = array_shift($method_array);
        $_call_action = implode('_', $method_array);
        $_call_class = $this->app->app_id . '_api_obj_' . $_tmp_cls;

        if (
                !class_exists($_call_class) ||
                !method_exists($_call_class, $_call_action)
        ) {
            throw new Exception('不存在的方法名', '15');
        }

        if (!$this->_check_app_method($_rel_method)) {
            throw new Exception('请求被禁止', '3');
        }

        $_api_obj = vmc::singleton($_call_class);
        $_api_obj->setApp($this->app);
        method_exists($_api_obj, '_before') && call_user_func_array(array($_api_obj, '_before'), array());
        $_out_data = call_user_func_array(array($_api_obj, $_call_action), ($_method_params ? array($_method_params) : array()));
        method_exists($_api_obj, '_after') && call_user_func_array(array($_api_obj, '_after'), array());
        $this->_ref_obj->set_out_data($_out_data);
    }

    /*
     * 输出json
     */

    protected function _error($code) {
        $code = (int) $code;
        if (!$code) return false;
        static $codes;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        $codes = false;
        if(!$codes && $ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/error_code.php')){
            $codes = (include $_tmp_file);
        }
        
        !$codes && $codes = include $this->app->app_dir . '/vars/api/error_code.php';
        if (!$codes || !isset($codes[$code])) return false;
        return $codes[$code];
    }

    // 输出映射方法未定义
    /*
     * 输出json
     */
    public function out_json() {
        $_out_data = $this->_ref_obj->get_out_data();
        $this->output_datas = $_out_data;
        $this->output_encode_datas = array();

        vmc::singleton('vmcconnect_object_api_output_stage')->set_com_tpl($this->_ref_obj->get_com_tpl())->get_out_datas($this->output_encode_datas, $this->_ref_obj->get_method(), $this->output_datas);
        vmc::singleton('vmcconnect_object_api_output_stage')->out_json($this->output_encode_datas);

        unset($_out_data);
        $this->log();
        exit;
    }

    /*
     * 数组转xml
     * 
     */

    private function _array2xml($data, $item = 'item', $id = 'id') {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $flag = true;
            if (is_array($val) && (array_keys($val) === range(0, count($val) - 1))) {
                $flag = false;
            }
            $xml .= $flag ? "<{$key}{$attr}>" : "";
            $xml .= (is_array($val) || is_object($val)) ? $this->_array2xml($val, is_string($key) ? $key : $item, $id) : $val;
            $xml .= $flag ? "</{$key}>" : "";
        }
        return $xml;
    }

    /*
     * xml转数组
     */

    private function _xml2array($xml) {
        return json_decode(json_encode((array) simplexml_load_string($xml)), true);
    }

    /*
     * 输出XML
     */

    public function out_xml() {
        $_out_data = $this->_ref_obj->get_out_data();
        $this->output_datas = $_out_data;
        $this->output_encode_datas = array();

        vmc::singleton('vmcconnect_object_api_output_stage')->set_com_tpl($this->_ref_obj->get_com_tpl())->get_out_datas($this->output_encode_datas, $this->_ref_obj->get_method(), $this->output_datas);
        vmc::singleton('vmcconnect_object_api_output_stage')->out_xml($this->output_encode_datas);
        unset($_out_data);
        $this->log();
        exit;
    }

    protected function _bind_err_msg($msg, $msg_strs) {
        $call_array = array($msg);
        $msg_strs && is_array($msg_strs) && $call_array = array_merge($call_array, $msg_strs);
        return call_user_func_array('sprintf', $call_array);
    }

    protected function log() {
        try {
            $datas = array();
            $datas['app_key'] = $this->get_app_key();
            $datas['api_method'] = $this->_ref_obj->get_method();
            $datas['log_date'] = time();
            $datas['log_ip'] = ip2long(base_request::get_remote_addr());
            $datas['act_res'] = (
                    $this->output_datas &&
                    isset($this->output_datas['code']) &&
                    !empty($this->output_datas['code'])
                    ) ? 0 : 1;
            $datas['ori_in_params'] = serialize($this->input_params);
            $datas['code_in_params'] = serialize($this->input_encode_params);
            $datas['ori_out_params'] = serialize($this->output_datas);
            $datas['code_out_params'] = serialize($this->output_encode_datas);

            $this->app->model('apilogs')->save($datas);

            $model = $this->app->model('apilogs');
            $log_lastinsertid = $model->db->exec("select count(*) from vmc_vmcconnect_apilogs");
            //判断预警配置是否开启

            $warning_conf = $this->app->getConf('vmcconnect-warning-conf');
            if (!$warning_conf['warning_enable']) return true;  //如果不启用警报设置，则直接返回

            //将设置的预警号码取出来
            $fail_count_obj = $this->app->model('failure_count');
            $apps_obj = @$this->app->model('apps');
            $res = $apps_obj->getlist('api_alert_phone', array('api_id'=>$this->get_app_key()));

            //如果执行成功
            if ($datas['act_res'] == 1){
                //判断执行的method是否在failure表中
                $fail_model = app::get('vmcconnect')->model('failure_count');
                $fail_methods = $fail_model->getlist('item_id',array('worker'=>$this->_ref_obj->get_method()));

                if ($fail_methods){
                    $data['remove'] = 'true';
                    foreach ($fail_methods as $k=>$v){
                        $data['item_id'] = $v['item_id'];
                        $data['failure_count'] = 0;
                        $data['warning_count'] = 0;
                        $fail_model->save($data);
                    }
                }
            }

            //如果得到的是失败的数据
            $data = array();
            if ($datas['act_res'] == 0){

                //①组装数组
                //②判断此method在表中是否已存在
                $old_data = $fail_count_obj->getlist('*',array(
                                                'app_key'=>$this->get_app_key(),
                                                'service_type'=>'api',
                                                'worker'=>$this->_ref_obj->get_method()));

                if ($old_data){
                    //如果存在旧数据,则做更新操作，获取id
                    $data['failure_count'] = $old_data[0]['failure_count'] + 1;
                    $data['item_id'] = $old_data[0]['item_id'];

                    //如果fail次数达到设置次数的整数倍，则warning加1
                    if ($data['failure_count'] % $warning_conf['hook_alert'] == 0){
                        $data['warning_count'] = $old_data[0]['warning_count'] + 1;
                        $data['remove'] = 'false';
                        //如果warning_count次数+1，则向设置的联系人发送短信通知并加入队列去执行
                        //挂载服务去执行队列任务
                        $service_key = 'vmcconnect.queue.warning';
                        //组装需要加入队列的参数
                        $queue_data = array();
                        $queue_data['type'] = 'api';
                        $queue_data['worker'] = $old_data[0]['worker'];
                        $queue_data['alert_phone'] = $res[0]['api_alert_phone'];

                        $task['logs_id'] = $log_lastinsertid['rs'][0]['count(*)'];
                        foreach (vmc::servicelist($service_key) as $service) {

                            if (method_exists($service, 'exec')) {
                                if (!$service->exec($task, $queue_data)) {
                                    return false;
                                }
                            }
                        }
                    }
                    $data['app_key'] = $this->get_app_key();
                    $data['service_type'] = 'api';
                    $data['alert_phone'] = $res[0]['api_alert_phone'];

                }else{
                    //如果不存在，则为新的添加，组装数组
                    $data['app_key'] = $this->get_app_key();
                    $data['service_type'] = 'api';
                    $data['worker'] = $this->_ref_obj->get_method();
                    $data['failure_count'] = 1;
                    $data['remove'] = 'true';
                    $data['warning_count'] = 0;
                    $data['alert_phone'] = $res[0]['api_alert_phone'];
                }
                //失败情况下将数据保存到表中
                $res = $fail_count_obj->save($data);
                if (!$res) return false;

            }

            return true;

        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
        }
    }

}
