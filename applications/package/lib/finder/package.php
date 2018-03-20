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



class package_finder_package{
    public function __construct($app)
    {
        $this->app = $app;
    }
    var $column_control = '操作';
    public function column_control($row)
    {
        $edit_html = "<a href='index.php?app=package&ctl=admin_package&act=edit&p[0]=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i>编辑</a>";
        return $edit_html;
    }
}
