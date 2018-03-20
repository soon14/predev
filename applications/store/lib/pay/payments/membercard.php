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


final class store_pay_payments_membercard extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '店铺MMGO专用卡消费';
    public $version = 'v1.0';
    public $intro = '店铺MMGO专用卡消费';
    public $transId = [];//需要推送的crm_id
    public $retransId = []; //需要撤销额crm_id
    public $billdetail = []; //推送金额
    public $order_id = '';
//    public $cardNo = '';
    public $real_order_id;
//    public $cardBalance = '';
    public $platform_allow = [
        'store',
    ]; //pc,mobile,app,store


    public function __construct($app)
    {
        parent::__construct($app);
        $this->total_pay = 0;
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
                'title' => '支付方式名称',
                'type' => 'text',
                'default' => '店铺MMGO专用卡支付',
            ),
            'order_num' => array(
                'title' => '排序',
                'type' => 'number',
                'default' => 0,
            ),
            'pay_fee' => array(
                'title' => '交易费率 (%)',
                'type' => 'text',
                'default' => 0,
            ),
            'description' => array(
                'title' => '支付方式描述',
                'type' => 'html',
                'default' => '店铺MMGO专用卡支付',
            ),
            'status' => array(
                'title' => '是否开启此支付方式',
                'type' => 'radio',
                'options' => array(
                    'true' => '是',
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
        $this->order_id =app::get('b2c')->model('order_items')->getRow('item_id',array('order_id'=>$params['order_id']))['item_id'];
        $this->membercard = $params['membercard'];
        logger::alert('订单'.$params['order_id'].'传入进来的总参数'.var_export($params,1));

        /*
         * 没读取到支付卡，就返回false
         * */
        if (is_null($params['membercard'])) {
            $msg = '没卡片信息，请先按Enter键查询';
            $this->pay_response = [
                'err_code' => '0x001',
                'err_code_des' => '没卡片信息，请先按Enter键查询',
                'result_code' => '0x000',
                'return_code' => '调用支付接口成功',
                'return_msg' => '没卡片信息，请先按Enter键查询',
            ];

            return false;
        }
        //计算会员卡消费的金额
        foreach ($params['membercard'] as $k => $v) {
            $v = floatval($v);
            $this->total_pay = $this->total_pay + $v;
        }
        logger::alert('总共需要消费的金额'.$this->total_pay.$params['money']);
        if ($this->total_pay > $params['money']) {
            $msg = 'MMGO专用卡金额超额';
            $this->pay_response = [
                'err_code' => '0x001',
                'err_code_des' => 'MMGO专用卡金额不对',
                'result_code' => '0x000',
                'return_code' => '调用支付接口成功',
                'return_msg' => 'MMGO专用卡金额不对',
            ];

            return false;
        }
        //记录最后的卡号及余额
        $keys = array_keys($params['cardInfo']);
//        $this->cardNo = end($keys);
        $values = array_values($params['cardInfo']);
//        $this->cardBalance = end($values);
        logger::alert(var_export($keys,1));
        $num = 0;
        foreach ($params['membercard'] as $k => $v) {
            //会员卡计算计数器
            $num++;
            $trade_no = $this->get_transaction_number($k, $v, $params['store_id'], $this->order_id,$msg);
            if ($trade_no == false) {
                $this->pay_response = [
                    'err_code' => '0x001',
                    'err_code_des' => '请求准备MMGO专用卡支付交易失败',
                    'result_code' => '0x000',
                    'return_code' => '调用支付接口成功',
                    'return_msg' => '请求准备MMGO专用卡支付交易失败',
                ];
                return false;
            }

            if ($num == 1) {
                $callbackData = [
                    'out_trade_no' => $params['bill_id'],//原样返回提交的支付单据ID
                    'seller_email' => $this->collection_account,//收款者（卖家）账户
                    'payee_bank' => $this->receiving_bank,//收款者（卖家）银行
                    'buyer_email' => $k,//付款者（买家）账户
//                    'payer_bank' => $this->cardNo.'_'.$this->cardBalance,//付款者（买家）银行
                    'payer_bank' => $keys[$num-1].'_'.$values[$num-1],//付款者（买家）银行
                    'total_fee' => $v,//支付金额
                    'trade_no' => $trade_no,//支付平台交易号
                    'order_id' => $params['order_id'],//支付订单id
                ];

                //处理回调数据
                $payResult = $this->callback($callbackData);
            } else {
                //构造支付单数据
                $billData = array(
                    'order_id' => $params['order_id'],
                    'bill_type' => 'payment',
                    'pay_mode' => 'online',
                    'pay_object' => 'order',
                    'money' => $v,
                    'member_id' => $params['member_id'],
                    'status' => 'succ',
                    'pay_app_id' => $params['pay_app_id'],
                    'pay_fee' => $params['pay_fee'],
                    'memo' => $params['memo'],
                    'out_trade_no' => $trade_no,
                    'op_id' => $params['op_id'],
                    'payer_account' => $k,//付款者（买家）账户
                    'payer_bank' => $keys[$num-1].'_'.$values[$num-1],//付款者（买家）银行
                );
                $modelBills = app::get('ectools')->model('bills');
                $billData['bill_id'] = $modelBills->apply_id($billData);
                $paybillInfo = $billData;
            }
            if ($num == 1) {
                //查询支付单数据
                $paybiilColumns = 'bill_type, pay_object, pay_app_id';
                $paybillInfo = app::get('ectools')->model('bills')->getRow($paybiilColumns, ['bill_id' => $params['bill_id']]);
                if (is_array($paybillInfo) === false) {
                    $msg = '支付单数据错误';

                    return false;
                }
                $paybillInfo = array_merge($paybillInfo, $payResult);
            }
//            logger::alert('二次以上生成的支付单'.var_export($paybillInfo,1));
            $this->transId[$trade_no] = $paybillInfo['bill_id'];
//            logger::alert('参数this->transId'.var_export($this->transId,1));
            $this->billdetail[$paybillInfo['bill_id']] = $v;
            //更改支付单状态,进行支付成功后的操作
            $objBill = vmc::singleton('ectools_bill');
            $paybillInfo['app_id'] ='b2c';
            $result = $objBill->generate($paybillInfo, $msg);
//            logger::alert('generate的结果'.var_export($result,1));
            if (!$result) {
                logger::error('支付网关回调后，更新或保存支付单据失败！' . $msg . '.bill_export:' . var_export($payResult, 1));
                return false;
            }
        }
        if(!$this->ConfirmTransCashCardPayment($this->transId)){
            $msg = '刷卡消费失败';
            $this->pay_response = [
                'err_code' => '0x001',
                'err_code_des' => '刷卡消费失败',
                'result_code' => '0x000',
                'return_code' => '调用支付接口成功',
                'return_msg' => '刷卡消费失败',
            ];
            if(!$this->CancelTransCashCardPayment($this->retransId)){
                $msg = '刷卡失败及撤销会员卡消费金额';
                $this->pay_response = [
                    'err_code' => '0x001',
                    'err_code_des' => '刷卡失败及撤销会员卡消费金额',
                    'result_code' => '0x000',
                    'return_code' => '刷卡失败及撤销会员卡消费金额',
                    'return_msg' => '刷卡失败及撤销会员卡消费金额',
                ];
                return false;
            }
            return false;

        }
        return true;

    }

    /*
     * //确认储值卡支付交易
     * */
    protected function ConfirmTransCashCardPayment($transId){
        foreach($transId as $k=>$v){
            $params = array(
                'transId' => $k,
                'serverBillId' => 0,
                'transMoney' =>doubleval($this->billdetail[$v])
            );
//            logger::alert('确认储值卡支付交易参数'.var_export($params,1));
            $result = vmc::singleton('apicenter_api')->api('ConfirmTransCashCardPayment', $params);
//            logger::alert('确认储值卡支付交易结果'.var_export($result,1));
            if(!$result['ConfirmTransCashCardPaymentResult']){
                return false;
            }
            $this->retransId[$k] = $v;
        }
        return true;
    }
    /*
     * //取消储值卡支付交易
     * */
    protected function CancelTransCashCardPayment($retransId){
        if(!count($retransId)>0){
            return true;
        }
//        logger::alert(var_export($retransId,1));
        foreach($retransId as $k=>$v){
            $params = array(
                'transId' => $k,
                'serverBillId' => $this->order_id,
                'transMoney' =>$this->billdetail[$v]
            );
            $result = vmc::singleton('apicenter_api')->api('CancelTransCashCardPayment', $params);
//        logger::alert(var_export($result,1));
            if(!$result){
                return false;
            }
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
    public function get_pay_response()
    {

        $response = [
            'pay_method' => 'membercard',
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
    private function get_transaction_number($cardId, $PayMoney, $storeid,  $billId,&$msg)
    {
        //收款员代码
        $op_id = vmc::singleton('desktop_user')->user_id;
        $time = date('Y-m-d');
        $payments = array(
            'CardId' => $cardId,
            'PayMoney' => doubleval($PayMoney)
        );
        $storeCode = app::get('store')->model('store')->getRow('store_bn', array('store_id' => $storeid));
        $params = array(
            'storeCode' => 'DS'.substr($storeCode['store_bn'],0,2),
            'posId' => '99999',
            'billId' => $billId,
            'cashier' => $op_id,
            'accountDate' => $time,
//            'payments' =>  $payments
        );
        $params['payments']['CashCardPayment']=$payments;
        logger::alert('准备储值卡支付交易参数'.var_export($params,1));
        $result = vmc::singleton('apicenter_api')->api('PrepareTransCashCardPayment2', $params);
//        logger::alert('准备储值卡支付交易结果'.var_export($result,1));
        if (!$result['PrepareTransCashCardPayment2Result']) {
            if($result['msg'] == '储值帐户余额不足') {
                $msg = '支付失败，使用金额超过卡内余额';
            }else{
                $msg = $result['msg'];
            }
            return false;
        }
        //固定id
        return $result['transId'];
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
