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


class wechat_xcxwidgets_articlelist extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
{
    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function getConfig()
    {
    	$test=[['src'=>"https://image.vmcshop.com/89/79/7290d01eef24.jpg!m?93065_OW1439_OH500",'title'=>'1'],
            ['src'=>"https://image.vmcshop.com/89/79/7290d01eef24.jpg!m?93065_OW1439_OH500",'title'=>'1'],
            ['src'=>"https://image.vmcshop.com/89/79/7290d01eef24.jpg!m?93065_OW1439_OH500",'title'=>'1']];
        return array(
            'name'=>'articlelist',
            'title'=>'文章列表',
            'desc'=>'多种文章列表展示风格,满足个性化展现',
            'icon'=>'',
            'order'=>12,
            'default' => array(
                "title" => "文章列表",
                "type" => "gallery",
                "swiper_height" => 80,
                "swiper_width" => 80,
                "mode" => "aspectFit",
                "filter_type" => 'column_id',
                "filter" => array(
                    'column_id' => false,
                    'content_id' => false,
                ),
                "orderby" => false,
                "limit" => 9,
                "items" => array()
            )
        );
    }

}
