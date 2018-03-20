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

class requestfilter_view_prender
{
    public function pre_display(&$content)
    {
        if(app::get('requestfilter')->getConf('csrf_filter') =='true'){
            $token_input = vmc::singleton('requestfilter_view_helper') ->function_csrf_field();
            $content = preg_replace("/(<\/form>)/",$token_input."$1",$content);
            //TODO 该方案可能存在缓存问题，需要观察，可使用cookie，但是需要防止xss盗取cookie
            $token = vmc::singleton('requestfilter_csrf')->get_token();
            $ajax_js=<<<SCRIPT
            <script>
                if(typeof(jQuery) != 'undefined'){
                    (function($){
                    $.ajaxSetup( {
                        headers: {
                                "X-CSRF-TOKEN": '$token',
                            } ,
                        } );

                    })(jQuery);
  	            }

            </script>
SCRIPT;
            $content = preg_replace("/(<\/body>)/",$ajax_js."$1",$content);
        }
    }//End Function

}