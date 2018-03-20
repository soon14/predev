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


class mobile_theme_install
{
    public function check()
    {
        $d = dir(vmc::get_themes_m_root_dir());
        while (false !== ($entry = $d->read())) {
            if (in_array($entry, array(
                '.',
                '..',
                '.svn',
                '.git',
            ))) {
                continue;
            }
            if (is_dir(vmc::get_themes_m_root_dir().'/'.$entry)) {
                $this->init_theme($entry);
            }else{
                continue;
            }
            //设置默认模板
            if (!vmc::singleton('mobile_theme_base')->get_default()) {
                vmc::singleton('mobile_theme_base')->set_default($entry);
            }
            $is_there[] = $entry;
        }
        if (!empty($is_there) && !in_array(vmc::singleton('mobile_theme_base')->get_default(), $is_there)) {
            vmc::singleton('mobile_theme_base')->set_default($is_there[0]);
        }
        $d->close();
        vmc::singleton('mobile_theme_base')->clean($is_there);
    } //End Function


    public function monitor_change($theme){
        $config_file_path = vmc::get_themes_m_root_dir().'/'.$theme.'/config.xml';
        if(!file_exists($config_file_path))return false;
        $last_modify_time = filemtime($config_file_path);
        if(!$last_modify_time){
            return false;
        }
        $exist_last_time = app::get('mobile')->getConf($theme.'_theme_last_config');
        $exist_last_time = $exist_last_time?$exist_last_time:0;

        if($exist_last_time - $last_modify_time == 0){
            return true;
        }else{
            app::get('mobile')->setConf($theme.'_theme_last_config',$last_modify_time);
        }
        $config_xml_content = file_get_contents($config_file_path);
        if ($config_xml_content) {
            $theme_info = vmc::singleton('mobile_utility_xml')->xml2arrayValues($config_xml_content);
        }
        if (empty($theme_info)) {
            return false;
        }
        $config = $theme_info;

        $theme_sdf = array(
            'theme_id' => $config['theme']['id']['value'],
            'theme_dir'=>$theme,
            'name' => $config['theme']['name']['value'],
            'version' => $config['theme']['version']['value'],
            'info' => $config['theme']['info']['value'],
            'author' => $config['theme']['author']['value'],
            'config' => array() , //TODO
        );

        $theme_sdf = vmc_('mobile','theme_install_config',$theme_sdf,$config);
        if (!vmc::singleton('mobile_theme_base')->update_theme($theme_sdf)) {
            return false;
        }

        return $theme_sdf;

    }
    public function init_theme($theme)
    {
        return $this->monitor_change($theme);
    }
} //End Class
