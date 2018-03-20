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



$db['member_sso']=array (
    'columns' =>
        array (
            'sso_uid' =>
                array (
                    'type' => 'bigint unsigned',
                    'required' => true,
                    'pkey' => true,
                    'label' => ('sso_uid'),
                    'comment' => ('sso_uid'),
                ),

            'member_id' =>
                array (
                    'type' => 'table:members@b2c',
                    'required' => true,
                    'comment' => ('member_id'),
                ),
        ),
    'comment' => ('sso'),
);
