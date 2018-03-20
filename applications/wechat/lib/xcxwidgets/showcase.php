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


class wechat_xcxwidgets_showcase extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'showcase',
            'title'=>'智能橱窗',
            'desc'=>'多种橱窗展示风格,满足个性化展现',
            'icon'=>'',
            'order'=>7,
            'default'=>array(
                'cols'=> 'col-1-2', //-3
                'has_gap'=> true,
                "items" => array()
            )
        );
    }

}
