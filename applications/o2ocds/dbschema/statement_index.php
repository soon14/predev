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


$db['statement_index'] = array(
    'columns' => array(
        'index_id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('序号'),
        ),
        'statement_id' => array(
            'type' => 'table:statement',
            'required' => true,
            'label' => ('结算单ID'),
        ),
        'achieve_id' => array(
            'type' => 'table:orderlog_achieve',
            'required' => true,
            'label' => ('结算凭证'),
        ),
    ),
    'index' => array(
        'ind_statement' => array(
            'columns' => array(
                0 => 'statement_id',
            ),
        ),
    ),
    'comment' => ('结算单明细表'),
);
