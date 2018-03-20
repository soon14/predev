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


class vshop_finder_shop
{
    public $column_control = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        if (vmc::singleton('desktop_user')->has_permission('vshop_edit')) {
            $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=vshop&ctl=admin_shop&act=edit&p[0]=' . $row['shop_id'] . '"><i class="fa fa-edit"></i>编辑</a>';
        }
        return $returnValue;
    }
    public function row_style($row)
    {
        $row = $row['@row'];
        switch ($row['status']) {
            case 'validate':
                return ' text-warning';
                break;
            case 'active':
                return ' text-success';
                    break;
            case 'pause':
                return ' text-error';
                break;
        }
    }
}
