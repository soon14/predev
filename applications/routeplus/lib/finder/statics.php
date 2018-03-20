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

class routeplus_finder_statics
{
    public $detail_statics = '自定义详情\编辑';

    public function detail_statics($id)
    {
        $render = app::get('routeplus')->render();
        $render->pagedata['base_url'] = vmc::base_url(true);
        $render->pagedata['app_url_map'] = vmc::$app_url_map;
        $render->pagedata['statics'] = app::get('routeplus')->model('statics')->dump($id);
        return $render->fetch('admin/edit_statics.html');
    }

    public function row_style($row){
        if($row['@row']['enable'] == 'false'){
            return 'text-muted';
        }

    }

}//End Class
