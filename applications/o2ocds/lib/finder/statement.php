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


class o2ocds_finder_statement
{
    public $column_control = '操作';
    public $column_name = '企业/店铺名称';
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function column_control($row)
    {
        $returnValue = '';
        if(vmc::singleton('desktop_user')->has_permission('o2ocds_edit_statement')){
            $returnValue .= '<a class="btn btn-default btn-xs" href="index.php?app=o2ocds&ctl=admin_statement&act=edit&p[0]='.$row['statement_id'].'"><i class="fa fa-edit"></i> 查看\编辑</a>';
        }
        return $returnValue;
    }

    public function column_name($row) {
        if($row['relation_type'] == 'store') {
            if($store = $this->app->model('store')->getRow('name',array('store_id'=>$row['@row']['relation_id']))) {
                return $store['name'];
            };
        }else{
            if($enterprise = $this->app->model('enterprise')->getRow('name',array('enterprise_id'=>$row['@row']['relation_id']))) {
                return $enterprise['name'];
            };
        }
        return '';
    }

     public function row_style($row)
     {
         //$row = $row['@row'];
     }
}
