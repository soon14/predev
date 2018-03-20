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

return array(
    // 默认
    '__' => array(
        'fields' => array(
            'corp_id' => 'corp_id',
            'corp_code' => 'corp_code',
            'corp_name' => 'name',
            'website' => 'website',
            'request_url' => 'request_url',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // delivery.write.add - 添加物流公司 
    'write_add' => array(
        'fields' => array(
            'corp_id' => 'corp_id',
            'create_time' => 'create_time',
        ),
        'input' => array(
            'corp_name' => 'name',
            'corp_code' => 'corp_code',
            'website' => 'website',
            'request_url' => 'request_url',
        ),
        'output' => array(
        ),
    ),
    // delivery.write.edit - 编辑物流公司 
    'write_edit' => array(
        'fields' => array(
            'corp_id' => 'corp_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'corp_id' => 'corp_id',
            'corp_name' => 'name',
            'corp_code' => 'corp_code',
            'website' => 'website',
            'request_url' => 'request_url',
        ),
        'output' => array(
        ),
    ),
    // delivery.write.delete - 删除物流公司 
    'write_delete' => array(
        'fields' => array(
            'corp_id' => 'corp_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'corp_id' => 'corp_id',
        ),
        'output' => array(
        ),
    ),
    // delivery.read.get - 获取物流公司 
    'read_get' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
);
