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


class community_finder_userlv
{
    public $column_edit = '编辑';
    public function column_edit($row)
    {
        //if (vmc::singleton('desktop_user')->has_permission('memberlv_edit')) {
            $return = '<a class="btn btn-default btn-xs" href="index.php?app=community&ctl=admin_user_lv&act=addnew&p[0]='.$row['user_lv_id'].'" ><i class="fa fa-edit"></i>'.('编辑').'</a>';
        if (!$row['default_lv']) {
            $return .= '<a class="btn btn-xs btn-default" target="_command" href="index.php?app=community&ctl=admin_user_lv&act=setdefault&p[0]='.$row['user_lv_id'].'">'.('设为默认等级').'</a>';
        } else {
            $return .= '<span class="label label-default"><i></i> 默认等级</span>';
        }

        return $return;
        //}
    }

    public function row_style($row)
    {
        if ($row['default_lv']) {
            return 'active';
        }
    }
}
