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


$db['member_lv'] = array(
    'columns' =>
        array(
            'res_id' => array(
                'type' => 'table:restrict',
                'required' => true,
                'label' => ('限购ID'),
            ),
            'member_lv_id' =>
                array(
                    'type' => 'number',
                    'required' => true,
                    'label' => 'ID',
                    'width' => 110,
                    'in_list' => false,
                    'default_in_list' => false,
                ),

        ),
    'engine' => 'innodb',
    'comment' => ('会员等级表'),
);
