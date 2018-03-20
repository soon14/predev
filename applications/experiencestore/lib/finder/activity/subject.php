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


class experiencestore_finder_activity_subject
{
    public $column_control = '操作';
    public $column_image = '图标';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $url_preview = app::get('site')->router()->gen_url(array('app' => 'experiencestore', 'ctl' => 'site_activity','act'=>'subject', 'args' => array($row['id'])));

        $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=experiencestore&ctl=admin_activity&act=edit_subject&p[0]='.$row['id'].'"><i class="fa fa-edit"></i>编辑</a><a class="btn btn-default btn-xs" target="_blank" href="'.$url_preview.'"><i class="fa fa-external-link"></i> 浏览</a>';

        return $returnValue;
    }

    public function column_image($row)
    {
        $img_src = base_storager::modifier($row['@row']['default_image_id'], 's');
        //使用modifier 让smarty统一渲染，减少数据库查询
        if (!$img_src) {
            return '';
        }

        return "<img class='img-thumbnail' src='$img_src' style='height:80px;width:150px;'>";
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
