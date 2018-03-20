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
class ubalance_mdl_fundlog extends dbeav_model
{

    public $defaultOrder = array(
        'opt_time DESC',
    );
    function modifier_member_id($col)
    {
        if (!$col) {
            return ('非会员顾客');
        } else {
            return vmc::singleton('b2c_user_object')->get_member_name(null, $col);
        }
    }

    public function searchOptions()
    {
        $columns = parent::searchOptions();
        $columns['member_login_name'] = '会员帐号';
        return $columns;
    }

    function _filter($filter,$tableAlias=null,$baseWhere=null){
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
        $filter = parent::_filter($filter);
        return $filter;
    }


}
