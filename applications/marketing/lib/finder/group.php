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
class marketing_finder_group{
    var $column_control = '操作';

    public function column_control($row){
        $return ='<a href="index.php?app=marketing&ctl=admin_group&act=edit_group&p[0]=' . $row['group_id'] . '" class="btn btn-xs btn-default"><i class="fa fa-edit"></i> 编辑</a>';
        $return .='<a target="_command" href="index.php?app=marketing&ctl=admin_group&act=count&p[0]=' . $row['group_id'] . '" class="btn btn-xs btn-default"><i class="fa fa-refresh"></i> 重新统计</a>';
        return $return;
    }
}