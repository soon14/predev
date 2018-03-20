<?php

/**
 * 导入导出存储方式调用统一入口.
 */
class importexport_policy extends base_policy
{
    /**
     * 提供导入导出的存储方式.
     *
     * @return array $storage 返回存储方式的参数
     */
    public function storage_policy()
    {
        $storage = array(
            #storage支持类型为ftp
            'policy' => 'ftp',

            #后台导入导出存储方式配置页面
            'view' => array('html' => 'admin/ftp.html','app' => 'importexport'),

            #setConf存储配置信息的key
            'setting_key' => 'ftp_server_setting',

            //是否需要配置参数
            'is_store_params' => true,

            #存储服务器配置view页面 调用的其他参数
            'params' => array(
                'support_ftp' => extension_loaded('ftp'),//php是否支持ftp扩展
            ),
            //配置参数变量定义
            'var_server_params' => 'ftp_server',
        );

        return $storage;
    }

    /**
     * 保存存储方式的配置参数 如果需要配置则存储配置.
     *
     * @params array $params
     *
     * @return bool
     */
    public function set_storage_params($params)
    {
        $storage = $this->storage_policy();
        if ($storage['is_store_params']) {
            $flag = app::get('importexport')->setConf($storage['setting_key'], $params);
        } else {
            $flag = true;
        }

        return $flag ? true : false;
    }

    /**
     * 获取存储方式的配置参数.
     *
     * @return array $params
     */
    public function get_storage_params()
    {
        $storage = $this->storage_policy();
        if ($storage['is_store_params']) {
            if(defined('FTP_STORAGE') && constant('FTP_STORAGE') && preg_match("/ftp:\/\/([^:]+):([^@]+)@([^:]+):([\d]+)\/([\s\S]*)/",FTP_STORAGE,$matches)){
                //match ftp://uname:password@xxx.domain.com:21/path
                $params = array(
                    'defined'=>'true',
                    'host'=>$matches[3],
                    'port'=>$matches[4],
                    'name'=>$matches[1],
                    'pass'=>$matches[2],
                    'dir'=>$matches[5]
                );
                if(defined('FTP_PASV') && constant('FTP_PASV') === true){
                    $params['pasv'] = 'Y';
                }
            }else{
                $params = app::get('importexport')->getConf($storage['setting_key']);
            }
        } else {
            $params = true;
        }

        return $params;
    }


    /**
     * 获取到导入导出存储调用类,并建立连接.
     */
    public function connect()
    {
        $this->_instance();
        $params = $this->get_storage_params();
        if (!$params) {
            return false;
        }

        if (!$this->policy_obj->connect($params, $msg)) {
            return $msg;
        }

        return true;
    }

    /**
     * 实例化存储方式类.
     */
    private function _instance()
    {
        $params = $this->storage_policy();
        $obj_name = 'importexport_policy_'.$params['policy'];
        $this->policy_obj = vmc::singleton($obj_name);

        return true;
    }

    /**
     * 检查配置信息是否成功.
     */
    public function check()
    {
        $this->_instance();

        $params = $this->get_storage_params();

        return $this->policy_obj->check($params);
    }
}
