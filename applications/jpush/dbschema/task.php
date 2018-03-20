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


$db['task'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'extra' => 'auto_increment',
            'required' => true,
            'pkey' => true,
            'label' => '推送任务ID' ,
        ) ,
        'msg_id'=>array(
            'type'=>'bigint unsigned',
            'label'=>'消息ID'
        ),
        'schedule_id'=>array(
            'type'=>'varchar(200)',
            'label'=>'定时推送ID'
        ),
        'schedule_enabled'=>array(
            'type'=>'bool',
            'label'=>'定时任务是否有效'
        ),
        'task_mark'=>array(
            'type'=>'varchar(255)',
            'label'=>'推送任务备注',
            'filtertype' => 'normal',
            'searchtype' => 'has',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'platform' => array(
            'type' => array(
                'ios'=>'iOS用户',
                'android'=>'Android用户',
                'all'=>'所有类型设备用户'
            ),
            'label' => '推送平台' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'title'=>array(
            'type'=>'varchar(255)',
            'label'=>'个性标题',
            'in_list' => true,
        ),
        'content' => array(
            'type' => 'varchar(500)',
            'label' => '推送内容' ,
            'filtertype' => 'normal',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'event_type' => array(
            'type' => array(
                'normal'=>'打开APP',
                'push'=>'打开APP并跳转',
            ),
            'default'=>'normal',
            'label' => '消息点击动作' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'event_params'=>array(
            'type'=>'serialize',
            'label'=>'消息点击动作参数',
        ),
        'send_time'=>array(
            'type'=>'time',
            'label'=>'消息推送时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'ios_received'=>array(
                'type'=>'number',
                'label'=>'iOS设备消息触达数',
        ),
        'android_received'=>array(
                'type'=>'number',
                'label'=>'Android设备消息触达数',
        ),
        'createtime'=>array(
            'type'=>'time',
            'label'=>'推送任务创建时间',
            'filtertype'=>'normal',

            'in_list' => true,
            'default_in_list' => true,
        )
    ) ,
    'engine' => 'innodb',
    'comment' => ('消息推送任务表') ,
);
