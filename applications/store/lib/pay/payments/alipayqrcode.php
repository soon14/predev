<?php

/**
 * 扫码支付
 *
 * Class store_pos_createorder
 */
class store_pay_payments_alipayqrcode extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '店铺支付宝条码支付';
    public $version = 'v1.0';
    public $intro = '店铺支付宝条码支付';
    public $platform_allow = [
        'store',
    ]; //pc,mobile,app,store

    private $msg = '';

    /**
     * 公众账号ID
     *
     * @var string
     */
    private $app_id = '';
    /**
     * 支付url
     *
     * @var string
     */
    private $submit_url = '';
    /**
     * 证书目录
     *
     * @var string
     */
    private $cert_path;

    /**
     * 支付请求结果
     *
     * @var array
     */
    private $pay_response = [];

    /**
     * 字符集
     *
     * @var string
     */
    private $charset = 'utf-8';

    public function __construct($app) {
        parent::__construct($app);

        $this->submit_url = 'https://openapi.alipay.com/gateway.do?charset=' . $this->charset;
        $this->app_id = $this->app_id = $this->getConf('mer_id',      __CLASS__);

        //获取当前文本编码
        $this->file_charset = mb_detect_encoding($this->app_id, "UTF-8,GBK");

        $this->cert_path = ROOT_DIR . '/applications/store/statics/cert/alipayqrcode';
    }

    /**
     * 设置后台的显示项目（表单项目）
     *
     * @params null
     *
     * @return array - 配置的表单项
     */
    public function setting()
    {
        return array(
            'display_name' => array(
                'title'   => '支付方式名称',
                'type'    => 'text',
                'default' => '店铺支付宝条码支付',
            ),
            'order_num'    => array(
                'title'   => '排序',
                'type'    => 'number',
                'default' => 0,
            ),
            'mer_id'       => array(
                'title' => '合作者身份(parterID)',
                'type'  => 'text',
            ),
//            'mer_key'      => array(
//                'title' => '交易安全校验码(key)',
//                'type'  => 'text',
//            ),
            'pay_fee'      => array(
                'title'   => '交易费率 (%)',
                'type'    => 'text',
                'default' => 0,
            ),
            'description'  => array(
                'title'   => '支付方式描述',
                'type'    => 'html',
                'default' => '店铺支付宝条码支付',
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
     * 调起支付
     *
     * @param array $payments 支付数据
     * @param string $msg
     *
     * @return bool 支付结果
     */
    public function dopay($payments, &$msg = '')
    {
       

        $this->add_field('app_id', $this->app_id);//开发者的AppId
        $this->add_field('method', 'alipay.trade.pay');//接口名称
        $this->add_field('charset', $this->charset);//参数字符编码
        $this->add_field('sign_type', 'RSA');//签名类型
        $this->add_field('timestamp', date('Y-m-d H:i:s'));//发送请求的时间
        $this->add_field('version', '1.0');//调用的接口版本，固定为:1.0

        $bizContent = [
            'out_trade_no' => $payments['bill_id'],//商户订单号
            'scene' => 'bar_code',//支付场景
            'auth_code' => $payments['auth_code'],//付款条码(扫描或输入的支付授权码)
            //'seller_id' => '',//卖家支付宝用户ID
            'total_amount' => bcmul($payments['money'], 1, 2),//付款金额
            'subject' => $payments['subject'] ? $payments['subject'] : '订单名称',//订单名称
            'time_expire' => date('Y-m-d H:i:s', time() + 60 * 30),//30分钟过期时间
        ];
        $this->add_field('biz_content', json_encode($bizContent));

       

        $this->add_field('sign', $this->get_sign($this->getSignContent($this->fields)));//签名

        //去支付
        try{
            $this->pay_response = $this->curl($this->submit_url, $this->fields);
        }catch (Exception $e){
            $msg = $e->getMessage();

            return false;
        }

        $this->pay_response = iconv($this->charset, $this->file_charset . "//IGNORE", $this->pay_response);

        $this->pay_response = json_decode($this->pay_response, true);

        if($this->pay_response == false ){
            $msg = '支付结果不是标准的json格式';

            return false;
        }

        $this->pay_response = $this->pay_response['alipay_trade_pay_response'];

        //检查支付结果
        $check_result = $this->check_pay_response($this->pay_response);
        if($check_result == false){
            $msg = $this->msg;
            if(!$this ->check_pay_result($payments ,$msg)){
                return false;
            }
            return true;
        }

        //处理支付回调数据
        $callback_data = $this->callback($this->pay_response);

        //支付完成以后回调
        $extends_result = $this->pay_succ_extends($callback_data);
        if(!$extends_result){
            #失败的话要取消订单
            $this->cancel_order($payments);
            $msg = '支付回调处理失败';

            return false;
        }

        return true;
    }

    /**
     * 支付后返回后处理的事件的动作
     *
     * @param array $params 所有返回的参数，包括POST和GET
     *
     * @return array
     */
    public function callback(&$params)
    {
        $ret['bill_id'] = $params['out_trade_no'];//商户订单号
        $ret['payee_account'] = $params['store_name'];//收款者（卖家）账户(交易发生所在门店的门店名称)
        $ret['payee_bank'] = $params['store_name'];//收款者（卖家）银行(交易发生所在门店的门店名称)
        $ret['payer_account'] = $params['open_id'];//付款者（买家）账户(买家支付宝用户号)
        $ret['payer_bank'] = $params['buyer_logon_id'];//付款者（买家）银行(买家支付宝账号)
        $ret['money'] = $params['total_amount'];//支付金额
        $ret['out_trade_no'] = $params['trade_no'];//支付平台交易号(支付宝交易号)
        $ret['status'] = 'succ';

        return $ret;
    }

    /**
     * 获取请求支付接口的响应数据
     *
     * @return array
     */
    public function get_pay_response(){

        $response = [
            'pay_method' => 'alipayqrcode',
            'err_code' => $this->pay_response['code'],
            'err_code_des' => "{$this->pay_response['msg']}({$this->get_error_info($this->pay_response['sub_code'])})",
        ];

        return $response;
    }

    /**
     * 支付后异步通知返回后处理的事件的动作
     *
     * @param array $recv 所有返回的参数，包括POST和GET
     *
     * @return null
     */
    public function notify(&$recv)
    {
        // TODO: Implement notify() method.
    }

    /**
     * 循环查询支付宝条码支付订单是否支付成功
     *
     * @param $payment_info
     * @param string $msg
     *
     * @return bool
     */
    public function check_pay_result($payment_info, &$msg = ''){

        //构造查询订单支付结果需要的参数
        $to_check_input = [
            'app_id' => $this->app_id,
            'method' => 'alipay.trade.query',
            'charset' => $this->charset,
            'sign_type' => 'RSA',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0'
        ];
        $biz_content = [
            'out_trade_no' => $payment_info['bill_id']
        ];
        $to_check_input['biz_content'] = json_encode($biz_content);
        $to_check_input['sign'] = $this->get_sign($this->getSignContent($to_check_input));

        //循环查询10次,确认支付是否成功
        $query_times = 10;
        while ($query_times > 0) {
            $result_code = 0;
            $query_result = $this->to_check_pay_result($to_check_input, $result_code);
            $query_times--;
            if($query_result === false){

                //用户支付中,2秒以后再次查询
                if ($result_code === 2) {
                    sleep(2);
                    continue;
                }

                //订单不存在了,直接返回支付失败
                if ($result_code === 0) {

                    return false;
                }
            }else{
                //支付成功了,处理支付回调数据
                $callback_data = $this->callback($this->pay_response);

              

                //支付完成以后回调
                $pay_succ_action_result = $this->pay_succ_extends($callback_data);
                //支付成功以后操作(比如更改支付单状态,订单状态等等)失败的话,表示订单支付失败.
                //直接退出循环然后删除微信支付订单
                if($pay_succ_action_result === false){

                    break;
                }

                return true;
            }
        }

        $msg = '订单支付失败';

        //查询10次以后还是失败,删除支付宝订单
        $this->cancel_order($payment_info);

        return false;
    }

    /**
     * 调起微信查询订单支付结果接口
     *
     * @param array $to_check_input 查询接口需要的参数
     * @param int $result_code 结果代码
     *
     * @return bool|mixed
     */
    private function to_check_pay_result($to_check_input, &$result_code){
        try{
            $this->pay_response = $this->curl($this->submit_url, $to_check_input);
        }catch (Exception $e){
            $this->msg = $e->getMessage();

            return false;
        }
        $this->pay_response = iconv($this->charset, $this->file_charset . "//IGNORE", $this->pay_response);

        $this->pay_response = json_decode($this->pay_response, true);

        if($this->pay_response == false ){
            $this->msg = '支付结果不是标准的json格式';

            return false;
        }

        $this->pay_response = $this->pay_response['alipay_trade_pay_response'];

       
        //订单支付成功
        if($this->pay_response['code'] === '10000'){
            $result_code = 1;

            return true;
        }

        //订单还在支付中
        if($this->pay_response['code'] === '10003'){
            $result_code = 2;

            return false;
        }

        //订单支付失败
        if($this->pay_response['code'] === '40004'){
            $result_code = 0;

            return false;
        }

        //未知错误,继续查询
        if($this->pay_response['code'] === '20000'){
            $result_code = 2;

            return false;
        }

        return false;
    }

    /**
     * 递归撤销支付宝订单
     *
     * @param array $payment_info
     * @param int $depth
     *
     * @return bool
     */
    private function cancel_order($payment_info, $depth = 0){

        if($depth > 10){

            return false;
        }

        //构造请求数据
        $cancel_params = [
                'app_id' => $this->app_id,
                'method' => 'alipay.trade.cancel',
                'charset' => $this->charset,
                'sign_type' => 'RSA',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0'
        ];
        $biz_content = [
            'out_trade_no' => $payment_info['bill_id']
        ];
        $cancel_params['biz_content'] = json_encode($biz_content);
        $cancel_params['sign'] = $this->get_sign($this->getSignContent($cancel_params));

        //调起接口
        try{
            $this->pay_response = $this->curl($this->submit_url, $cancel_params);
        }catch (Exception $e){
            $this->msg = $e->getMessage();

            return false;
        }
        $this->pay_response = iconv($this->charset, $this->file_charset . "//IGNORE", $this->pay_response);

        $this->pay_response = json_decode($this->pay_response, true);

        if($this->pay_response == false ){
            $this->msg = '支付结果不是标准的json格式';

            return false;
        }

        //接口调用失败
        if($this->pay_response["code"] === "10000"){

            return true;
        }

        //失败的话,继续调用接口取消
        return $this->cancel_order($payment_info, ++$depth);
    }

    /**
     * 检查是否支付成功
     *
     * @param array $pay_response 支付返回数据
     *
     * @return bool
     */
    private function check_pay_response($pay_response){

        //支付成功
        if($pay_response['code'] === '10000'){

            return true;
        }

        //业务出现未知错误或者系统异常
        if($pay_response['code'] === '20000'){
            $this->msg = "{$pay_response['msg']}({$this->get_error_info($pay_response['sub_code'])})";

            return false;
        }

        //业务处理中
        //该结果码只有在条码支付请求 API 时才返回，代表付款还在进行中，需要调用查询接口查询最终的支付结果
        if($pay_response['code'] === '10003'){
            $this->msg = "{$pay_response['msg']}({$this->get_error_info($pay_response['sub_code'])})";

            return false;
        }

        //业务处理失败
        if($pay_response['code'] === '40004'){
            $this->msg = "{$pay_response['msg']}({$this->get_error_info($pay_response['sub_code'])})";

            return false;
        }

        return false;
    }

    /**
     * 支付成功以后操作(比如更改支付单状态,订单状态等等)
     *
     * @param array $callback_data 支付回调数据
     *
     * @return bool
     */
    private function pay_succ_extends($callback_data){
        //查询支付单数据
        $pay_biil_columns = 'bill_type, pay_object, pay_app_id, order_id';
        $pay_bill_info = app::get('ectools')->model('bills')->getRow($pay_biil_columns, ['bill_id' => $callback_data['bill_id']]);
        if(is_array($pay_bill_info) == false){
            $this->msg = '支付单数据错误';

            return false;
        }

        //合并数据
        $pay_bill_info = array_merge($pay_bill_info, $callback_data);

        //更改支付单状态,进行支付成功后的操作
        $objBill = vmc::singleton('ectools_bill');
        $pay_bill_info['app_id'] = 'b2c';
        $result = $objBill->generate($pay_bill_info, $this->msg);
        if (!$result) {
            logger::error('支付网关回调后，更新或保存支付单据失败！' . $this->msg . '.bill_export:' . var_export($pay_bill_info, 1));

            return false;
        }

        return true;
    }

    /**
     * 调用接口方法
     *
     * @param string $url 接口url
     * @param null $postFields 请求数据
     *
     * @return mixed json字符串
     * @throws Exception
     */
    private function curl($url, $postFields = null){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $postBodyString = "";
        $encodeArray = Array();
        if (is_array($postFields) && count($postFields) > 0) {

            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if (substr($v, 0, 1) != '@') //判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($this->characet($v, $this->charset)) . "&";
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    $encodeArray[$k] = urlencode($this->characet($v, $this->charset));
                }
            }

            curl_setopt($ch, CURLOPT_POST, true);

            if ($postMultipart === true) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }

        //设置header
        $headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->charset);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            throw new Exception(curl_error($ch), 0);
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpStatusCode !== 200) {

            throw new Exception($response, $httpStatusCode);
        }

        curl_close($ch);

        return $response;
    }

    /**
     * 获取签名
     *
     * @param string $data 要签名的数据
     *
     * @return string
     */
    private function get_sign($data) {
        $priKey = file_get_contents($this->get_rsa_cert_file_path('merchant_private_key_file'));
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);

        return $sign;
    }

    /**
     * 数据签名前处理
     *
     * @param $params
     *
     * @return string
     */
    private function getSignContent($params)
    {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);

        return $stringToBeSigned;
    }

    /**
     * 校验$value是否非空
     * if not set ,return true;
     * if is null , return true;
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function checkEmpty($value)
    {
        if (!isset($value)) {

            return true;
        }

        if ($value === null) {

            return true;
        }

        if (trim($value) === "") {

            return true;
        }

        return false;
    }

    /**
     * 转换字符集编码
     *
     * @param string $data 要转化的字符串
     * @param string $targetCharset 目标编码
     *
     * @return string
     */
    private function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->file_charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {

                $data = mb_convert_encoding($data, $targetCharset);
            }
        }

        return $data;
    }

    /**
     * 根据证书类型名称获取证书完整路径
     *
     * @param string $cert_type_name 证书类型名称
     *
     * @return string
     */
    private function get_rsa_cert_file_path($cert_type_name){
        $config = [
            'alipay_public_key_file' => "{$this->cert_path}/alipay_rsa_public_key.pem",
            'merchant_private_key_file' => "{$this->cert_path}/rsa_private_key.pem",
            'merchant_public_key_file' => "{$this->cert_path}/rsa_public_key.pem",
        ];


        return $config[$cert_type_name];
    }

    /**
     * 根据错误代码获取错误信息
     *
     * @param $error_code
     *
     * @return string
     */
    private function get_error_info($error_code){
        $error_info = '未知错误';

        $error_infos = [
            'ACQ.SYSTEM_ERROR'                           => '接口返回错误',//系统超时请立即调用查询订单API，查询当前订单的状态，并根据订单状态决定下一步的操作
            'ACQ.INVALID_PARAMETER'                      => '参数无效',//请求参数未按指引进行编写检查请求参数，修改后重新发起请求
            'ACQ.ACCESS_FORBIDDEN'                       => '无权限使用接口',//未签约条码支付或者合同已到期联系支付宝小二签约条码支付
            'ACQ.EXIST_FORBIDDEN_WORD'                   => '订单信息中包含违禁词',//订单信息中(标题，商品名称，描述等)包含了违禁词修改订单信息后，重新发起请求
            'ACQ.PARTNER_ERROR'                          => '应用APP_ID填写错误',//应用APP_ID填写错误或者对应的APP_ID状态无效联系支付宝小二，确认APP_ID的状态
            'ACQ.TOTAL_FEE_EXCEED'                       => '订单总金额超过限额',//输入的订单总金额超过限额修改订单金额再发起请求
            'ACQ.PAYMENT_AUTH_CODE_INVALID'              => '支付授权码无效',//当前使用的支付授权码无效，可能已被使用过或者输入错误用户刷新条码后，重新扫码发起请求
            'ACQ.CONTEXT_INCONSISTENT'                   => '交易信息被篡改',//该笔交易已存在，但是交易信息匹配不上更换商家订单号后，重新发起请求
            'ACQ.TRADE_HAS_SUCCESS'                      => '交易已被支付',//该笔交易已存在，并且已经支付成功确认该笔交易信息是否为当前买家的，如果是则认为交易付款成功，如果不是则更换商家订单号后，重新发起请求
            'ACQ.TRADE_HAS_CLOSE'                        => '交易已经关闭',//该笔交易已存在，并且该交易已经关闭更换商家订单号后，重新发起请求
            'ACQ.BUYER_BALANCE_NOT_ENOUGH'               => '买家余额不足',//买家余额不足买家绑定新的银行卡或者支付宝余额有钱后再发起支付
            'ACQ.BUYER_BANKCARD_BALANCE_NOT_ENOUGH'      => '用户银行卡余额不足',//用户指定扣款的银行卡中没有钱建议买家更换支付宝进行支付或者更换其它付款方式
            'ACQ.ERROR_BALANCE_PAYMENT_DISABLE'          => '余额支付功能关闭',//用户关闭了余额支付功能用户打开余额支付开关后，再重新进行支付
            'ACQ.BUYER_SELLER_EQUAL'                     => '买卖家不能相同',//交易的买卖家为同一个人更换买家重新付款
            'ACQ.TRADE_BUYER_NOT_MATCH'                  => '交易买家不匹配',//该笔交易已存在，但是交易不属于当前付款的买家更换商家订单号后，重新发起请求
            'ACQ.BUYER_ENABLE_STATUS_FORBID'             => '买家状态非法',//买家的状态不合法，不能进行交易用户联系支付宝小二，确认买家状态为什么非法
            'ACQ.PULL_MOBILE_CASHIER_FAIL'               => '唤起移动收银台失败',//需要唤起用户手机上的收银台进行付款确认，但是唤起时失败用户刷新条码后，重新扫码发起请求
            'ACQ.MOBILE_PAYMENT_SWITCH_OFF'              => '用户的无线支付开关关闭',//用户关闭了无线支付开关用户在PC上打开无线支付开关后，再重新发起支付
            'ACQ.PAYMENT_FAIL'                           => '支付失败支付系统支付过程中失败',//用户刷新条码后，重新发起请求，如果重试一次后仍未成功，更换其它方式付款
            'ACQ.BUYER_PAYMENT_AMOUNT_DAY_LIMIT_ERROR'   => '买家付款日限额超限',//当前买家(用户)当日支付宝付款额度已用完更换买家进行支付
            'ACQ.BEYOND_PAY_RESTRICTION'                 => '商户收款额度超限',//商户收款额度超限联系支付宝小二提高限额
            'ACQ.BEYOND_PER_RECEIPT_RESTRICTION'         => '商户收款金额超过月限额',//商户收款金额超过月限额联系支付宝小二提高限额
            'ACQ.BUYER_PAYMENT_AMOUNT_MONTH_LIMIT_ERROR' => '买家付款月额度超限',//买家本月付款额度已超限让买家更换账号后，重新付款或者更换其它付款方式
            'ACQ.SELLER_BEEN_BLOCKED'                    => '商家账号被冻结',//商家账号被冻结联系支付宝小二，解冻账号
            'ACQ.ERROR_BUYER_CERTIFY_LEVEL_LIMIT'        => '买家未通过人行认证',//当前买家(用户)未通过人行认证让用户联系支付宝小二并更换其它付款方式
            'ACQ.PAYMENT_REQUEST_HAS_RISK'               => '支付有风险',//当前支付行为，支付宝认为有风险更换其它付款方式
            'ACQ.NO_PAYMENT_INSTUMENTS_AVAILABLE'        => '没用可用的支付具',//用户当前没有任何可以用于付款的渠道更换其它付款方式
            'USER_FACE_PAYMENT_SWITCH_OFF'               => '用户当面付付款开关关闭',//用户当面付付款开关关闭让用户在手机上打开当面付付款开关
            'ISV.INVALID-SIGNATURE'                      => '无效的签名'
        ];

        $error_code = strtoupper($error_code);

        if(empty($error_infos[$error_code]) === false){

            $error_info = $error_infos[$error_code];
        }

        return $error_info;
    }
}
