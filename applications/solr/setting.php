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

$setting = array(
    'solr_host' => array(
        'type' => 'text',
        'default' => '127.0.0.1',
        'required' => true,
        'desc' => 'Solr Host' ,
    ) ,
    'solr_port' => array(
        'type' => 'text',
        'default' => '8983',
        'required' => true,
        'desc' => 'Solr Port' ,
    ) ,
    'solr_path' => array(
        'type' => 'text',
        'default' => '/solr/',
        'required' => true,
        'desc' => 'Solr Path' ,
    ) ,
);
