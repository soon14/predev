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
class ubalance_mdl_account extends dbeav_model
{
    var $has_tag = true;
    /**
     * 重写搜索的下拉选项方法.
     *
     * @param null
     */
    public function searchOptions()
    {
        $arr = parent::searchOptions();
        $columns = array();
        /* 添加登录帐号搜索 **/
        $columns['member_login_name'] = ('会员帐号');
        /* end **/
        $return = array_merge($columns, $arr);

        return $return;
    }

    public function getRow($cols = '*', $filter = array(), $orderType = null)
    {
        $data = parent::getRow($cols, $filter, $orderType);
        if (!$data && $filter['member_id'] && count($filter) == 1) {
            return $this->create_account($filter['member_id']);
        }

        return $data;
    }

    public function create_account($member_id)
    {
        $data = array(
            'member_id' => $member_id,
            'status' => '1',
            'last_modify' => time(),
        );
        $this->insert($data);

        return $data;
    }


    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        if (isset($filter) && $filter && is_array($filter) && array_key_exists('member_login_name', $filter))
        {
            $obj_pam_account = app::get('pam')->model('members');
            $pam_filter = array(
                'login_account|has'=>$filter['member_login_name'],
            );
            $row_pam = $obj_pam_account->getList('*',$pam_filter);
            $arr_member_id = array();
            if ($row_pam)
            {
                foreach ($row_pam as $str_pam)
                {
                    $arr_member_id[] = $str_pam['member_id'];
                }
                $filter['member_id|in'] = $arr_member_id;
            }
            else
            {
                if ($filter['member_login_name'] == ('非会员顾客'))
                    $filter['member_id'] = 0;
            }
            unset($filter['member_login_name']);
        }
        if($filter['member_lv_id']) {
            $mdl_members = app::get('b2c')->model('members');
            if($member_ids = $mdl_members->getList('member_id', array('member_lv_id' => $filter['member_lv_id']))) {
                $member_ids = array_keys(utils::array_change_key($member_ids,'member_id'));
            };
            $filter['member_id'] = array_merge($member_ids,(array)$filter['member_id']);
            unset($filter['member_lv_id']);
        }
        return parent::_filter($filter, $tableAlias, $baseWhere);
    }
}
