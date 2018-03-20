<?php

class site_finder_seo
{
    
    function detail_basic($id){
        $seo = app::get('site')->model('seo')->select()->where('id = ?', $id)->instance()->fetch_row();
        if(is_string($seo['param'])){
            $seo['param'] = unserialize($seo['param']);
        }
        if(is_string($seo['config'])){
            $seo['config'] = unserialize($seo['config']);
        }
        $render = app::get('site')->render();
        $render->pagedata['id'] = $id;
        $render->pagedata['param'] = $seo['param'];
        $render->pagedata['config'] = $seo['config'];//print_R($seo['config']);exit;
        return $render->fetch('admin/seo/base.html');
    }
}//End Class
