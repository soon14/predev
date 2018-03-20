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
class commission_mdl_member_relation extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /*
     * 检查域名是否可用
     */
    public function check_domain($domain){
        if($domain == ''){
            throw new Exception('请输入域名');
        }
        $domain = strtolower($domain);
        if(preg_match('/^[\w-]+$/' ,$domain) == 0){
            throw new Exception('域名格式不正确');
        }
        foreach(file($this ->app->app_dir.'/keep_domain') as $row){
            $domain_arr = explode(" " , trim($row));
            if(in_array($domain ,$domain_arr ,true)){
                throw new Exception('该域名已存在');
            }
        }
        $is_exit = $this ->count(array('domain_pre' => $domain));
        if($is_exit){
            throw new Exception('该域名已存在');
        }
    }

    /*
     * 手动为特权用户分配下级
     */
    public function set_children($parent_id ,$nums =0 ,&$msg=''){
        $current_member = app::get('b2c') ->model('members') ->dump(array('member_id' => $parent_id));
        $member_lv = app::get('b2c')->model('member_lv')->getList("*", array('disabled' => 'false'));
        $member_lv = utils::array_change_key($member_lv ,'member_lv_id');
        $sql = "SELECT A.member_id FROM vmc_b2c_members AS A JOIN vmc_b2c_member_lv AS B ON A.member_lv_id=B.member_lv_id WHERE B.experience< {$member_lv[$current_member['member_lv']['member_group_id']]['experience']}";
        $re = vmc::database() ->select($sql);
        if(!$re){
            $msg = "没有比该用户等级低的用户";
            return false;
        }

        $count = $this ->count(array('member_id' => array_keys(utils::array_change_key($re ,'member_id')) ,'parent_id'=>array(0,-1) ));
        if($count<$nums){
            $msg = "无上级的会员数量不足，只剩{$count}个";
            return false;
        }
        $parent= $this ->getRow("*" ,array('member_id' =>$parent_id));
        if(!$parent){
            $parent = array('member_id' => $parent_id);
            $this->save($parent);
        }
        $parent_path = $parent['parent_id']  ?$parent_id.','.$parent['parent_id'] :$parent_id;
        $parents = $parent['parents'] ? $parent_id.','.$parent['parents'] :$parent_id;
        $member_ids = implode(',' , array_keys(utils::array_change_key($re ,'member_id')));
        $sql = "UPDATE `vmc_commission_member_relation` SET `parent_id`=$parent_id,`parent_path`='$parent_path',`parents`='$parents' WHERE (`parent_id`=0 OR `parent_id`=-1) AND `member_id` in ({$member_ids}) LIMIT $nums";
        if(false == vmc::database() ->exec($sql)){
            $msg = "操作失败";
            return false;
        }
        return true;
    }

    public function modifier_member_id($col){
        $member = vmc::singleton('b2c_user_object')->get_members_data(array('account' => 'login_account'), $col);
        return $member['account']['login_account'];
    }

    public function modifier_parent_id($col){
        if($col >0){
            $member = vmc::singleton('b2c_user_object')->get_members_data(array('account' => 'login_account'), $col);
            return $member['account']['login_account'];
        }else{
            return "系统会员";
        }

    }

    public function modifier_parents($col){
        if(!$col){
            return "系统会员";
        }
        $parents = explode("," ,$col);
        if(!empty($parents)){
            $result = '';
            foreach($parents as $k => $v){
                $member = vmc::singleton('b2c_user_object')->get_members_data(array('account' => 'login_account'), $v);
                $url_preview = 'index.php?app=commission&ctl=admin_member&act=index&p[0]='.$v.'&p[1]=0';
                $result .= "<<a href='$url_preview'>{$member['account']['login_account']}</a>";

            }
            return $result;
        }else{
            return "系统会员";
        }

    }

}