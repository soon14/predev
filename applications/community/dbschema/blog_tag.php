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

$db['blog_tag'] = array(
    'columns' => array(
        'blog_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'label' => '关联blog_id',
        ),
        'tag_name' => array(
            'type' => 'varchar(32)',
            'required' => true,
            'pkey' => true,
            'label' => '标签名',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modified' => array(
          'label' => '更新时间',
          'type' => 'last_modify',

        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('网志标签表') ,
);
