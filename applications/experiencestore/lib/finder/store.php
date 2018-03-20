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


class experiencestore_finder_store
{
    public $column_control = '操作';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $url_preview = app::get('site')->router()->gen_url(array('app' => 'experiencestore', 'ctl' => 'site_store','act'=>'detail', 'args' => array($row['id'])));

        $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=experiencestore&ctl=admin_store&act=edit&p[0]='.$row['id'].'"><i class="fa fa-edit"></i>编辑</a>';

        return $returnValue;
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
