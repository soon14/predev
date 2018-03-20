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
class ssoclient_view_prender{

    public function function_SYSTEM_FOOTER($params, &$smarty)
    {
        return $this ->get_html();
    }//End Function


    public function function_SYSTEM_FOOTER_M($params, &$smarty)
    {

        return $this ->get_html();
    }//End Function

    private function get_html(){
        $sso_server = SSO_SERVER.'openapi/sso/get_token';
        $html =<<<SCRIPT
<script>
    var is_login = 0;
    $.ajax({
        url :"$sso_server",
        type:"get",
        dataType:"jsonp",
        success :function(re){
            console.log(re);
            if(re.result=='success'){
                var data = {
                    vmc_uid :re.data.vmc_uid,
                    vmc_utoken :re.data.vmc_utoken
                }
                to_login(data)
            }else{
                to_logout()
            }

        }
    });

    function to_login(data){
        $.post("/index.php/openapi/ssoclient/login" ,data ,function(re){
            console.log(re);
            if(re.result=='success'){
                is_login = 1;
                login_toggle();
            }else{
                to_logout();
            }
        },'json');
    }

    function to_logout(){
        $.get("/index.php/openapi/ssoclient/logout"  ,function(re){
            if(re.result=='success'){
                is_login = 0;
                login_toggle();
            }
        },'json');
    }

    function login_toggle(){
        if (is_login == 1) {
            $('.top-tools .is-unlogin').addClass('hidden');
            $('.top-tools .is-login').removeClass('hidden');
            $('.top-tools .is-login .uname').text($.cookie('UNAME'));
        } else {
            $('.top-tools .is-login').addClass('hidden');
            $('.top-tools .is-unlogin').removeClass('hidden');
        }
    }

    $('body').on('click','.j-logout,.btn-logout', function(e){
        e.preventDefault();
        $.get("/index.php/openapi/ssoclient/logout"  ,function(re){
            if(re.result=='success'){
                location.reload();
            }
        },'json');
    });
</script>
SCRIPT;
        return $html;
    }

}