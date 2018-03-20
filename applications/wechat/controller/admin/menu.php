<?php
class wechat_ctl_admin_menu extends desktop_controller {

    function __construct($app) {
        parent::__construct($app);
    } //End Function
    //关注自动回复信息设置

    public function setting(){
        $wx_paccounts = app::get('wechat')->model('bind')->getList('id,avatar,name');
        $this->pagedata['wx_paccounts'] = $wx_paccounts;
        $this->page('admin/menu/setting.html');
    }

    public function edit($bind_id){
        $this->pagedata['bind_id'] = $bind_id;
        $access_token = vmc::singleton('wechat_stage')->get_access_token($bind_id);
        $menu_info_action = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=$access_token";
        $http = vmc::singleton('base_httpclient');
        $menu_result = $http->get($menu_info_action);
        $menu_data = json_decode($menu_result,1);
        $menu = $menu_data['selfmenu_info'];
        foreach ($menu['button'] as $key => $value) {
            if(!empty($value['sub_button'])){
                $menu['button'][$key]['sub_button'] = $value['sub_button']['list'];
            }
        }
        $this->pagedata['menu_data'] = json_encode($menu,JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        $this->display('admin/menu/menu_list.html');
    }

    public function save($bind_id){
        $this->begin();
        $menu_data = $_POST['menu_data'];
        $access_token = vmc::singleton('wechat_stage')->get_access_token($bind_id);
        $menu_crate_action = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $http = vmc::singleton('base_httpclient');
        $menu_result = $http->post($menu_crate_action,$menu_data);
        $menu_data  = json_decode($menu_result,1);
        if($menu_data && $menu_data['errcode'] === 0){
            $this->end(true,'发布成功！');
        }else{
            $this->end(false,'发布失败!'.$menu_data['errmsg']);
        }
    }
}
