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

class commission_prender
{
    public function pre_display(&$content)
    {
        $fmid_js=<<<SCRIPT
            <script>
                var arr=location.href.split("#");
                var nums = arr.length;
                if(nums>1){
                    var reg = /^[1-9][0-9]*$/;
                    for(var i = 1 ;i<nums ;i++){
                        var arg = arr[i].split("=");
                        if(arg[0] =='fmid' && reg.test(arg[1])){
                            $.cookie('fmid', arg[1] ,{path:'/'});
                            break;
                        }
                    }
                }
            </script>

SCRIPT;
        $content = preg_replace("/(<\/body>)/",$fmid_js."$1",$content);
    }//End Function


}