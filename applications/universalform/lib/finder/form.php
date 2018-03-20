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


class universalform_finder_form
{
    public $column_control = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        if(vmc::singleton('desktop_user') ->has_permission('universalform_edit_form')){
            return '<a href="index.php?app=universalform&ctl=admin_form&act=edit&p[0]=' . $row['form_id'] . '"  class="btn btn-default btn-xs"><i class="fa fa-edit"></i>编辑</a>';
        }
        return '';
    }


    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
