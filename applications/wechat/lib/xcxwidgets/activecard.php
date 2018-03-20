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


class wechat_xcxwidgets_activecard extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'activecard',
            'title'=>'活动卡片',
            'desc'=>'配置活动场次',
            'icon'=>'',
            'order'=>13,
            'default'=>array(
                'isShowTitle'=> true,
                'isShowTime'=> true,
                'isShowAddress'=> true,
                'isShowRadius'=> true,
                'isShowGap'=>true,
                'isShowBrief'=>true,
                'title' => '',
		        'time' => '',
		        'address' => '',
		        'brief'=>'',
                'filter' => array(
                    'activity_id' => false,
                ),
                'items' => array()
            )
        );
    }

}
