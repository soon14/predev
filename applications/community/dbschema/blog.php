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


$db['blog'] = array(
    'columns' => array(
        'blog_id' => array(
            'type' => 'bigint unsigned',
            'pkey'=>true,
            'required' => true,
            'label' => '帖子ID',
        ),
        'blog_type'=>array(
            'type'=>array(
                'topic'=>'主题',
                'zan'=>'点赞',
                'unzan'=>'不赞',
                'comment'=>'评论留言'
            ),
            'required' => true,
            'default'=>'topic',
            'label' => '帖子类型'
        ),
        'follow_blog_id' => array(
            'type' => 'bigint unsigned',
            'default'=>0,
            'required' => true,
            'label' => '跟帖ID',
        ),
        'author' => array(
            'type' => 'table:users',
            'required' => true,
            'label' => '作者',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'title'=>array(
            'type'=>'varchar(255)',
            'label' => '标题',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'content'=>array(
            'type'=>'text',
            'label'=>'描述',
            'filtertype' => 'normal',
            'searchtype' => 'has',
            'in_list' => true
        ),
        'mode' => array(
            'type' => array(
                'audio'=>'音频',
                'short_video'=>'短视频',
                'video'=>'视频',
                'image'=>'图片',
                'text'=>'普通文本',
                'other'=>'其他'
            ),
            'label' => '内容类型' ,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'tag_setting'=>array(
            'type'=>'varchar(255)',
            'label'=>'标签设置'
        ),
        'createtime'=>array(
            'type'=>'time',
            'label'=>'发布时间',
            'orderby'=>true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status'=>array(
            'label' => '状态' ,
            'type'=>array(
                'shield'=>'被屏蔽',
                'release'=>'已发布',
                'recommend'=>'推荐',
                'highlyrecommend'=>'强烈推荐',
                'top'=>'置顶'
            ),
            'orderby'=>true,
            'default'=>'release',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'lnandla'=>array(
            'type'=>'varchar(50)',
            'label'=>'经纬度lng|lat'
        ),
        'longitude'=>array(
            'type'=>'varchar(50)',
            'label'=>'经度'
        ),
        'latitude'=>array(
            'type'=>'varchar(50)',
            'label'=>'纬度'
        ),
        'location'=>array(
            'type'=>'varchar(255)',
            'label'=>'位置',
            'in_list' => true,
        ),
        'address'=>array(
            'type'=>'varchar(255)',
            'label'=>'详细地址门牌'
        ),
        'zan_count'=>array(
            'type'=>'number',
            'label'=>'赞赏数',
            'orderby'=>true,
            'in_list' => true,

        ),
        'unzan_count'=>array(
            'type'=>'number',
            'label'=>'讨厌数',
            'orderby'=>true,
            'in_list' => true,

        ),
        'comment_count'=>array(
            'type'=>'number',
            'label'=>'评论数',
            'orderby'=>true,
            'in_list' => true,
        ),
        'mark_count'=>array(
            'type'=>'number',
            'label'=>'收藏数',
            'orderby'=>true,
            'in_list' => true,
        ),
        'share_count'=>array(
            'type'=>'number',
            'label'=>'转发数',
            'orderby'=>true,
            'in_list' => true,
        ),
        'disabled' => array(
          'type' => 'bool',
          'default' => 'false',
          'label' => ('是否已失效'),
          'in_list' => true
        ),
    ) ,
    'index' => array(
      'ind_status' => array(
        'columns' => array(
          0 => 'status',
        ),
      ),
      'ind_type' => array(
        'columns' => array(
          0 => 'blog_type',
        ),
      ),
      'ind_mode' => array(
        'columns' => array(
          0 => 'mode',
        ),
      ),
      'ind_follow_blog_id' => array(
        'columns' => array(
          0 => 'follow_blog_id',
        ),
      ),
      'ind_disabled' => array(
        'columns' => array(
          0 => 'disabled',
        ),
      ),
    ),
    'engine' => 'innodb',
    'comment' => ('网志表') ,
);
