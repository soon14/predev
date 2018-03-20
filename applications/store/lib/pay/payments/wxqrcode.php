<?php

/**
 * 微信扫码支付
 *
 * Class store_pay_payment_wxqrcode
 */
class store_pay_payments_wxqrcode extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '店铺微信扫码支付';
    public $version = 'v1.0';
    public $intro = '店铺微信扫码支付';
    public $platform_allow = [
        'store',
    ]; //pc,mobile,app,store

    private $msg = '';

    /**
     * 公众账号
     *
     * @var string
     */
    private $public_number;
    /**
     * 公众账号ID
     *
     * @var string
     */
    private $app_id = '';
    /**
     * 商户号
     *
     * @var string
     */
    private $mch_id = '';
    /**
     * 商户密钥
     *
     * @var string
     */
    private $mch_key = '';
    /**
     * 查询订单支付结果url
     *
     * @var string
     */
    private $check_pay_result_url = '';
    /**
     * 撤销微信支付订单API接口
     *
     * @var string
     */
    private $reverse_pay_url = '';
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
     * 构造方法.
     *
     * @param object $app
     *
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->cert_path = ROOT_DIR . '/applications/store/statics/cert';

        //这个支付方式用不到
        $this->callback_url = '';//vmc::openapi_url('openapi.ectools_payment', 'getway_callback', ['store_pay_payments_wxqrcode' => 'callback']);

        //这个支付方式用不到
        $this->notify_url = '';//vmc::openapi_url('openapi.ectools_payment', 'getway_callback', ['store_pay_payments_wxqrcode' => 'notify']);

        $this->submit_url = 'https://api.mch.weixin.qq.com/pay/micropay';
        $this->check_pay_result_url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $this->reverse_pay_url = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
        $this->submit_method = 'POST';
        $this->submit_charset = 'utf-8';

        //公众账号
        //$this->public_number = $this->getConf('public_number',      __CLASS__);

        //公众账号ID
        $this->app_id = $this->getConf('appid',      __CLASS__);

        //商户号
        $this->mch_id = $this->getConf('mch_id',      __CLASS__);

        //商户密钥
        $this->mch_key = $this->getConf('mch_key',      __CLASS__);
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
        //获取微信公众号里配置的微信公众号信息
        //$publicNumbers = $this->getPublicNumbers();

        $settingArray = array(
            'display_name'  => array(
                'title'         => '支付方式名称',
                'type'          => 'text',
                'validate_type' => 'required',
                'default'       => '店铺微信扫码支付',
            ),
//            'public_number' => array(
//                'title'   => '选择公众账号',
//                'type'    => 'select',
//                'options' => $publicNumbers
//            ),
            'order_num'     => array(
                'title'   => '排序',
                'type'    => 'number',
                'default' => 0,
            ),
            'appid'         => array(
                'title' => 'appid',
                'type'  => 'text',
            ),
            'appsecret'     => array(
                'title' => 'appsecret',
                'type'  => 'text',
            ),
            'mch_id'        => array(
                'title' => 'mch_id(商户号)',
                'type'  => 'text',
            ),
            'mch_key'       => array(
                'title' => 'API密钥',
                'type'  => 'text'
            ),
            'pay_fee'       => array(
                'title'   => '交易费率 (%)',
                'type'    => 'text',
                'default' => 0,
            ),
            'description'   => array(
                'title'   => '支付方式描述',
                'type'    => 'html',
                'default' => '店铺微信扫码支付',
            ),
            'status'        => array(
                'title'   => '是否开启此支付方式',
                'type'    => 'radio',
                'options' => array(
                    'true'  => '是',
                    'false' => '否',
                ),
                'default' => 'true',
            )
        );

        return $settingArray;
    }

    /**
     * 支付表单的提交方式
     *
     * @param array $payments 提交的表单数据
     * @param string $msg
     *
     * @return mixed - 自动提交的表单
     */
    public function dopay($payments, &$msg)
    {
        $this->add_field('appid', $this->app_id);
        $this->add_field('mch_id', $this->mch_id);
        $this->add_field('nonce_str', $this->getNonceStr());
        $this->add_field('body', '网店订单');
        $this->add_field('attach', $payments['order_id']);
        $this->add_field('out_trade_no', $payments['bill_id']);
        $this->add_field('total_fee', ceil(bcmul($payments['money'], 100, 3)));
        $this->add_field('spbill_create_ip', /*$_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : */'127.0.0.1');
        $this->add_field('auth_code', $payments['auth_code']);

        $this->add_field('sign', $this->get_sign($this->mch_key, $this->fields));

        $pay_xml = $this->array2xml($this->fields);
        if($pay_xml === false){
            $msg = $this->msg;

            return false;
        }


        //调起支付
        $this->pay_response = $this->postXmlCurl($pay_xml, $this->submit_url);
        $this->pay_response = $this->xml2array($this->pay_response);

        //检查支付结果
        $check_result = $this->check_pay_response($this->pay_response);
        if($check_result === false){
            $msg = $this->msg;
            if(!$this ->check_pay_result($payments ,$msg)){
                return false;
            }
            return true;
        }

        //处理支付回调数据
        $callback_data = $this->callback($this->pay_response);

        //支付完成以后回调
        return $this->pay_succ_extends($callback_data);
    }

    /**
     * 循环查询微信订单是否支付成功
     *
     * @param $payment_info
     * @param string $msg
     *
     * @return bool
     */
    public function check_pay_result($payment_info, &$msg = ''){

        //构造查询订单支付结果需要的参数
        $to_check_input = [
            'appid' => $this->app_id,
            'mch_id' => $this->mch_id,
            'out_trade_no' => $payment_info['bill_id'],
            'nonce_str' => $this->getNonceStr(),
        ];
        $to_check_input['sign'] = $this->get_sign($this->mch_key, $to_check_input);

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

        //查询10次以后还是失败,删除微信订单
        $this->cancel_wx_order($payment_info);

        return false;
    }

    /**
     * 支付后返回后处理的事件的动作
     *
     * @param array $params 所有返回的参数，包括POST和GET
     *
     * @return null
     */
    public function callback(&$params)
    {
        $ret['bill_id'] = $params['out_trade_no'];//原样返回提交的支付单据ID
        $ret['payee_account'] = $params['appid'];//收款者（卖家）账户(appid)
        $ret['payee_bank'] = '南宁百货O2O';//$this->public_number;//收款者（卖家）银行(微信公众号号码)
        $ret['payer_account'] = $params['openid'];//付款者（买家）账户(买家openid)
        $ret['payer_bank'] = $params['bank_type'];//付款者（买家）银行
        $ret['money'] = bcdiv($params['cash_fee'], 100, 3);//支付金额
        $ret['out_trade_no'] = $params['transaction_id'];//支付平台交易号(微信支付订单号)
        $ret['order_id'] = $params['attach'];//支付订单id
        $ret['status'] = 'succ';

        return $ret;
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
     * 获取请求支付接口的响应数据
     *
     * @return array
     */
    public function get_pay_response(){

        $response = [
            'pay_method' => 'wxqrcode',
            'err_code' => $this->pay_response['err_code'],
            'err_code_des' => $this->pay_response['err_code_des'],
            'result_code' => $this->pay_response['result_code'],
            'return_code' => $this->pay_response['return_code'],
            'return_msg' => $this->pay_response['return_msg'],
        ];

        return $response;
    }

    /**
     * 生成签名.
     *
     * @param string $key 签名用到的私钥
     * @param array $fields 需要签名的数据
     *
     * @return string
     */
    private function get_sign($key, $fields = [])
    {
        ksort($fields);

        $sign = '';
        foreach ($fields as $k => $v) {
            $sign .= "&{$k}={$v}";
        }
        $sign = substr($sign, 1);

        $sign = md5($sign . '&key=' . $key);

        return strtoupper($sign);
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool|false $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     *
     * @return mixed
     */
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验

        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);

        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //设置证书
        if ($useCert == true) {
            #使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->cert_path);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->cert_path);
        }

        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        //运行curl
        $data = curl_exec($ch);

        //返回结果
        if (!$data) {
            $error = curl_errno($ch);
            logger::error('微信支付CURL出错' . $error);
        }

        curl_close($ch);

        return $data;
    }

    /**
     * 获取微信公众号里配置的微信公众号信息
     *
     * @return array
     */
    private function getPublicNumbers()
    {
        $publicNumbers = [];
        $modelWeixinBind = app::get('wechat')->model('bind');
        // 公众账号
        $publicNumbersInfos = $modelWeixinBind->getList('wechat_id, name', array('wechat_id|noequal' => ''));
        if (is_array($publicNumbersInfos) === false) {

            return $publicNumbers;
        }

        foreach ($publicNumbersInfos as $publicNumbersInfo) {
            $publicNumbers[$publicNumbersInfo['wechat_id']] = $publicNumbersInfo['name'];
        }

        return $publicNumbers;
    }

    /**
     * xml字符串转化为数组
     *
     * @param string $xml
     *
     * @return mixed
     */
    private function xml2array($xml){
        libxml_disable_entity_loader(true);
        $xml2array = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if(!$xml2array){

            return [];
        }

        $xml2array = json_decode(json_encode($xml2array), true);

        return $xml2array;
    }

    /**
     * 输出xml字符
     *
     * @param array $array
     *
     * @return string
     */
    private function array2xml($array)
    {
        if (!is_array($array) || count($array) <= 0) {
            $this->msg = '支付数据异常';

            return false;
        }

        $xml = "<xml>";
        foreach ($array as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";


        return $xml;
    }

    /**
     * 产生随机字符串，不长于32位
     *
     * @return string 产生的随机字符串
     */
    private function getNonceStr()
    {
        return md5(uniqid('wxqrcode', true));
    }

    /**
     * 检查是否支付成功
     *
     * @param array $pay_result 支付返回数据
     *
     * @return bool
     */
    private function check_pay_response($pay_result){


        if($pay_result['return_code'] !== 'SUCCESS'){
            $this->msg = $pay_result['return_msg'];

            return false;
        }

        if($pay_result["result_code"] == "FAIL" && $pay_result["err_code"] != "USERPAYING" && $pay_result["err_code"] != "SYSTEMERROR")
        {
            $this->msg = $this->get_error_info($pay_result['err_code']);

            return false;
        }

        return true;
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
        $paybiilColumns = 'bill_type, pay_object, pay_app_id';
        $pay_bill_info = app::get('ectools')->model('bills')->getRow($paybiilColumns, ['bill_id' => $callback_data['bill_id']]);
        if(is_array($pay_bill_info) === false){
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
     * 调起微信查询订单支付结果接口
     *
     * @param array $to_check_input 查询接口需要的参数
     * @param int $result_code 结果代码
     *
     * @return bool|mixed
     */
    private function to_check_pay_result($to_check_input, &$result_code){
        $xml = $this->array2xml($to_check_input);
        $this->pay_response = $this->postXmlCurl($xml, $this->check_pay_result_url);
        $this->pay_response = $this->xml2array($this->pay_response);

        if ($this->pay_response["return_code"] == "SUCCESS" && $this->pay_response["result_code"] == "SUCCESS") {
            //支付成功
            if ($this->pay_response["trade_state"] === "SUCCESS") {
                $result_code = 1;

                return $this->pay_response;
            }
            else if ($this->pay_response["trade_state"] == "USERPAYING") //用户支付中
            {
                $result_code = 2;

                return false;
            }
        }

        //如果返回错误码为“此交易订单号不存在”则直接认定失败
        if ($this->pay_response["err_code"] == "ORDERNOTEXIST") {
            $result_code = 0;

            return false;
        }

        //如果是系统错误，则后续继续
        $result_code = 2;

        return false;
    }

    /**
     * 递归撤销微信支付订单
     *
     * @param array $payment_info
     * @param int $depth
     *
     * @return bool
     */
    private function cancel_wx_order($payment_info, $depth = 0){

        if($depth > 10){

            return false;
        }

        //构造请求数据
        $cancel_params = [
            'appid' => $this->app_id,
            'mch_id' => $this->mch_id,
            'out_trade_no' => $payment_info['bill_id'],
            'nonce_str' => $this->getNonceStr(),
        ];
        $cancel_params['sign'] = $this->get_sign($this->mch_key, $cancel_params);

        //调起接口
        $xml = $this->array2xml($cancel_params);
        $response = $this->postXmlCurl($xml, $this->reverse_pay_url, true, 6);
        $result = $this->xml2array($response);

        //接口调用失败
        if($result["return_code"] != "SUCCESS"){

            return false;
        }

        //如果结果为success且不需要重新调用撤销，则表示撤销成功
        if($result["result_code"] != "SUCCESS" && $result["recall"] == "N"){

            return true;
        }

        if($result["recall"] == "Y") {

            return $this->cancel_wx_order($payment_info, ++$depth);
        }

        return false;
    }

    /**
     * 根据错误代码获取错误信息
     *
     * @param $error_code
     *
     * @return string
     */
    private function get_error_info($error_code){

        $error_infos = [
            'SYSTEMERROR'           => '接口返回错误',//支付结果未知 	系统超时 	请立即调用被扫订单结果查询API，查询当前订单状态，并根据订单的状态决定下一步的操作。',
            'PARAM_ERROR'           => '参数错误',//支付确认失败 	请求参数未按指引进行填写 	请根据接口返回的详细信息检查您的程序',
            'ORDERPAID'             => '订单已支付',//支付确认失败 	订单号重复 	请确认该订单号是否重复支付，如果是新单，请使用新订单号提交',
            'NOAUTH'                => '商户无权限',//支付确认失败 	商户没有开通被扫支付权限 	请开通商户号权限。请联系产品或商务申请',
            'AUTHCODEEXPIRE'        => '二维码已过期，请用户在微信上刷新后再试',//支付确认失败 	用户的条码已经过期 	请收银员提示用户，请用户在微信上刷新条码，然后请收银员重新扫码。 直接将错误展示给收银员',
            'NOTENOUGH'             => '余额不足',//支付确认失败 	用户的零钱余额不足 	请收银员提示用户更换当前支付的卡，然后请收银员重新扫码。建议：商户系统返回给收银台的提示为“用户余额不足.提示用户换卡支付”',
            'NOTSUPORTCARD'         => '不支持卡类型',//	支付确认失败 	用户使用卡种不支持当前支付形式 	请用户重新选择卡种 建议：商户系统返回给收银台的提示为“该卡不支持当前支付，提示用户换卡支付或绑新卡支付”',
            'ORDERCLOSED'           => '订单已关闭',//支付确认失败 	该订单已关 商户订单号异常，请重新下单支付',
            'ORDERREVERSED'         => '订单已撤销',//支付确认失败 	当前订单已经被撤销 	当前订单状态为“订单已撤销”，请提示用户重新支付',
            'BANKERROR'             => '银行系统异常',//	支付结果未知 	银行端超时 	请立即调用被扫订单结果查询API，查询当前订单的不同状态，决定下一步的操作。',
            'USERPAYING'            => '用户支付中，需要输入密码',//支付结果未知 	该笔交易因为业务规则要求，需要用户输入支付密码。 	等待5秒，然后调用被扫订单结果查询API，查询当前订单的不同状态，决定下一步的操作。',
            'AUTH_CODE_ERROR'       => '授权码参数错误',//支付确认失败 	请求参数未按指引进行填写 	每个二维码仅限使用一次，请刷新再试',
            'AUTH_CODE_INVALID'     => '授权码检验错误',//支付确认失败 	收银员扫描的不是微信支付的条码 	请扫描微信支付被扫条码/二维码',
            'XML_FORMAT_ERROR'      => 'XML格式错误',//支付确认失败 	XML格式错误 	请检查XML参数格式是否正确',
            'REQUIRE_POST_METHOD'   => '请使用post方法',//支付确认失败 	未使用post传递参数 	请检查请求参数是否通过post方法提交',
            'SIGNERROR'             => '签名错误',//支付确认失败 	参数签名结果不正确 	请检查签名参数和方法是否都符合签名算法要求',
            'LACK_PARAMS'           => '缺少参数',//支付确认失败 	缺少必要的请求参数 	请检查参数是否齐全',
            'NOT_UTF8'              => '编码格式错误',//支付确认失败 	未使用指定编码格式 	请使用UTF-8编码格式',
            'BUYER_MISMATCH'        => '支付帐号错误',//支付确认失败 	暂不支持同一笔订单更换支付方 	请确认支付方是否相同',
            'APPID_NOT_EXIST'       => 'APPID不存在',//支付确认失败 	参数中缺少APPID 	请检查APPID是否正确',
            'MCHID_NOT_EXIST'       => 'MCHID不存在',//支付确认失败 	参数中缺少MCHID 	请检查MCHID是否正确',
            'OUT_TRADE_NO_USED'     => '商户订单号重复',//支付确认失败 	同一笔交易不能多次提交 	请核实商户订单号是否重复提交',
            'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',//支付确认失败 	appid和mch_id不匹配 	请确认appid和mch_id是否匹配',
        ];

        $error_info = '未知错误';

        if(isset($error_infos[$error_code]) === true){
            $error_info = $error_infos[$error_code];
        }

        return $error_info;
    }
}

/**
 * 提交刷卡支付API输入参数:
 *
    公众账号ID 	appid 	String(32) 	是 	wx8888888888888888 	微信分配的公众账号ID（企业号corpid即为此appId）
    商户号 	mch_id 	String(32) 	是 	1900000109 	微信支付分配的商户号
    设备号 	device_info 	String(32) 	否 	013467007045764 	终端设备号(商户自定义，如门店编号)
    随机字符串 	nonce_str 	String(32) 	是 	5K8264ILTKCH16CQ2502SI8ZNMTM67VS 	随机字符串，不长于32位。推荐随机数生成算法
    签名 	sign 	String(32) 	是 	C380BEC2BFD727A4B6845133519F3AD6 	签名，详见签名生成算法
    商品描述 	body 	String(32) 	是 	Ipadmini16G白色 	商品或支付单简要描述
    商品详情 	detail 	String(8192) 	否 	Ipadmini16G白色 	商品名称明细列表
    附加数据 	attach 	String(127) 	否 	说明 	附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    商户订单号 	out_trade_no 	String(32) 	是 	1217752501201407033233368018 	商户系统内部的订单号,32个字符内、可包含字母,其他说明见商户订单号
    总金额 	total_fee 	Int 	是 	888 	订单总金额，单位为分，只能为整数，详见支付金额
    货币类型 	fee_type 	String(16) 	否 	CNY 	符合ISO4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
    终端IP 	spbill_create_ip 	String(16) 	是 	8.8.8.8 	调用微信支付API的机器IP
    商品标记 	goods_tag 	String(32) 	否 		商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
    指定支付方式 	limit_pay 	String(32) 	否 	no_credit 	no_credit--指定不能使用信用卡支付
    授权码 	auth_code 	String(128) 	是 	120061098828009406 	扫码支付授权码，设备读取用户微信中的条码或者二维码信息
 */

/**
 * 提交刷卡支付API支付成功返回参数:
    appid : wx426b3015555a46be
    attach : Array
    bank_type : CMB_CREDIT
    cash_fee : 1
    fee_type : CNY
    is_subscribe : N
    mch_id : 1225312702
    nonce_str : W7EMRlpJvfzefYhS
    openid : oHZx6uCAU_k8JXUrDZMgZVU0_uBI
    out_trade_no : 122531270220151204173928
    result_code : SUCCESS
    return_code : SUCCESS
    return_msg : OK
    sign : 097B15D27AD553B9D05A806343FE16B0
    time_end : 20151204173928
    total_fee : 1
    trade_state : SUCCESS
    trade_type : MICROPAY
    transaction_id : 1005560492201512041901722421
 */
