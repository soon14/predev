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


class ubalance_finder_account
{
    public $column_control = '操作';
    public $column_account = '会员帐号';

    public function __construct($app)
    {
        $this->app = $app;
    }


    public function column_account($row)
    {
        if (!$row['member_id']) {
            return ('非会员顾客');
        } else {
            return vmc::singleton('b2c_user_object')->get_member_name(null, $row['member_id']);
        }
    }

    public function column_control($row)
    {
        if(vmc::singleton('desktop_user') ->has_permission('ubalance_account_edit')){
            return '<a href="index.php?app=ubalance&ctl=admin_account&act=edit&member_id=' . $row['member_id'] . '" data-target="#member_edit" data-toggle="modal" class="btn btn-default btn-xs"><i class="fa fa-edit"></i>编辑</a>';
        }

    }


    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
