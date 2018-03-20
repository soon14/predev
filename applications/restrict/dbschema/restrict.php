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


$db['restrict'] = array(
    'columns' => array(
        'res_id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => '限购ID',
        ),
        'res_name' => array(
            'type' => 'varchar(20)',
            'label' => ('限购名称'),
            'comment' => ('限购名称'),
            'required' => true,
            'searchtype' => 'has',
            'is_title' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'res_description' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'default' => '',
            'label' => ('限购描述'),
            'in_list' => true,
        ),
        'default_order' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('订单最多购买数量'),
            'comment' => ('订单最多购买数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'default_day_times' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('用户每天最多购买次数'),
            'comment' => ('用户每天最多购买次数'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'default_day_member' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('用户每天最多购买数量'),
            'comment' => ('用户每天最多购买数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'default_member' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('用户最多购买数量'),
            'comment' => ('用户最多购买数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'default_sum' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('总数量'),
            'comment' => ('总数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'res_orderby' => array(
            'type' => 'number',
            'default' => 0,
            'label' => ('权重'),
            'orderby' => true,
            'in_list' => true,
        ),
        'goods_id' => array(
            'type' => 'serialize',
            'default' => '',
            'label' => ('商品'),
        ),
        'product_id' => array(
            'type' => 'serialize',
            'default' => '',
            'label' => ('货品'),
        ),
        'status' => array(
            'type' => array(
                '1' => ('是'),
                '0' => ('否'),
            ),
            'default' => '0',
            'filtertype' => 'normal',
            'label' => ('是否启用中'),
            'in_list' => true,
        ),
        'from_time' => array(
            'type' => 'time',
            'label' => ('开始时间'),
            'orderby' => true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'to_time' => array(
            'type' => 'time',
            'label' => ('结束时间'),
            'orderby' => true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => ('添加时间'),
            'orderby' => true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间'),
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ),
    ),
    'comment' => ('限制购买主表'),
    'engine' => 'innodb',
);
