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


class adjustprices_finder_plan
{


    public $column_control = '操作';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $url_preview = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_collection','args'=>array($row['collection_id'])));

        if (vmc::singleton('desktop_user')->has_permission('adjustprices_edit')) {
            $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=adjustprices&ctl=admin_plan&act=edit&p[0]='.$row['plan_id'].'"><i class="fa fa-edit"></i> 编辑</a>';

        }

        if (vmc::singleton('desktop_user')->has_permission('adjustprices_status_edit')) {
            $returnValue .= '<a class="btn btn-default btn-xs" href="index.php?app=adjustprices&ctl=admin_plan&act=edit_products&p[0]='.$row['plan_id'].'"><i class="fa fa-diamond"></i> 限时降价货品管理</a>';

        }
        return $returnValue;
    }



    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
