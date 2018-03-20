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


class wechat_mdl_xcxwidgets
{
    public function __construct(&$app)
    {
        $this->app = $app;
    }
    public static function sort_list($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }

        return $a['order'] > $b['order'] ? +1 : -1;
    }

    public function getList() {
        foreach (vmc::servicelist('wechat.xcxwidgets') as $class_name => $ins) {
            $_classes = method_exists($ins, 'get_classes') ? call_user_func_array(array($ins, 'get_classes'), array()) : false;
            if (!$_classes && !is_array($_classes)) continue;
            foreach ($_classes as $_cls_name) {
                $_obj = class_exists($_cls_name) ? vmc::singleton($_cls_name) : false;
                if (!$_obj) continue;
                $config = method_exists($_obj, 'getConfig') ? call_user_func_array(array($_obj, 'getConfig'), array()) : array();
                $return[] = $config;
                if ($name_filter && $name_filter == $config['name']) {
                    break;
                }
            }
        }

        usort($return, array(
            'wechat_mdl_xcxwidgets',
            'sort_list',
        ));

        return $return;
    }
    public function dump($name, $widget_data)
    {
        foreach (vmc::servicelist('wechat.xcxwidgets') as $class_name => $ins) {
            $_classes = method_exists($ins, 'get_classes') ? call_user_func_array(array($ins, 'get_classes'), array()) : false;
            if (!$_classes && !is_array($_classes)) continue;
            foreach ($_classes as $_cls_name) {
                $_obj = class_exists($_cls_name) ? vmc::singleton($_cls_name) : false;
                if (!$_obj) continue;
                $config = method_exists($_obj, 'getConfig') ? call_user_func_array(array($_obj, 'getConfig'), array()) : array();
                $return[] = $config;
                if ($name && $name == $config['name']) {
                    $fix_ins = $ins;
                    break;
                }
            }
        }

        $return = array(
            'config' => $config,
            'setting_html' => $ins->getSettingPanel($widget_data),
            'preview_html' => $ins->getDesignPreview($widget_data),
        );

        return $return;
    }
}
