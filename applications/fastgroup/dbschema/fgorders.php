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


$db['fgorders'] = array(
  'columns' => array(
      'skey' => array(
          'type' => 'bigint unsigned',
          'required' => true,
          'default' => 0,
          'pkey' => true,
          'label' => '团购秘钥' ,
          'searchtype' => 'nequal',
          'filtertype' => 'custom',
          'in_list' => true,
          'default_in_list' => true,
      ) ,
      'order_id' => array(
          'type' => 'table:orders@b2c',
          'label' => '订单号',
          'searchtype' => 'nequal',
          'filtertype' => 'custom',
          'in_list' => true,
          'default_in_list' => true,
        ) ,
        'order_status' => array(
            'type' => array(
                'active' => ('活动订单') ,
                'dead' => ('已作废') ,
                'finish' => ('已完成') ,
            ) ,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'default' => 'active',
            'required' => true,
            'label' => '订单状态',
          ) ,
        'pay_status' => array(
            'type' => array(
                '0' => ('未支付') ,
                '1' => ('已支付') ,
                '2' => ('已付款至到担保方') ,
                '3' => ('部分付款') ,
                '4' => ('部分退款') ,
                '5' => ('全额退款') ,
            ) ,
            'label' => '付款状态',
            'default'=>'0',
            'required' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
          ) ,
        'mobile' => array(
          'type' => 'varchar(20)',
          'label' => '订购手机号',
          'searchtype' => 'nequal',
          'filtertype' => 'normal',
          'required' => true,
          'in_list' => true,
          'default_in_list' => true,
        ),
        'subject_id' => array(
          'type' => 'table:subject',
          'label' => '团购活动',
          'required' => true,
          'filtertype' => 'normal',
          'in_list' => true,
          'default_in_list' => true,
        ),
        'customer_memo' => array(
          'type' => 'text',
          'label' => '顾客备注',
          'in_list' => true,
          'default_in_list' => true,
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => '下单时间' ,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ) ,
        'succ_pay_time' => array(
            'label' => '成功付款时间' ,
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'last_modified' => array(
            'label' => '最后更新时间' ,
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
        ) ,

  ),
  // //索引
  // 'index' => array(
  //   'ind_key' => array(
  //     'columns' => array(
  //       0 => 'column',
  //     ),
  //   ),
  // ),
  'comment' => ('团购纪录'),
);
