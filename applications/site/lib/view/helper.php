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


class site_view_helper
{
    public function function_SYSTEM_HEADER($params, &$smarty)
    {
        $smarty->pagedata['base_url'] = vmc::base_url(1);
        $smarty->pagedata['res_url'] = app::get('site')->res_url;
        $smarty->pagedata['res_url_local'] = vmc::base_url(1).'/public';
        $html = $smarty->fetch('header.html', app::get('site')->app_id);
        $services = vmc::servicelist('site_view_helper');
        foreach ($services as $service) {
            if (method_exists($service, 'function_SYSTEM_HEADER')) {
                $html .= $service->function_SYSTEM_HEADER($params, $smarty);
            }
        }

        return $html;
    }//End Function

    public function function_SYSTEM_FOOTER($params, &$smarty)
    {
        $smarty->pagedata['base_url'] = vmc::base_url(1);
        $html = $smarty->fetch('footer.html', app::get('site')->app_id);
        $services = vmc::servicelist('site_view_helper');
        foreach ($services as $service) {
            if (method_exists($service, 'function_SYSTEM_FOOTER')) {
                $html .= $service->function_SYSTEM_FOOTER($params, $smarty);
            }
        }

        return $html;
    }//End Function

    public function function_template_filter($params, &$smarty)
    {
        if ($params['type']) {
            $render = vmc::singleton('base_render');
            $theme = vmc::singleton('site_theme_base')->get_default();
            vmc::singleton('site_theme_install')->monitor_change($theme);
            $views = vmc::singleton('site_theme_base')->get_theme_views($theme);
            $render->pagedata['theme'] = $theme;
            $render->pagedata['theme_info'] = app::get('site')->model('themes')->dump(array('theme_dir'=>$theme));
            $render->pagedata['list'] = $views[$params['type']];
            unset($params['type']);
            $render->pagedata['selected'] = $params['selected'];
            unset($params['selected']);
            if (is_array($params)) {
                foreach ($params as $k => $v) {
                    $ext .= sprintf(' %s="%s"', $k, $v);
                }
            }
            $render->pagedata['ext'] = $ext;
            return $render->fetch('admin/theme/tmpl/template_filter.html', app::get('site')->app_id);
        } else {
            return '';
        }
    }//End Function
}//End Class
