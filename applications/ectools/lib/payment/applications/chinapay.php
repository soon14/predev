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

include_once("chinapayclient.php");
final class ectools_payment_applications_chinapay extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '中国银联支付';
    public $version = 'v2.5.23';
    public $intro = 'CHINAPAY 致力于发展中国金融电子支付服务';
    public $platform_allow = array(
        'pc','mobile'
    ); //pc,mobile,app

    /**
     * 构造方法.
     *
     * @param null
     *
     * @return bool
     */
    public function __construct($app)
    {
        parent::__construct($app);

        // $this->callback_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
        //     'ectools_payment_applications_chinapay' => 'callback',
        // ));
        $this->callback_url = vmc::openapi_url('openapi.ecpay', 'gc', array(
            'chinapay' => 'callback',
        ));
        // $this->notify_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
        //     'ectools_payment_applications_chinapay' => 'notify',
        // ));
        $this->notify_url = vmc::openapi_url('openapi.ecpay', 'gc', array(
            'chinapay' => 'notify',
        ));
        $this->submit_url = 'https://payment.chinapay.com/CTITS/payment/TransGet';
        if(defined('CHINAPAY_TEST') && constant('CHINAPAY_TEST')){
            $this->submit_url = 'http://payment-test.ChinaPay.com/CTITS/payment/TransGet';
        }
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
                'title' => '支付方式名称' ,
                'type' => 'text',
                'default' => 'ChinaPay银联支付',
            ) ,
            'order_num' => array(
                'title' => '排序' ,
                'type' => 'number',
                'default' => 0,
            ) ,
            'mer_id' => array(
                'title' => '商户号' ,
                'type' => 'text',
            ) ,
            'pub_key' => array(
                'title' => '公钥文件内容' ,
                'type' => 'textarea',
            ) ,
            'mer_key' => array(
                'title' => '私钥文件内容' ,
                'type' => 'textarea',
            ) ,
            /*个性化字段结束*/
            'pay_fee' => array(
                'title' => '交易费率 (%)' ,
                'type' => 'text',
                'default' => 0,
            ) ,
            'description' => array(
                'title' => '支付方式描述' ,
                'type' => 'html',
                'default' => '支付方式描述',
            ) ,
            'status' => array(
                'title' => '是否开启此支付方式' ,
                'type' => 'radio',
                'options' => array(
                    'true' => '是' ,
                    'false' => '否' ,
                ) ,
                'default' => 'true',
            ) ,
        );
    }
    /**
     * 提交支付信息的接口.
     *
     * @param array 提交信息的数组
     *
     * @return mixed false or null
     */
    public function dopay($params,&$msg)
    {
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $sign_params = array(
            /**
             * 商户号,长度为 15 个字节的数字串,由 ChinaPay 分配,用户在后台填入。*
             */
            'MerId'=>$mer_id,
            /**
             * 订单号,长度为 16 个字节的数字串,由商户系统生成,失败的订单号允许重复支付。
             */
            'OrdId'=>str_pad($params['bill_id'],16,'0',STR_PAD_RIGHT),
            /**
             * 交易金额,长度为 12 个字节的数字串,例如:数字串"000000001234"表示 12.34 元
             */
            'TransAmt'=>str_pad(number_format($params['money'],2,'',''),12,'0',STR_PAD_LEFT),
            /**
             * 货币代码, 长度为 3 个字节的数字串,目前只支持人民币,取值为"156" 。
             */
            'CuryId'=>'156',
            /**
             * 交易日期,长度为 8 个字节的数字串,表示格式为:YYYYMMDD。
             */
            'TransDate'=>date('Ymd',$params['createtime']),
            /**
             * 交易类型,长度为 4 个字节的数字串,取值范围为:"0001"和"0002",其中"0001"表示消费,'0002'表示退款
             */
            'TransType'=>'0001',//支付操作
            'Version'=>'20141120',//必填
            'BgRetUrl'=>$this->notify_url,//异步通知
            'PageRetUrl'=>$this->callback_url,//同步回调
            //'GateId'=>'',//支付网关（银行）编号，为空时出现网关选择，指定时，直接跳转到网关
            'Priv1'=>$params['bill_id'],//成功后原样返回
        );
        $ChkValue = $this->_get_mac($sign_params);
        if(!$ChkValue){
            $msg = '验签失败！';
            return false;
        }
        $sign_params['ChkValue'] = $ChkValue;
        foreach ($sign_params as $key => $value) {
            $this->add_field($key,$value); //支付单据ID
        }
        if ($this->is_fields_valiad()) {
            echo $this->get_html();
            exit;
        } else {
            return false;
        }
    }
    /**
     * 支付平台同步跳转处理.
     *
     * @params array - 所有返回的参数，包括POST和GET
     */
    public function callback(&$params)
    {
        $ret['bill_id'] = $params['Priv1']; //原样返回提交的支付单据ID
        $ret['payee_account'] = 'MERID:'.$params['merid']; //收款者（卖家）账户
        $ret['payee_bank'] = $this->name; //收款者（卖家）银行
        //$ret['payer_account'] = '未知'; //付款者（买家）账户
        $ret['payer_bank'] = 'ChinaPay-'.$params['GateId']; //付款者（买家）银行
        $ret['out_trade_no'] = $params['orderno']; //支付平台交易号
        if ($this->is_return_vaild($params)) {
            switch ($params['status']) { //交易状态
                case '1001':
                    $ret['status'] = 'succ';
                break;
                default:
                    $ret['status'] = 'error';
            }
        } else {
            $ret['status'] = 'invalid'; //非法参数
        }

        return $ret;
    }
    /**
     * 支付平台异步处理.
     *
     * @params array - 所有返回的参数，包括POST和GET
     */
    public function notify(&$params)
    {
        $ret = $this->callback($params);
        if ($ret['status'] == 'succ') {
            echo 'success';//告知notify 服务不再通知
        }

        return $ret;
    }
    private function _get_key_filepath($type='pub'){
        $mer_id = $this->getConf('mer_id', __CLASS__);
        if (!is_dir(TMP_DIR)) {
            utils::mkdir_p(TMP_DIR);
        }
        return TMP_DIR.'/chinapay_cert_'.$mer_id.'_'.$type.'.key';
    }
    /**
     * 生成签名.
     *
     * @param mixed $params 包含签名数据的数组
     * @param mixed $type  验签类型 sign 签名、check 验证签名
     *
     * @return string
     */
    private function _get_mac($params)
    {
        //$pub_key = $this->getConf('pub_key', __CLASS__);
        $mer_key_content = $this->getConf('mer_key', __CLASS__);
        if(!file_exists($this->_get_key_filepath('mer'))){
            file_put_contents($this->_get_key_filepath('mer'),$mer_key_content, LOCK_EX);
        }
        $mer_id = buildKey($this->_get_key_filepath('mer'));
        if(!$mer_id || $mer_id != $params['MerId']){
            return false;
        }
        $sign_plain = implode('',array_values($params));
        $res = sign($sign_plain);
        if(!$res){
            return false;
        }
		return $res;
    }
    /**
     * 检验返回数据合法性.
     *
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key  签名用到的私钥
     *
     * @return bool
     */
    private function is_return_vaild($params)
    {
        $pub_key_content = $this->getConf('pub_key', __CLASS__);
        if(!file_exists($this->_get_key_filepath('pub'))){
            file_put_contents($this->_get_key_filepath('pub'),$pub_key_content, LOCK_EX);
        }
        $flag = buildKey($this->_get_key_filepath('pub'));
        if(!$flag){
            return false;
        }
        if(!verifyTransResponse($params['merid'],
        $params['orderno'],
        $params['amount'],
        $params['currencycode'],
        $params['transdate'],
        $params['transtype'],
        $params['status'],
        $params['checkvalue'])){
            logger::error('Chinapay 支付返回验证失败!'.var_export($params,1));
            return false;
        }
        return true;
    }
}
