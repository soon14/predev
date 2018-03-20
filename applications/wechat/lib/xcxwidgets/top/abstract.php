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


abstract class wechat_xcxwidgets_top_abstract
{
    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app)
    {
        $this->app = $app ? $app : app::get('wechat');
    }

    public function getSettingPanel($widget_data)
    {
        $config = $this->getConfig();
        $default_widget_data = $config['default'];
        if ($widget_data) {
            $widget_data = array_merge($default_widget_data, $widget_data);
        } else {
            $widget_data = $default_widget_data;
        }
        $render = $this->app->render();
        $render->pagedata['config'] = $config;
        $render->pagedata['data'] = $widget_data;

        return $render->fetch('xcxwidgets/'.$config['name'].'/'.'setting.html');
    }
    public function getDesignPreview($widget_data)
    {
        $config = $this->getConfig();
        $default_widget_data = $config['default'];
        if ($widget_data) {
            $widget_data = array_merge($default_widget_data, $widget_data);
        } else {
            $widget_data = $default_widget_data;
        }
        $render = $this->app->render();
        $render->pagedata['config'] = $config;
        $render->pagedata['data'] = $widget_data;

        return $render->fetch('xcxwidgets/'.$config['name'].'/'.'preview.html');
    }
}
