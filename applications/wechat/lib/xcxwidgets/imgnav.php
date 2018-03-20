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


class wechat_xcxwidgets_imgnav extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'imgnav',
            'title'=>'图片导航',
            'desc'=>'可以用图片形式建设导航链接',
            'icon'=>'',
            'order'=>3,
            'default'=>array(
                "limit"=> 4,
                "img_height"=> 50,
                "img_width"=> 50,
                "bottom_margin"=> 10,
                "show_text"=> true,
                "items" => array()
            )
        );
    }

}
