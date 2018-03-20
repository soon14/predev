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


class wechat_xcxwidgets_searchbar extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'searchbar',
            'title'=>'商品搜索框',
            'desc'=>'用于搜索商品',
            'icon'=>'',
            'order'=>1,
            'default'=>array(
                'show_scan' => true,
                'background' => false,
                'fixed' => false,
                'ipt_background' => '#FFFFFF',
                'input_val' => '',
                'placeholder' => '',
            )
        );
    }

}
