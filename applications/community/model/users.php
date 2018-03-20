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


class community_mdl_users extends dbeav_model
{
    public $idColumn = 'user_id'; //表示id的列
    public $textColumn = 'nickname';
    public $has_tag = true;
    public function get_user_bymember($member_id)
    {
        $user = parent::dump(array('member_id'=>$member_id));
        $member_info = vmc::singleton('b2c_user_object')->get_member_info($member_id);
        if(empty($user)){
            if(!$member_info)return false;
            $mdl_user_lv = $this->app->model('user_lv');
            $defaule_user_lv = $mdl_user_lv->get_default_lv();
            $new_user = array(
                'member_id' => $member_info['member_id'],
                'user_lv_id' => $defaule_user_lv,
                'nickname' => $member_info['name'] ? $member_info['name'] : $member_info['uname'],
            );
            $this->insert($new_user);
            $_return = array_merge($member_info,$new_user);
            logger::alert($new_user['user_id']);
            logger::alert($_return);
            return $_return;
        }
        return array_merge($member_info,$user);
    }

    public function getListPlus($cols='*',$filter = array(), $offset = 0, $limit = -1, $orderType = null){
        $user_list = parent::getList($cols,$filter,$offset,$limit,$orderType);
        if(empty($user_list)){
            return false;
        }
        $mdl_members = app::get('b2c')->model('members');
        $member_id_arr = array_keys(utils::array_change_key($user_list,'member_id'));
        $member_list = $mdl_members->getList('*',array('member_id'=>$member_id_arr));
        $member_list = utils::array_change_key($member_list,'member_id');
        foreach ($user_list as &$row) {
            $row = array_merge($member_list[$row['member_id']],$row);
        }
        return $user_list;
    }
    public function update_user_count($user_id,$target_col,$val){
        if(empty($user_id)){
            return false;
        }
        //$dbeav_filter = vmc::singleton('dbeav_filter');
        $table_name = $this->table_name(true);
        //$where_str = $dbeav_filter->dbeav_filter_parser($filter, null, null, $this);
        $SQL = "UPDATE $table_name SET $target_col = (IFNULL($target_col,0) + $val) WHERE user_id = $user_id";
        return $this->db->exec($SQL);
    }
}
