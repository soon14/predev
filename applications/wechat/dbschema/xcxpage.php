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


$db['xcxpage'] = array(
    'columns' => array(
        'id' => array(
          'type' => 'int unsigned',
          'required' => true,
          'pkey' => true,
          'extra' => 'auto_increment',
          'comment' => 'ID',
        ),
        'sno' => array(
            'type' => 'varchar(100)',
            'label' => '编号',
            'required' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'title' => array(
            'type' => 'varchar(100)',
            'label' => '标题',
            'required' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'bg_hex' => array(
            'type' => 'char(7)',
            'label' => '背景颜色',
        ),
        'bar_title_hex' => array(
            'type' => 'char(7)',
            'label' => '顶部导航文字颜色',
        ),
        'bar_bg_hex' => array(
            'type' => 'char(7)',
            'label' => '顶部导航背景色',
        ),
        'bar_animation_duration' => array(
            'type' => 'smallint(4)',
            'label' => '动画变化时间',
        ),
        'bar_animation_func' => array(
            'type' => 'varchar(20)',
            'label' => '动画变化方式',
        ),
        'widgets' => array(
            'type' => 'serialize',
            'label' => '挂件数据',
        ),
        'widgets_draft' => array(
            'type' => 'serialize',
            'required' => true,
            'label' => '挂件草稿数据',
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => '创建时间',
            'orderby' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'is_homepage'=>array(
            'type'=>'bool',
            'label'=>'是否默认首页',
            'filtertype'=>'normal',
            'in_list' => true,
            'default_in_list' => true
        ),
        'last_pubtime' => array(
            'type' => 'time',
            'label' => '最近发布时间',
            'orderby' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'updatetime'=>array(
            'type' => 'last_modify',
            'label' => '最近草稿保存时间',
            'orderby' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'version' => array(
            'type' => 'float(6,3)',
            'label' => '页面版本',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'index' => array(
      'ind_sno' => array(
          'columns' => array(
              0 => 'sno',
          ),
          'prefix' => 'unique',
      ),
    ),
    'comment' => '小程序页面表',
);
