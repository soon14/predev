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
class marketing_finder_message_tmpl{
    var $column_control = '操作';

    public function column_control($row){
        $return ='<a href="index.php?app=marketing&ctl=admin_message&act=edit_tmpl&p[0]=' . $row['message_type'] . '&p[1]='.$row['tmpl_id'].'" class="btn btn-xs btn-default"><i class="fa fa-edit"></i> 编辑</a>';
        return $return;
    }
}