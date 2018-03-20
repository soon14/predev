<?php

$setting = array(
    'name' => array(
        'type' => 'text',
        'default' => '余额宝',
        'required' => true,
        'desc' => '前台显示名称',
    ),
    'unit' => array(
        'type' => 'text',
        'default' => '',
        'desc' => '单位',
    ),
    'exchange_ratio' => array(
        'type' => 'text',
        'default' => '1',
        'desc' => '余额汇率',
        'helpinfo' => '与人民币汇率,1～100的数值。',
    ),
    'cash_out_enabled' => array(
        'required' => true,
        'type' => 'select',
        'options' => array(
            '1' => '是',
            '0' => '否',
        ),
        'default' => '0',
        'desc' => '是否允许提现',
        'helpinfo' => '',
    ),
    'cash_out_fee_ratio' => array(
        'type' => 'text',
        'default' => '0',
        'desc' => '提现手续费率',
        'helpinfo' => '提现时的手续费率,0则不收手续费,0.1代表10%提现手续费',
    ),
    'recharge_limit' => array(
        'type' => 'between',
        'from' => array(
            'limit_minimum' => array(
                'type' => 'text',
            ),
        ),
        'to' => array(
            'limit_maximum' => array(
                'type' => 'text',
            ),
        ),
        'desc' => '每次充值金额限制',
    ),
    'larger_type' => array(
        'required' => true,
        'type' => 'select',
        'options' => array(
            '1' => '是',
            '0' => '否',
        ),
        'default' => '0',
        'desc' => '较大额度支付手机验证类型',
        'helpinfo' => '会员需先绑定过手机',
    ),
    'larger_sum' => array(
        'type' => 'text',
        'default' => '',
        'desc' => '需手机验证起始金额',
    ),
    'earnings_type' => array(
        'required' => true,
        'type' => 'select',
        'options' => array(
            '1' => '是',
            '0' => '否',
        ),
        'default' => '0',
        'desc' => '余额年化收益是否开启',
    ),
    'year_ratio' => array(
        'type' => 'text',
        'default' => '',
        'desc' => '年化收益率',
        'helpinfo' => '请输入小数,如：年化收益12%,则输入0.12',
    ),
    'recharge_rule' => array(
        'type' => 'textarea',
        'default' => '充值后,即时到账',
        'desc' => '充值页面说明文字设置',
    ),
    'cash_rule' => array(
        'type' => 'textarea',
        'default' => '提现成功后,财务将在2个工作日完成审核',
        'desc' => '提现页面说明文字设置',
    ),
);
