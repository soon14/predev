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


class store_finder_center{

    var $column_edit = '操作';
    function column_edit($row){
        return '<a class="btn btn-default btn-xs" href="index.php?app=store&ctl=admin_center&act=edit&p[0]='.$row['center_id'].'" ><i class="fa fa-edit"></i> '.('编辑').'</a>';
    }

}
