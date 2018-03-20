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

$db['relation'] = array(
    'columns' => array(
        'relation_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '关联ID',
        ),
        'member_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '会员账号',
            'comment' => '会员id',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'type' => array(
            'type' => array(
                'store' => '店铺',
                'enterprise' => '企业',
            ),
            'required' => true,
            'label' => '类型',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'relation' => array(
            'type' => array(
                'admin' => '管理员',
                'salesman' => '业务员',
                'manager' => '店长',
                'salesclerk' => '店员',
            ),
            'required' => true,
            'label' => '身份',
            'comment' => '关系',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'time' => array(
            'type' => 'time',
            'label' => '绑定时间',
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ),
        'last_modified' => array(
            'label' => ('最后更新时间') ,
            'type' => 'last_modify',
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
    ),
    'index' => array(
        'relation_unique' => array(
            'columns' => array(
                0 => 'relation_id',
                1 => 'member_id',
                2 => 'type',
            ),
            'prefix' => 'unique',
        ),
    )
);

