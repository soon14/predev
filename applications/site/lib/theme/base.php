<?php

// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed )
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------


class site_theme_base
{
    public function set_last_modify()
    {
        return app::get('site')->setConf('site_theme_last_modify', time());
    } //End Function
    public function get_last_modify()
    {
        return app::get('site')->getConf('site_theme_last_modify');
    } //End Function
    public function set_default($theme)
    {
        app::get('site')->model('themes')->update(array(
            'is_used' => 'false',
        ), array('theme_dir|notin'=>array($theme)));
        $flag = app::get('site')->model('themes')->update(array(
            'is_used' => 'true',
        ), array(
            'theme_dir' => $theme,
        ));
        app::get('site')->setConf('current_theme', $theme);
        return $flag;
    }
    public function clean($is_there)
    {
        $mdl_themes = app::get('site')->model('themes');
        $themes = $mdl_themes->getList();
        foreach ($themes as $theme) {
            if (!in_array($theme['theme_dir'], $is_there)) {
                $mdl_themes->delete(array('theme_id' => $theme['theme_id']));
            }
        }
    }
    public function theme_exists()
    {
        return (is_null($this->get_default())) ? false : true;
    } //End Function
    public function get_default()
    {
        if($_COOKIE['CURRENT_THEME']){
            $current_theme = utils::decrypt($_COOKIE['CURRENT_THEME']);
            if($current_theme){
                return $current_theme;
            }
        }
        return app::get('site')->getConf('current_theme');
    } //End Function
    public function update_theme($aData)
    {
        return app::get('site')->model('themes')->save($aData);
    } //End Function
    public function set_theme_cache_version($theme)
    {
        return app::get('site')->setConf('theme_cache_version.'.$theme, time());
    } //End Function
    public function get_theme_cache_version($theme)
    {
        return app::get('site')->getConf('theme_cache_version.'.$theme);
    } //End Function
    public function get_view($theme)
    {
        return vmc_('site', 'theme_get_view', $theme);
    } //End Function

    public function get_theme_views($theme)
    {
        $mdl_themes = app::get('site')->model('themes');
        $theme = $mdl_themes->getRow('views', array('theme_dir' => $theme));

        return $theme['views'];
    }
} //End Class
