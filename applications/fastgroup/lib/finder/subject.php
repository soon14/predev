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


class fastgroup_finder_subject
{
    public $column_control = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $url_preview = app::get('site')->router()->gen_url(array('app' => 'fastgroup', 'ctl' => 'site_fastgroup','act'=>'subject', 'args' => array($row['id'])));

        $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=fastgroup&ctl=admin_subject&act=edit&p[0]='.$row['id'].'"><i class="fa fa-edit"></i>编辑</a><a class="btn btn-default btn-xs" target="_blank" href="'.$url_preview.'"><i class="fa fa-external-link"></i> 浏览</a>';

        return $returnValue;
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
