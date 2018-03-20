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
class wechat_xcxwidgets {

    protected $app;
    protected $default = array(
        'title' => '页面标题',
        'bg_hex' => '#FFFFFF',
        'version' => 0.001,
        'bar_title_hex' => '#000000',
        'bar_bg_hex' => '#FFFFFF',
        'bar_animation_duration' => '400',
        'widgets_draft' => array(),
    );
    protected static $xcx_widgets, $widget_names;

    public function __construct($app) {
        $this->app = $app;
    }
    
    public function get_classes() {
        return array(
            'wechat_xcxwidgets_searchbar',
            'wechat_xcxwidgets_slider',
            'wechat_xcxwidgets_imgnav',
            'wechat_xcxwidgets_goodslist',
            'wechat_xcxwidgets_linehelper',
            'wechat_xcxwidgets_blankhelper',
            'wechat_xcxwidgets_showcase',
            'wechat_xcxwidgets_textnav',
            'wechat_xcxwidgets_videos',
            'wechat_xcxwidgets_omnipotentform',
            'wechat_xcxwidgets_imagetext',
            'wechat_xcxwidgets_articlelist',
            'wechat_xcxwidgets_activecard',
            'wechat_xcxwidgets_smartwindow',
            'wechat_xcxwidgets_grouppresale',
        );
    }

    protected function _xcx_widgets() {
        if (self::$xcx_widgets) return self::$xcx_widgets;
        self::$xcx_widgets = $this->app->model('xcxwidgets')->getList();
        return self::$xcx_widgets;
    }

    protected function _widget_names() {
        if (self::$widget_names) return self::$widget_names;
        $this->_xcx_widgets();
        if (self::$xcx_widgets) {
            foreach (self::$xcx_widgets as $_v) {
                self::$widget_names[$_v['name']] = $_v;
            }
        }
        return self::$widget_names;
    }

    protected function _tpls() {
        $this->_xcx_widgets();
        if (!self::$xcx_widgets) return false;
        $res = array();
        if (self::$xcx_widgets) {
            foreach (self::$xcx_widgets as $_v) {
                $res[$_v['name']] = array(
                    'component' => "xcxwidgets/{$_v['name']}/component.html",
                    'item' => "xcxwidgets/{$_v['name']}/item.html",
                );
            }
        }
        return $res;
    }

    protected function _settingTpls() {
        $this->_xcx_widgets();
        if (!self::$xcx_widgets) return false;
        $res = array();
        if (self::$xcx_widgets) {
            foreach (self::$xcx_widgets as $_v) {
                $res[$_v['name']] = array(
                    'setting' => "xcxwidgets/{$_v['name']}/setting.html",
                    'component' => "xcxwidgets/{$_v['name']}/component.html",
                    'setting-item' => "xcxwidgets/{$_v['name']}/setting.item.html",
                );
            }
        }
        return $res;
    }

    protected function _name($name) {
        $name = trim($name);
        if (!$name) return false;
        return (substr($name, -1) == '*' ? substr($name, 0, -1) : $name);
    }

    protected function _widgets(&$widgets) {
        (!$widgets || !is_array($widgets)) && $widgets = array();
        if (!$widgets) return false;

        $this->_widget_names();
        if (!self::$widget_names) {
            $widgets = array();
            return false;
        }

        $names = array_keys(self::$widget_names);
        $tmp = array();
        foreach ($widgets as $_k => $_v) {
            $name = $this->_name($_v['name']);
            if (in_array($name, $names)) {
                $_v['data'] = array_merge(self::$widget_names[$name]['default'], ($_v['data'] ? $_v['data'] : array()));
                $tmp[] = $_v;
            }
        }

        $widgets = $tmp;
        return $widgets;
    }

    protected function _settingWidgets(&$widgets) {
        (!$widgets || !is_array($widgets)) && $widgets = array();
        if (!$widgets) return false;

        $this->_widget_names();
        if (!self::$widget_names) {
            $widgets = array();
            return false;
        }

        $names = array_keys(self::$widget_names);
        $tmp = array();
        foreach ($widgets as $_k => $_v) {
            $name = $this->_name($_v['name']);
            if (in_array($name, $names)) {
                $_v['onSetting'] = false;
                $_v['title'] = self::$widget_names[$name]['title'];
                $_v['desc'] = self::$widget_names[$name]['desc'];
                $_v['data'] = array_merge(self::$widget_names[$name]['default'], ($_v['data'] ? $_v['data'] : array()));
                $tmp[] = $_v;
            }
        }

        $widgets = $tmp;
        return $widgets;
    }

    public function getEditData(&$data) {
        (!$data || !is_array($data)) && $data = $this->default;

        $data['widget_names'] = array();
        $data['widget_tpls'] = array();

        !$data['widgets'] && $data['widgets'] = array();

        $data['xcx_widgets'] = $this->_xcx_widgets();
        $data['widget_names'] = $this->_widget_names();
        $data['widget_tpls'] = $this->_settingTpls();
        $this->_settingWidgets($data['widgets']);
        $this->_settingWidgets($data['widgets_draft']);
        return $data;
    }

    public function getData(&$data) {
        (!$data || !is_array($data)) && $data = $this->default;

        $data['widget_names'] = array();
        $data['widget_tpls'] = array();

        !$data['widgets'] && $data['widgets'] = array();

        $data['xcx_widgets'] = $this->_xcx_widgets();
        $data['widget_names'] = $this->_widget_names();
        $data['widget_tpls'] = $this->_tpls();
        $this->_widgets($data['widgets']);
        $this->_widgets($data['widgets_draft']);
        return $data;
    }

}
