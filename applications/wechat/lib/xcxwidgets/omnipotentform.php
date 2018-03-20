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


class wechat_xcxwidgets_omnipotentform extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface
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
            'name'=>'omnipotentform',
            'title'=>'万能表单',
            'desc'=>'万能表单挂件',
            'order'=>11,
            'default'=>array(
                "filter" => array(),
                "items" => array(),
                // "checkbox_color" => '#1AAD19',
                // "btn_width" => 200,
                // "btn_height" => 35,
                // "btn_bgcolor" => '#ffffff',
                // "btn_border_color" => '#ccc',
                // "btn_color" => '#000000',
                // "btn_fontSize" => 16,
                // "btn_align" => 'center',
                // "btn_content" => '提交',
                // "btn_radius" => 4,
                "is_show_form_name" => true,
                // "form_name_color" => '#000000',
                // "form_name_size" => 16,
                "form_name_iscenter" => false
                // "form_item_title_size" => 16
            )
        );
    }

}
