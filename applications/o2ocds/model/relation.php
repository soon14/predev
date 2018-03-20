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


class o2ocds_mdl_relation extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }


    public function modifier_member_id($col) {
        if($members = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$col))) {
            return $members['login_account'];
        };
        return '';
    }
    public function _filter($filter, $tableAlias = null, $baseWhere = null){
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
        return parent::_filter($filter,$tableAlias,$baseWhere);
    }

    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                $columns[$k] = $v['label'];
            }
        }
        /** 添加用户名搜索 **/
        $columns['member_login_name'] = ('会员用户名');
        return $columns;
    }

}