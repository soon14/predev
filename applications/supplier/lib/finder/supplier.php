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


class supplier_finder_supplier
{


    public $column_control = '操作';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $returnValue = '';
        if(vmc::singleton('desktop_user') ->has_permission('supplier_edit')){
            $returnValue .= '<a class="btn btn-default btn-xs" href="index.php?app=supplier&ctl=admin_supplier&act=edit&p[0]='.$row['supplier_id'].'"><i class="fa fa-edit"></i> 编辑</a>';
        }
        if(vmc::singleton('desktop_user') ->has_permission('supplier_relgoods')) {
            $returnValue .= '<a class="btn btn-default btn-xs" href="index.php?app=supplier&ctl=admin_relgoods&act=index&supplier_id=' . $row['supplier_id'] . '"><i class="fa fa-diamond"></i> 供应商商品管理</a>';
        }
        return $returnValue;
    }



    // public function row_style($row)
    // {
    //     //$row = $row['@row'];
    // }
}
