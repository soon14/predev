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
class blacklist_ctl_admin_member extends desktop_controller{
    public function index($page = 1, $keyword = false)
    {
        $pagelimit = 20;
        $mdl_pam_members = app::get('pam')->model('members');
        $mdl_b2c_members = app::get('b2c')->model('members');

        $member_filter = null;
        if ($keyword) {
            $member_range = $mdl_pam_members->getList('member_id', array('login_account|has' => $keyword));
            $member_range = $member_range ? $member_range : array();
            $member_ids = array_keys(utils::array_change_key($member_range, 'member_id'));
            $member_filter['member_id'] = $member_ids;
        }
        $mdl_members = $this->app->model('members');
        $items = $mdl_members->getList('*', $member_filter, $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_members->count($member_filter);

        $member_ids = array_keys(utils::array_change_key($items, 'member_id'));
        $members = $mdl_b2c_members->getList('*', array('member_id' => $member_ids));
        $pam_members =  $mdl_pam_members->getList('member_id , login_account ,login_type', array('member_id' => $member_ids));
        $pam_members = utils::array_change_key($pam_members ,'member_id');

        $this->pagedata['pam_members'] = $pam_members;
        $this->pagedata['members'] = $members;
        $this->pagedata['items_count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=blacklist&ctl=admin_member&act=index&p[0]='.time().'&in_page=true' ,
            'token' => time(),
        );
        if ($_GET['in_page']) {
            $this->display('/admin/member/items.html');
        } else {
            $this->page('/admin/member/index.html');
        }
    }

    public function update_members($action = 'save'){
        $members = $_POST['member_id'];
        $mdl_member = $this->app->model('members');
        if (empty($members)) {
            $this->end(false);
        }
        $this ->begin('index.php?app=blacklist&ctl=admin_member&act=index');
        if ($action == 'delete' && !is_array($members)) {
            $mdl_member->delete(array(
                'member_id' => $members,
            ));
        } else {
            foreach ($members as $member_id) {
                $data = array('member_id' =>$member_id);
                if ($action == 'save') {
                    if($mdl_member->count($data)){
                        continue;
                    }
                    $data['createtime'] =time();
                }
                if (!$mdl_member->{$action}($data)) {
                    $this->end(false);
                }
            }
        }
        $this ->end(true);
    }
}