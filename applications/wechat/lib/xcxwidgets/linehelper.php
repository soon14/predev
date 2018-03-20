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


class wechat_xcxwidgets_linehelper extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'linehelper',
            'title'=>'辅助线条',
            'desc'=>'实线、点线、虚线',
            'icon'=>'',
            'order'=>5,
            'default'=>array(
                "line_color"=>"#E9E9E9",
                "padding"=>false,
                "type"=>'solid'
            )
        );
    }

}
