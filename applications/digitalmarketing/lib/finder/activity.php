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
class  digitalmarketing_finder_activity{
    var $column_edit = '操作';
    public function column_edit($row){
        if (vmc::singleton('desktop_user')->has_permission('digitalmarketing_edit')) {
            return '<a class="btn btn-default btn-xs" href="index.php?app=digitalmarketing&ctl=admin_activity&act=edit&p[0]=0&p[1]='.$row['activity_id'].'" ><i class="fa fa-edit"></i> '.('编辑').'</a>';
        }
    }
}