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


class mobile_view_helper
{
    public function function_SYSTEM_HEADER_M($params, &$smarty)
    {
        $smarty->pagedata['base_url'] = vmc::base_url(1);
        $smarty->pagedata['res_url'] = app::get('site')->res_url;
        $smarty->pagedata['res_url_local'] = vmc::base_url(1).'/public';
        $html = $smarty->fetch('header.html', app::get('mobile')->app_id);
        $services = vmc::servicelist('mobile_view_helper');
        foreach ($services as $service) {
            if (method_exists($service, 'function_SYSTEM_HEADER_M')) {
                $html .= $service->function_SYSTEM_HEADER_M($params, $smarty);
            }
        }

        return $html;
    }//End Function

    public function function_SYSTEM_FOOTER_M($params, &$smarty)
    {
        $smarty->pagedata['base_url'] = vmc::base_url(1);
        $html = $smarty->fetch('footer.html', app::get('mobile')->app_id);
        $services = vmc::servicelist('mobile_view_helper');
        foreach ($services as $service) {
            if (method_exists($service, 'function_SYSTEM_FOOTER_M')) {
                $html .= $service->function_SYSTEM_FOOTER_M($params, $smarty);
            }
        }
        return $html;
    }//End Function

    public function function_template_filter_m($params, &$smarty)
    {

        if ($params['type']) {
            $render = vmc::singleton('base_render');
            $theme = vmc::singleton('mobile_theme_base')->get_default();
            vmc::singleton('mobile_theme_install')->monitor_change($theme);
            $views = vmc::singleton('mobile_theme_base')->get_theme_views($theme);
            $render->pagedata['theme'] = $theme;
            $render->pagedata['theme_info'] = app::get('mobile')->model('themes')->dump(array('theme_dir'=>$theme));
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

            return $render->fetch('admin/theme/tmpl/template_filter.html', app::get('mobile')->app_id);
        } else {
            return '';
        }
    }//End Function

    public function function_morepage($params, &$smarty)
    {

        $c = intval($params['data']['current']);
        $t = intval($params['data']['total']);
        if (!$c) {
            $c = 1;
        }
        if (!$t) {
            $t = 1;
        }
        if ($t < 2) {
            return '';
        }
        $l = $params['data']['link'];
        if (is_array($l)) {
            $l = app::get('mobile')->router()->gen_url($l);
        }
        $p = $params['data']['token'];
        $data_morepage = "{\"total\":$t,\"token\":\"$p\",\"link\":\"$l\"}";
        return '<button type="button" class="btn btn-morepage btn-block btn-outlined" data-morepage='.$data_morepage.'><span class="fa fa-circle-o-notch fa-spin hidden"></span> 加载更多 <span class="fa fa-ellipsis-h"></span></button>';

    }
}//End Class
