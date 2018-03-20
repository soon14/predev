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


$db['activity_order'] = array(
  'columns' => array(
  'id' => array(
      'type' => 'bigint unsigned',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      'label' => ('预约单号') ,
      'is_title' => true,
      'searchtype' => 'has',
      'filtertype' => 'custom',
      'in_list' => true,
      'default_in_list' => true,
  ) ,
    'member_id' => array(
      'type' => 'table:members@b2c',
      'label' => '会员',
      'required' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'store_id' => array(
      'type' => 'table:store',
      'label' => '活动地点',
      'required' => true,
      'filtertype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'subject_id' => array(
      'type' => 'table:activity_subject',
      'label' => '活动主题',
      'required' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'schedule_id' => array(
      'type' => 'table:activity_schedule',
      'label' => '预约场次',
      'required' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
      'ticket_id' => array(
          'type' => 'table:activity_ticket',
          'label' => '入场票券',
      ),
    'need_ticket' => array(
      'type' => 'bool',
      'label' => '是否需要入场券',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'ticket_name' => array(
      'type' => 'varchar(255)',
      'label' => '票券名称',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'ticket_batch_no' => array(
      'type' => 'varchar(100)',
      'label' => '票券批号',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'ticket_price' => array(
      'type' => 'money',
      'label' => '票券金额',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    //   'ticket_nums' => array(
    //       'type' => 'number',
    //       'required' => true,
    //       'default' => 1,
    //       'label' => '购买票券数量',
    //       'filtertype' => 'normal',
    //       'in_list' => true,
    //   ),
    //   'has_used' => array(
    //       'type' => 'number',
    //       'required' => true,
    //       'default' => 0,
    //       'label' => '已使用票券数量',
    //       'in_list' => true,
    //   ),
    'payed' => array(
      'type' => 'money',
      'label' => '已付款金额',
      'filtertype' => 'normal',
      'required' => true,
      'default' => 0,
      'in_list' => true,
    ),
    'pay_app_id' => array(
      'type' => 'varchar(50)',
      'label' => '支付方式appid',
    ),
    'createtime' => array(
      'type' => 'time',
      'label' => '预约时间',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'payed_time' => array(
      'type' => 'time',
      'label' => '支付时间',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'enter_time' => array(
      'type' => 'time',
      'label' => '入场时间',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
      'has_notice' => array(
          'type' => array(
              '0' => '未提醒',
              '1' => '已提醒',
          ),
          'default' => '0',
          'in_list' => true,
          'label' => '是否已提醒',
      ),
      'name' => array(
          'type' => 'varchar(100)',
          'label' => '姓名',
          'in_list' => true,
      ),
      'sex' => array(
          'type' => array(
              '0' => '-',
              '1' => '男',
              '2' => '女',
          ),
          'default' => '0',
          'label' => '性别',
          'in_list' => true,
      ),
      'birth' => array(
          'type' => 'varchar(20)',
          'label' => '出生年月',
          'in_list' => true,
      ),
      'phone' => array(
          'type' => 'varchar(20)',
          'label' => '联系电话',
          'in_list' => true,
      ),
      'enable' => array(
          'type' => array(
              'true' => '有效',
              'false' => '已取消',
          ),
          'default' => 'true',
          'label' => '是否有效',
          'in_list' => true,
      ),
      'wx_formid'=>array(
          'type' => 'varchar(55)',
          'label' => 'wx_formid'
      )
  ),
  // //索引
  // 'index' => array(
  //   'ind_key' => array(
  //     'columns' => array(
  //       0 => 'column',
  //     ),
  //   ),
  // ),
  'comment' => ('活动预约纪录'),
);
