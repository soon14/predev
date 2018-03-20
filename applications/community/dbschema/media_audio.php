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


$db['media_audio'] = array(
  'columns' => array(
    'audio_id' => array(
      'type' => 'char(32)',
      'pkey' => true,
    ),
    'target_id' => array(
      'type' => 'bigint unsigned',
      'required' => true,
      'comment' => '网志ID',
    ),
    'host_mirror' => array(
        'type' => 'varchar(255)',
        'required' => true,
        'label' => '域',
    ),
    'ident' => array(
        'type' => 'varchar(200)',
        'required' => true,
    ) ,
    'duration' => array(
        'type' => 'number',
    ) ,
    'url_path' => array(
        'type' => 'varchar(255)',
        'required' => true,
        'label' => '路径',
    ),
    'last_modified' => array(
      'label' => '更新时间',
      'type' => 'last_modify',
    ),
  ),
  'index' => array(
    'ind_target' => array(
      'columns' => array(
        0 => 'target_id',
      ),
    ),
  ),
  'comment' => '音频关联表'
);
