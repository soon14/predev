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


class mobile_theme_base
{
    public function set_last_modify()
    {
        return app::get('mobile')->setConf('mobile_theme_last_modify', time());
    } //End Function
    public function get_last_modify()
    {
        return app::get('mobile')->getConf('mobile_theme_last_modify');
    } //End Function
    public function set_default($theme)
    {

        app::get('mobile')->model('themes')->update(array(
            'is_used' => 'false',
        ), array('theme_dir|notin'=>array($theme)));

        $flag = app::get('mobile')->model('themes')->update(array(
            'is_used' => 'true',
        ), array(
            'theme_dir' => $theme,
        ));

        app::get('mobile')->setConf('current_theme', $theme);

        return $flag;
    }
    public function clean($is_there)
    {
        $mdl_themes = app::get('mobile')->model('themes');
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
        if($_COOKIE['CURRENT_THEME_M']){
            $current_theme = utils::decrypt($_COOKIE['CURRENT_THEME_M']);
            if($current_theme){
                return $current_theme;
            }
        }
        return app::get('mobile')->getConf('current_theme');
    } //End Function
    public function update_theme($aData)
    {
        return app::get('mobile')->model('themes')->save($aData);
    } //End Function
    public function set_theme_cache_version($theme)
    {
        return app::get('mobile')->setConf('theme_cache_version.'.$theme, time());
    } //End Function
    public function get_theme_cache_version($theme)
    {
        return app::get('mobile')->getConf('theme_cache_version.'.$theme);
    } //End Function
    public function get_view($theme)
    {
        return vmc_('mobile', 'theme_get_view', $theme);
    } //End Function

    public function get_theme_views($theme)
    {
        $mdl_themes = app::get('mobile')->model('themes');
        $theme = $mdl_themes->getRow('views', array('theme_dir' => $theme));

        return $theme['views'];
    }
} //End Class
