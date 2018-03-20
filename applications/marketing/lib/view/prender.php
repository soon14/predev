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

class marketing_view_prender
{
    public function pre_display(&$content)
    {
        $expired = 40;
        $fmid_js=<<<SCRIPT
            <script>
                function getQueryVariable(variable)
                {
                       var query = window.location.search.substring(1);
                       var vars = query.split("&");
                       for (var i=0;i<vars.length;i++) {
                               var pair = vars[i].split("=");
                               if(pair[0] == variable){return pair[1];}
                       }
                       return(false);
                }
                var taskno = getQueryVariable("taskno");
                if(taskno){
                    $.cookie('taskno', taskno ,{path:'/',expires:{$expired}});
                }
            </script>

SCRIPT;
        $content = preg_replace("/(<\/body>)/",$fmid_js."$1",$content);
    }//End Function


}