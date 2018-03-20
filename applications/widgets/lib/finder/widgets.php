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



class widgets_finder_widgets{
    public function __construct($app)
    {
        $this->app = $app;
    }
    var $column_control = '操作';
    public function column_control($row)
    {
        if(vmc::singleton('desktop_user') ->has_permission('widgets_edit')){
            return "<a href='index.php?app=widgets&ctl=admin_index&act=edit&p[0]=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i>编辑</a>"
            ."<a href='index.php?app=widgets&ctl=admin_index&act=copy&p[0]=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-copy'></i>复制</a>";
        }

    }
}
