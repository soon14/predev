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
class o2ocds_ctl_admin_invitation extends desktop_controller {

    public function index() {
        $this->finder('o2ocds_mdl_invitation', array(
            'title' => ('邀请码列表'),
            'finder_extra_view' => array(array('app'=>'o2ocds','view'=>'/admin/invitation/finder_invitation.html')),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
        ));
    }

    public function add() {
        $this->pagedata['member_lvs'] = app::get('b2c')->model('member_lv')->getList();
        $this->page('admin/invitation/add.html');
    }

    public function save() {
        $this->begin('index.php?app=o2ocds&ctl=admin_invitation&act=qrcode.html');
        $mdl_invitation = app::get('o2ocds')->model('invitation');
        $data = $_POST;
        $data['op_id'] = $this->user->user_id;
        $data['invitation_code'] = $mdl_invitation->apply_code();
        $data['createtime'] = time();
        if(!$mdl_invitation->save($data)) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'','index.php?app=o2ocds&ctl=admin_invitation&act=qrcode'.'&p[0]invitation_code='.$data['invitation_code']);
    }

    public function qrcode($code) {
        $mdl_invitation = app::get('o2ocds')->model('invitation');
        $code = $mdl_invitation->getRow('*',array('invitation_code'=>$code));
        $this->pagedata['url'] = vmc::singleton('mobile_router')->gen_url(array(
            'app'=>'b2c',
            'ctl'=>'mobile_product',
            'act'=>'index',
            'args'=>array('code'=>$code['code']),
            'full'=>1
        ));
        $this->pagedata['code'] = $code;
        $this->page('admin/invitation/qrcode.html');
    }




}