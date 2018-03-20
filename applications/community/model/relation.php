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


class community_mdl_relation extends dbeav_model
{

    public function getRelationList($user_id)
    {
        $SQL = 'SELECT r.relation_id,u.member_id,u.user_id,u.nickname,u.sign,u.follow_count,m.avatar,m.member_id,m.name FROM vmc_community_relation AS r LEFT JOIN vmc_community_users AS u ON r.relation_id = u.user_id LEFT JOIN vmc_b2c_members AS m ON u.member_id = m.member_id WHERE r.user_id = '.$user_id;
        return $this->db->select($SQL);
    }


}
