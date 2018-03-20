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


class site_view_pre_render
{
    public function pre_display(&$content)
    {
        if($_COOKIE['CURRENT_THEME']){
            //模板预览模式
            $preview_js_src = app::get('base')->res_url.'/misc/theme_preview.js';
            $preview_js_tag="<script src='$preview_js_src'></script>";
            $content = preg_replace("/(<\/body>)/",$preview_js_tag."$1",$content);
        }

    }//End Function


}
