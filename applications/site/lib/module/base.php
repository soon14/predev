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


class site_module_base {
    final public function create_site_config() {
        $conf = $this->assemble_config();
        return $this->write_config($conf);
    } //End Function
    final public function assemble_config() {
        $rows = app::get('site')->model('modules')->select()->where('enable = ?', 'true')->instance()->fetch_all();
        if (is_array($rows)) {
            $conf = array();
            foreach ($rows AS $row) {
                //$conf[$row['path']] = array($row['app'], $row['ctl'], $row['title'], trim($row['extension']),
                $conf[$row['path']] = array(
                    'app' => $row['app'],
                    'ctl' => $row['ctl'],
                    'title' => $row['title'],
                    'extension' => trim($row['extension']) ,
                    'use_ssl' => $row['use_ssl'] === 'true' ? true : false
                );
            }
            return $conf;
        }
        return false;
    } //End Function
    final public function write_config($conf) {
        if (is_array($conf)) {
            return app::get('site')->setConf('sitemaps', $conf);
        } else {
            return false;
        }
    } //End Function

} //End Class
