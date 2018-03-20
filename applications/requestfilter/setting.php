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

/**
 *  getConf 默认值.
 */
$setting = array(
    'xss_filter' => array(
        'type' => 'select',
        'options' => array(
            'false' => '不启用',
            'true' => '启用',
        ),
        'default' => 'false',
        'desc' => ('是否启用XSS防护'),
    ) ,
    'csrf_filter' => array(
        'type' => 'select',
        'options' => array(
            'false' => '不启用',
            'true' => '启用',
        ),
        'default' => 'false',
        'desc' => ('是否启用CSRF防护'),
    ) ,

);
