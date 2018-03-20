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


class wechat_xcxwidgets_goodslist extends wechat_xcxwidgets_top_abstract implements wechat_xcxwidgets_top_interface {

    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app) {
        parent::__construct($app);
    }

    public function getConfig() {
        return array(
            'name' => 'goodslist',
            'title' => '商品列表',
            'desc' => '多种商品展示风格',
            'icon' => '',
            'order' => 4,
            'default' => array(
                "title" => "商品列表",
                "show_title" => false,
                "with_panel" => true,
                "type" => "swiper",
                "card_style" => false,
                "swiper_height" => 150,
                "swiper_item_margin" => 10,
                "filter_type" => 'goods_id',
                "filter" => array(
                    'goods_id' => false,
                    'cat_id' => false,
                    'brand_id' => false,
                    'tag_name' => false,
                    'collection_id' => false,
                ),
                "orderby" => false,
                "limit" => 9,
                "show_product_title" => true,
                "show_product_price" => true,
                "show_product_brief" => false,
                "items" => array()
            )
        );
    }

}
