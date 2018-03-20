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


class productplus_finder_products
{

    public $column_control = '操作';
    public $column_product_pic = '缩略图';
    public $column_product_qrcode = '二维码';
    public $column_product_pic_order = COLUMN_IN_HEAD;
    public $column_product_pic_order_field = 'image_id';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_product_qrcode($row)
    {
        $pid = $row['product_id'];

        return "<a target='_blank' class='btn btn-xs btn-default' href='index.php?app=productplus&ctl=admin_products&act=qrcode&p[0]=$pid&singlepage=1'><i class='fa fa-qrcode'></i></a>";
    }

    public function column_product_pic($row)
    {
        //$img_src = base_storager::image_path($row['@row']['image_default_id'], 'xs');
        $img_src = base_storager::modifier($row['@row']['image_id'], 'xs');
        //使用modifier 让smarty统一渲染，减少数据库查询
        if (!$img_src) {
            return '';
        }

        return "<img class='img-thumbnail' src='$img_src' style='height:30px;'>";
    }

    public function column_control($row)
    {
        $url_preview = app::get('site')->router()->gen_url(array('app' => 'b2c', 'ctl' => 'site_product', 'args' => array($row['product_id'])));
        $returnValue ='';
        if(vmc::singleton('desktop_user') ->has_permission('productplus_edit')){
            $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=productplus&ctl=admin_products&act=edit&p[0]='.$row['product_id'].'"><i class="fa fa-edit"></i>编辑</a>';
        }
        $returnValue .='<a class="btn btn-default btn-xs" target="_blank" href="'.$url_preview.'"><i class="fa fa-external-link"></i> 浏览</a>';
        return $returnValue;
    }



    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
