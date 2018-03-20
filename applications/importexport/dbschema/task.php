<?php

$db['task'] = array(
  'columns' => array(
    'task_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
    ),
    'name' => array(
      'type' => 'varchar(255)',
      'required' => true,
      'in_list' => true,
      'default_in_list' => true,
      'width' => 150,
      'label' => '任务名称',
    ),
    'message' => array(
      'type' => 'varchar(255)',
      'label' => '备注',
      'width' => 300,
      'in_list' => true,
      'default_in_list' => true,
    ),
      'error_content' => array(
          'type' => 'serialize',
          'label' => '错误描述',
          'comment' => '详细的错误信息'
      ),
    'filetype' => array(
      'type' => 'varchar(20)',
      'width' => 100,
      'default_in_list' => true,
      'in_list' => true,
      'label' => '文件类型',
    ),

    'create_date' => array(
      'type' => 'time',
      'label' => '创建时间',
      'width' => 150,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'complete_date' => array(
      'type' => 'time',
      'label' => '完成时间',
      'width' => 150,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'type' => array(
      'type' => array(
        'export' => '导出',
        'import' => '导入',
      ),
      'width' => 100,
      'label' => '任务类型',
    ),
    'status' => array(
      'type' => array(
        0 => '等待执行',
        1 => '正在导出',
        2 => '导出成功',
        3 => '导出失败',
        4 => '正在导入',
        5 => '导入成功',
        6 => '导入失败',
        7 => '中断',
        8 => '部分导入',
      ),
      'default' => '0',
      'in_list' => true,
      'default_in_list' => true,
      'width' => 100,
      'label' => '任务状态',
    ),
    'is_display' => array(
      'type' => array(
        0 => '隐藏',
        1 => '显示',
      ),
      'default' => '0',
      'label' => '是否显示',
    ),
   'key' => array(
      'type' => 'varchar(255)',
      'label' => '存储文件名称',
      'in_list' => true,
      'default_in_list' => true,
    )
  ),
  'comment' => '导出、导入任务表',
  'engine' => 'innodb',
);
