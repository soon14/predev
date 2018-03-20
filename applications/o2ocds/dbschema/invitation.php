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

$db['invitation'] = array(
    'columns' => array(
        'invitation_code' => array(
            'type' => 'char(6)',
            'required' => true,
            'label' => '邀请码',
            'in_list' => true,
            'pkey' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'op_id' => array(
            'type' => 'number',
            'label' => '管理员',
        ),
        /*'type' => array(
            'type' => array(
                '0' => '邀请店铺',
                '1' => '邀请业务员',
                '2' => '邀请店员',
            ),
            'default' => '0',
            'label' => '发行种类',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),*/
        'enterprise_id' => array(
            'type' => 'number',
            'label' => '关联企业',
            'comment' => '企业ID',
            'default' => '0'
        ),
        'store_id' => array(
            'type' => 'number',
            'label' => '关联店铺',
            'comment' => '店铺ID',
            'default' => '0'
        ),
        'member_lv_id' => array(
            'required' => true,
            'default' => 0,
            'label' => ('会员等级') ,
            'sdfpath' => 'member_lv/member_group_id',

            'order' => 40,
            'type' => 'table:member_lv@b2c',
            'editable' => true,
            'filtertype' => 'bool',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'member_id' => array(
            'type' => 'number',
            'label' => '来自账号',
            'comment' => '来自会员',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'use_member_id' => array(
            'type' => 'number',
            'label' => '使用会员',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status' => array(
            'type' => array(
                '0' => '未使用',
                '1' => '已使用',
            ),
            'default' => '0',
            'label' => '状态',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'remark' => array(
            'type' => 'longtext',
            'label' => ('备注') ,
            'width' => 50,
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => '创建时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'usetime' => array(
            'type' => 'time',
            'label' => '使用时间',
            'in_list'=> true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        )
    ),
    'index' => array(
        'store_id' => array(
            'columns' => array(
                0 => 'store_id',
            ),
        ),
        'enterprise_id' => array(
            'columns' => array(
                0 => 'enterprise_id',
            ),
        ),
    ),
);