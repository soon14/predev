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


class wechat_xcxwidgets_imagetext extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'imagetext',
            'title'=>'图文混合',
            'desc'=>'图文混合展示风格,满足个性化展现',
            'icon'=>'',
            'order'=>8,
            'default'=>array(
                'type'=> 'type_3', //type_1,2,3,4
                'isScroll'=> false,
                'showTitle'=>true,
                'showContent'=>true,
                'borderRadius'=>false,
                'title' => '',
                'titleColor' => '#000000',
                'titleSize' => 18,
                'content' => '',
                'contentColor' => '#000000',
                'contentSize' => 15,
                'letterSpacing' => 3,
                'lineHeight' => 1.5,
                'title_align' => 'center',
                'imageWidth' => 120,
                'imageHeight' => 120,
                'items' => array()
            )
        );
    }

}
