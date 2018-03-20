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


class wechat_xcxwidgets_grouppresale extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'grouppresale',
            'title'=>'团购预售',
            'desc'=>'配置促销活动',
            'icon'=>'',
            'order'=>15,
            'default'=>array(
            	'title'=>'',
                'filter_type'=>'group_id',//group_id,presale_id
                'display_type'=>'normal',//card
                "filter" => array(
                    'group_id' => false,
                    'presale_id' => false,
                ),
                "imgSrc"=>null,
                'bgColor'=>'#db0000',
                'items' => array()
            )
        );
    }

}
