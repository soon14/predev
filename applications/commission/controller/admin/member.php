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
class commission_ctl_admin_member extends desktop_controller
{
    public function index($member_id,$type){
        switch($type){
            case 0 :
                $where = array('member_id' => $member_id);break;    //某一个会员
            case 1:
                $where = array('parent_id' => $member_id);break; //查看直属下级
            default:
                $where = 1;
        }
        $actions_base['base_filter'] = $where;
        $group[] = array(
            'label' => ('修改上级') ,
            'data-submit' => 'index.php?app=commission&ctl=admin_member&act=batch_edit&p[0]=parent',
            'data-target' => '_ACTION_MODAL_',
        );
        $custom_actions[] = array(
            'label' => ('批量操作') ,
            'group' => $group,
        );
        $actions_base['title'] = ('分佣关系');
        $actions_base['actions'] = $custom_actions;
        $this->finder('commission_mdl_member_relation', $actions_base);
    }

    /**
     * 批量编辑.
     */
    public function batch_edit($type = '')
    {
        $params = $_POST;
        if (count($_POST['member_id']) < 1) {
            echo '请选择会员';
            exit;
        }
        switch ($type) {
            case 'parent':
                break;
        }
        $this->pagedata['filter'] = htmlspecialchars(serialize($params));
        $this->display('admin/member/batchedit/'.$type.'.html');
    }
    public function batch_save(){
        $this->begin();
        $params = $_POST;
        $type = $params['type'];
        $filter = unserialize(trim($params['filter']));
        switch ($type) {
            case 'parent':
                if ($params["member_account"]) {
                    $mdl_member_relation = $this ->app ->model('member_relation');
                    //检查绑定帐号是否合法
                    $mdl_pam_members = app::get('pam')->model('members');
                    $member_info = $mdl_pam_members->getRow('member_id', array(
                        'login_account' => trim($params["member_account"]),
                    ));
                    if (!$member_info['member_id']) {
                        $this->end(false,'该上级会员帐号不存在');
                    }
                    $parent_id = $member_info['member_id'];
                    if($this ->app->getConf('relation_change') == 1){//与会员等级有关
                        $member_ids = $filter['member_id'];
                        $member_lv = app::get('b2c')->model('member_lv')->getList("*", array('disabled' => 'false'));
                        $member_lv = utils::array_change_key($member_lv ,'member_lv_id');
                        $parent_lv = app::get('b2c') ->model('members') ->dump(array('member_id' => $parent_id));
                        foreach($member_ids as $v){
                            $current_lv = app::get('b2c') ->model('members') ->dump(array('member_id' => $v));
                            if($member_lv[$current_lv["member_lv"]["member_group_id"]]['experience']>=$member_lv[$parent_lv["member_lv"]["member_group_id"]]['experience']){
                                $current_member = vmc::singleton('b2c_user_object')->get_member_info($v);
                                $this->end(false,"会员[{$current_member['local_uname']}]等级不比[{$params["member_account"]}]等级低，请修改");
                            }
                        }
                    }
                    $parent =$mdl_member_relation ->getRow('*' ,array('member_id' => $parent_id));
                    $update = array(
                        'parent_id' => $parent_id,
                        'parent_path' => $parent['parent_id']  ?$parent_id.','.$parent['parent_id'] :$parent_id,
                        'parents' => $parent['parents'] ? $parent_id.','.$parent['parents'] :$parent_id
                    );

                    foreach($filter['member_id'] as $current_member_id){
                        //同步该会员所有直属下级的上上级关系
                        $children_new_parent_path =array('parent_path' => $current_member_id.','.$parent_id) ;
                        if(false == $this ->app ->model('member_relation') ->update($children_new_parent_path ,array('parent_id' => $current_member_id ,'relation_change' =>'1'))){
                            $this->end(false,'用户关系更新失败，error001');
                        }
                        //同步该会员所有下级的全部上级关系
                        $current_member_relation = $this ->app ->model('member_relation') ->getRow('*' ,array('member_id' =>$current_member_id));
                        $old_parents = $current_member_relation['parents'] ? $current_member_id.','.$current_member_relation['parents'] :$current_member_id;
                        $children_new_parents = $update['parents'] ? $current_member_id.','.$update['parents'] :$current_member_id;
                        $sql = "UPDATE vmc_commission_member_relation SET parents = REPLACE(parents ,'{$old_parents}' ,'{$children_new_parents}') WHERE relation_change ='1' AND CONCAT(',', parents, ',')  LIKE '%,{$current_member_id},%' ";
                        if (false == vmc::database()->exec($sql)) {
                            $this->end(false,'用户关系更新失败，error003');
                        };
                    }
                    if(!$mdl_member_relation->update($update,$filter)){
                        $this->end(false,'修改失败');
                    }
                    $this->end(true,'保存成功');
                }else{
                    $this->end(false,'请填写正确上级帐号');
                }
                break;
        }

    }


    /*
     * 分配下级
     */
    public function  assign_children(){
        if($_GET['nums']<1){
            $this ->splash('error' ,'' ,"请填写要分配的人数");
        }
        $re = $this ->app ->model('member_relation') ->set_children($_GET['mid'] ,$_GET['nums'] ,$msg);
        if($re){
            $this ->splash('success' ,'' ,"操作成功");
        }else{
            $this ->splash('error' ,'' ,$msg);
        }
    }
}