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


class wechat_xcxwidgets_slider extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
        return array(
            'name'=>'slider',
            'title'=>'广告图片轮播',
            'desc'=>'可以轮播多张广告图片',
            'icon'=>'',
            'order'=>2,
            'default'=>array(
                "indicator_dots"=> true,
                "autoplay"=> true,
                "interval"=> 5000,
                "duration"=> 500,
                "circular"=> true,
                "height"=> 160,
                "items" => array()
            )
        );
    }

}
