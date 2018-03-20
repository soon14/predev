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


class supplier_finder_notice
{


    public $column_control = '操作';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=supplier&ctl=admin_notice&act=edit&p[0]='.$row['notice_id'].'"><i class="fa fa-edit"></i> 编辑</a>';
        return $returnValue;
    }



    // public function row_style($row)
    // {
    //     //$row = $row['@row'];
    // }
}
