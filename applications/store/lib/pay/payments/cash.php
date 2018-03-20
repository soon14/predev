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


final class store_pay_payments_cash extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '店铺O2O现金支付';
    public $version = 'v1.0';
    public $intro = '店铺O2O现金支付';
    public $platform_allow = [
        'store',
    ]; //pc,mobile,app,store

    /**
     * 现金支付收款账号
     *
     * @var string
     */
    private $collection_account = '';
    /**
     * 现金支付收款银行
     *
     * @var string
     */
    private $receiving_bank = '';

    /**
     * 支付请求结果
     *
     * @var array
     */
    private $pay_response = [];

    /**
     * 构造方法.
     *
     * @param $app
     *
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->callback_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'store_pay_payments_cash' => 'callback',
        ));
        $this->notify_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'store_pay_payments_cash' => 'notify',
        ));
        $this->submit_url = '';
        $this->submit_method = 'POST';
        $this->submit_charset = 'utf-8';
    }

    /**
     * 后台配置参数设置.
     *
     * @param null
     *
     * @return array 配置参数列表
     */
    public function setting()
    {
        return array(
            'display_name' => array(
                'title'   => '支付方式名称',
                'type'    => 'text',
                'default' => '店铺O2O会员卡支付',
            ),
            'order_num'    => array(
                'title'   => '排序',
                'type'    => 'number',
                'default' => 0,
            ),
            'pay_fee'      => array(
                'title'   => '交易费率 (%)',
                'type'    => 'text',
                'default' => 0,
            ),
            'description'  => array(
                'title'   => '支付方式描述',
                'type'    => 'html',
                'default' => '店铺O2O现金支付',
            ),
            'status'       => array(
                'title'   => '是否开启此支付方式',
                'type'    => 'radio',
                'options' => array(
                    'true'  => '是',
                    'false' => '否',
                ),
                'default' => 'true',
            ),
        );
    }

    /**
     * 提交支付信息的接口.
     * 现金支付直接构造回调数据调用回调方法
     *
     * @param array $params 提交信息的数组
     * @param string $msg 错误信息
     *
     * @return mixed false or null
     */
    public function dopay($params, &$msg)
    {
        $this->real_order_id = $params['order_id'];
        if( $params['shoukuanJine']> $params['money'] + 100 || $params['shoukuanJine'] == 0){
            $msg = '支付金额超额或未输入';
            $this->pay_response = [
                'err_code' => '0x001',
                'err_code_des' => '支付金额不足',
                'result_code' => '0x000',
                'return_code' => '调用支付接口成功',
                'return_msg' => '支付金额不足',
            ];

            return false;
        }
        if($params['shoukuanJine'] <$params['money']){
            $params['money'] = $params['shoukuanJine'];
        }
        //构造回调数据
        $callbackData = [
            'out_trade_no' => $params['bill_id'],//原样返回提交的支付单据ID
            'seller_email' => $this->collection_account,//收款者（卖家）账户
            'payee_bank'   => $this->receiving_bank,//收款者（卖家）银行
            'buyer_email'  => 'xianjin',//付款者（买家）账户
            'payer_bank'   => 'xianjin',//付款者（买家）银行
            'total_fee'    => $params['money'],//支付金额
            'trade_no'     => $this->get_transaction_number(),//支付平台交易号
            'order_id'     => $params['order_id'],//支付订单id
        ];

        //处理回调数据
        $payResult = $this->callback($callbackData);

        //查询支付单数据
        $paybiilColumns = 'bill_type, pay_object, pay_app_id';
        $paybillInfo = app::get('ectools')->model('bills')->getRow($paybiilColumns, ['bill_id' => $params['bill_id']]);
        if(is_array($paybillInfo) === false){
            $msg = '支付单数据错误';

            return false;
        }
        $paybillInfo = array_merge($paybillInfo, $payResult);
        $paybillInfo['app_id'] = 'b2c';
        //更改支付单状态,进行支付成功后的操作
        $objBill = vmc::singleton('ectools_bill');
        $result = $objBill->generate($paybillInfo, $msg);
        if (!$result) {
            logger::error('支付网关回调后，更新或保存支付单据失败！' . $msg . '.bill_export:' . var_export($payResult, 1));

            return false;
        }

        return true;
    }

    /**
     * 支付回调.
     *
     * @param array $params 所有返回的参数，包括POST和GET
     *
     * @return array
     */
    public function callback(&$params)
    {
        $ret['bill_id'] = $params['out_trade_no']; //原样返回提交的支付单据ID
        $ret['payee_account'] = $params['seller_email']; //收款者（卖家）账户
        $ret['payee_bank'] = $params['payee_bank']; //收款者（卖家）银行
        $ret['payer_account'] = $params['buyer_email']; //付款者（买家）账户
        $ret['payer_bank'] = $params['payer_bank']; //付款者（买家）银行
        $ret['money'] = $params['total_fee'];
        $ret['out_trade_no'] = $params['trade_no']; //支付平台交易号
        $ret['order_id'] = $params['order_id'];//支付订单id
        $ret['status'] = 'succ';

        return $ret;
    }

    /**
     * 支付平台异步处理.
     *
     * @param array $params 所有返回的参数，包括POST和GET
     *
     * @return array
     */
    public function notify(&$params)
    {
        $ret = $this->callback($params);
        if ($ret['status'] == 'succ') {

            echo 'success';//告知notify 服务不再通知
        }

        return $ret;
    }

    /**
     * 获取请求支付接口的响应数据
     *
     * @return array
     */
    public function get_pay_response(){

        $response = [
            'pay_method' => 'cash',
            'err_code' => $this->pay_response['err_code'],
            'err_code_des' => $this->pay_response['err_code_des'],
            'result_code' => $this->pay_response['result_code'],
            'return_code' => $this->pay_response['return_code'],
            'return_msg' => $this->pay_response['return_msg'],
        ];

        return $response;
    }

    /**
     * 检验返回数据合法性.
     *
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key 签名用到的私钥
     *
     * @return bool
     */
    private function is_return_vaild($form, $key)
    {
        return true;
    }

    /**
     * 获取现金支付唯一的交易号
     *
     * @return string
     */
    private function get_transaction_number()
    {
        $transaction_number = md5(uniqid('transaction_number', true));

        return $transaction_number;
    }
    public function successMessage(){
        //系统支付单查询：如果支付状态不是 1 返回支付失败
        $pay_order_info = app::get('b2c')->model('orders')->getRow('*',array('order_id'=>$this->real_order_id));
        logger::alert('订单信息'.$pay_order_info);
        $arr = array(
            'pay_status'=>$pay_order_info['pay_status'],
            'payed'=>$pay_order_info['payed'],
            'order_total'=>$pay_order_info['order_total'],
        );
        return $arr;
    }
}
