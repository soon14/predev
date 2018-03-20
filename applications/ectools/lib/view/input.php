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


class ectools_view_input
{
    public function input_region($params)
    {
        $package = vmc::service('ectools_regions.ectools_mdl_regions');
        $params['package'] = $package->key;
        if (!$params['callback']) {
            unset($params['callback']);
        }
        $render = new base_render(app::get('ectools'));
        if (!$params['id']) {
            $params['id'] = substr(md5(uniqid()), 0, 6);
        }
        $render->pagedata['params'] = $params;
        $area_depth = app::get('ectools')->getConf('system_area_depth');
        $aDepth = array();
        for ($i = 0;$i < $area_depth;$i++) {
            $aDepth[] = $i;
        }
        $render->pagedata['area_depth'] = $aDepth;

        $views = 'common/region.html';

        return $render->fetch($views);
    }

    public function input_country($params)
    {
        $render = new base_render(app::get('ectools'));
        if (!$params['id']) {
            $params['id'] = substr(md5(uniqid()), 0, 6);
        }
        $render->pagedata['params'] = $params;
        $mdl_country = app::get('ectools')->model('country');
        $country_list = $mdl_country->getList('*');
        foreach ($country_list as $key => &$value) {
            $value['flag'] = app::get('ectools')->res_url.'/misc/flags/'.strtolower($value['ct_abbreviation']).'.png';
        }
        $render->pagedata['country_list'] = $country_list;

        return $render->fetch('common/country.html');
    }
}
