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


$db['regions'] = array(
    'columns' => array(
        'region_id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => '区域序号',
        ),
        'local_name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'default' => '',
            'label' => '地区名称',
        ),
        'package' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'label' => '地区包的类别, 中国/外国等. 中国大陆的编号目前为mainland',
        ),
        'p_region_id' => array(
            'type' => 'int unsigned',
            'label' => '上一级地区的序号',
        ),
        'region_path' => array(
            'type' => 'varchar(255)',
            'label' => '序号层级排列结构',
        ),
        'region_grade' => array(
            'type' => 'number',
            'label' => '地区层级',
        ),
        'ordernum' => array(
            'type' => 'number',
            'editable' => true,
            'label' => '排序',
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ),
    ),
    'index' => array(
    'ind_p_regions_id' => array(
        'columns' => array(
          0 => 'p_region_id',
          1 => 'region_grade',
          2 => 'local_name',
        ),
        'prefix' => 'unique',
    ),
  ),
    'comment' => '地区表',
);
