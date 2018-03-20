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

class routeplus_rstatics
{
    private static $_genurl_map;
    private $_kvprefix = 'routeplus/statics';

    public function set_dispatch($key, $val)
    {
        return base_kvstore::instance($this->_kvprefix.'/dispatch')->store($key, $val, 0, false);
    }//End Function

    public function get_dispatch($key)
    {
        if (base_kvstore::instance($this->_kvprefix.'/dispatch')->fetch($key, $val)) {
            return $val;
        } else {
            return false;
        }
    }//End Function

    public function del_dispatch($key)
    {
        return base_kvstore::instance($this->_kvprefix.'/dispatch')->delete($key);
    }//End Function

    public function set_genurl($key, $val)
    {
        $this->refresh_genurl_map();

        return base_kvstore::instance($this->_kvprefix.'/genurl')->store($key, $val, 0, false);
    }//End Function

    public function get_genurl($key)
    {
        if (!isset(self::$_genurl_map)) {
            self::$_genurl_map = array();
            base_kvstore::instance($this->_kvprefix)->fetch('genurl_map', self::$_genurl_map);
        }
        if (isset(self::$_genurl_map[$key])) {
            app::get('routeplus')->getConf('statics.genurl_map_modify');     //使cache过期检查生效
            return self::$_genurl_map[$key];
        } else {
            return false;
        }
    }//End Function

    public function del_genurl($key)
    {
        $this->refresh_genurl_map();

        return base_kvstore::instance($this->_kvprefix.'/genurl')->delete($key);
    }//End Function

    public function refresh_genurl_map()
    {
        $maps = app::get('routeplus')->model('statics')->getList('custom_url, url', array('enable' => 'true'));
        if(!$maps){
            return base_kvstore::instance($this->_kvprefix)->delete('genurl_map');
        }
        foreach ($maps as $map) {
            $data[$map['url']] = $map['custom_url'];
        }
        base_kvstore::instance($this->_kvprefix)->store('genurl_map', $data);
        app::get('routeplus')->setConf('statics.genurl_map_modify', microtime());
    }//End Function
}//End Class
