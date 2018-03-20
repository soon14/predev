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

$db['participate_member'] = array(
    'columns' => array(
        'presell_id' => array(
            'type' => 'table:orders',
            'required' => true,
            'pkey' => true,
            'comment' => '预售单',
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => '用户名',
            'comment' => '会员id',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'activity_id' => array(
            'type' => 'table:activity',
            'label' => '预售活动id',
        ),
        'createtime' => array(
            'label' => ('参与时间') ,
            'type' => 'time',
            'required' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),

    )

);