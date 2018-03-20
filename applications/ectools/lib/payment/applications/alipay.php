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


final class ectools_payment_applications_alipay extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '支付宝即时到账';
    public $version = 'v4.8';
    public $intro = '支付宝www.alipay.com即时到账交易接口';
    public $platform_allow = array(
        'pc','mobile','app'
    ); //pc,mobile,app
    /**
     * APP 接入支付宝参考：https://doc.open.alipay.com/docs/doc.htm?treeId=193&articleId=105695&docType=1
     */
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

        $this->callback_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'ectools_payment_applications_alipay' => 'callback',
        ));
        $this->notify_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'ectools_payment_applications_alipay' => 'notify',
        ));
        $this->refund_notify_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'ectools_payment_applications_alipay' => 'refund_notify',
        ));
        $this->submit_url = 'https://mapi.alipay.com/gateway.do?_input_charset=utf-8';
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
                'default' => '支付宝支付',
            ) ,
            'order_num' => array(
                'title' => '排序' ,
                'type' => 'number',
                'default' => 0,
            ) ,
            /*个性化字段开始*/
            'mer_id' => array(
                'title' => '合作者身份(parterID)' ,
                'type' => 'text',
            ) ,
            'mer_key' => array(
                'title' => '交易安全校验码(key)' ,
                'type' => 'text',
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
                'default' => '支付宝支付描述',
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
            // 'compatible_wap' => array(
            //     'title' => '是否兼容手机端' ,
            //     'type' => 'radio',
            //     'options' => array(
            //         'true' => '是' ,
            //         'false' => '否' ,
            //     ) ,
            //     'default' => 'true',
            // ) ,
        );
    }
    /**
     * 提交支付信息的接口.
     *
     * @param array 提交信息的数组
     *
     * @return mixed false or null
     *               //TODO it_b_pay 超时时间传递  \需商户申请开通
     *               //TODO token  支付宝快捷登陆令牌 \需商户申请开通
     */
    public function dopay($params,&$msg)
    {
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);

        if (base_mobiledetect::is_mobile()) {
            //&& $this->getConf('compatible_wap', __CLASS__) == 'true'
            $this->add_field('service', 'alipay.wap.create.direct.pay.by.user');
        }else{
            $this->add_field('service', 'create_direct_pay_by_user');
        }

        $this->add_field('out_trade_no', $params['bill_id']); //支付单据ID
        $this->add_field('partner', $mer_id);
        $this->add_field('seller_id', $mer_id);
        $this->add_field('payment_type', 1);
        $this->add_field('total_fee', number_format($params['money'], 2, '.', ''));
        $this->add_field('return_url', $this->callback_url);
        $this->add_field('notify_url', $this->notify_url);
        $this->add_field('subject', $params['subject'] ? $params['subject'] : $params['order_id']);
        $this->add_field('_input_charset', 'utf-8');
        $this->add_field('sign', $this->_get_mac($mer_key));
        $this->add_field('sign_type', 'MD5');
        if ($this->is_fields_valiad()) {
            // Generate html and send payment.
            //echo "<textarea>";//DEBUG
            //echo __CLASS__;
            //echo 'id:'.$mer_id;
            //echo 'key:'.$mer_key;
            echo $this->get_html();
            //echo "</textarea>";//DEBUG
            exit;
        } else {
            $msg = '支付数据签名失败!';
            return false;
        }
    }

    /**
     * 提交退款信息的接口.
     *
     * @param array 提交信息的数组
     *
     * @return mixed false or null
     */
    public function dorefund($params, &$msg) {
        $mer_id = trim($this->getConf('mer_id', __CLASS__));
        $mer_key = $this->getConf('mer_key', __CLASS__);

        $time = time();
        $this->add_field('service', 'refund_fastpay_by_platform_nopwd');
        $this->add_field('partner', $mer_id);
        $this->add_field('notify_url', $this->refund_notify_url);
        $this->add_field('batch_no', date('Ymd',$time).$params['bill_id']);
        $this->add_field('refund_date', date('Y-m-d H:i:s',$time));
        $this->add_field('batch_num', 1);
        $M = $params['memo']?$params['memo']:'退款';
        $detail_data = $params['transaction_id'].'^'.number_format($params['money'], 2, '.', '').'^'.$M;
        $this->add_field('detail_data', $detail_data);
        $this->add_field('_input_charset', 'utf-8');
        $this->add_field('sign', $this->_get_mac($mer_key));
        $this->add_field('sign_type', 'MD5');
        if ($this->is_fields_valiad()) {
            $sResult = $this->getHttpResponsePOST($this->submit_url,$this->fields);
            if($sResult['is_success'] == 'T') {
                $res_arr['status'] = 'process';
                $res_arr['bill_id'] = $params['bill_id'];
                return $res_arr;
            }elseif($sResult['is_success'] == 'F'){
                $msg = '支付宝错误:'.$this->error_code($sResult['error']);
                return false;
            }else{
                $msg = $sResult['error']?$this->error_code($sResult['error']):'处理中';
                return false;
            }
        } else {
            $msg = '支付数据签名失败!';
            return false;
        }
    }

    /**
     * 远程获取数据，POST模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * @param $para 请求的数据
     * @param $input_charset 编码格式。默认值：空值
     * return 远程输出的数据
     */
    function getHttpResponsePOST($url, $para) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO,dirname(__FILE__).'/../cert/alipay/cacert.pem');//证书地址
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl,CURLOPT_POST,true); // post传输数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        $responseText = json_decode(json_encode(simplexml_load_string($responseText, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $responseText;
    }

    /**
     * 支付平台同步跳转处理.
     *
     * @params array - 所有返回的参数，包括POST和GET
     */
    public function callback(&$params)
    {
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);
        $ret['bill_id'] = $params['out_trade_no']; //原样返回提交的支付单据ID
        $ret['payee_account'] = $params['seller_email']; //收款者（卖家）账户
        $ret['payee_bank'] = $this->name; //收款者（卖家）银行
        $ret['payer_account'] = $params['buyer_email']; //付款者（买家）账户
        $ret['payer_bank'] = $this->name; //付款者（买家）银行
        $ret['money'] = $params['total_fee'];
        $ret['out_trade_no'] = $params['trade_no']; //支付平台交易号

        if ($this->is_return_vaild($params, $mer_key)) {

            switch ($params['trade_status']) { //交易状态

                case 'TRADE_FINISHED':
                case 'TRADE_SUCCESS':
                case 'TRADE_SUCCES':
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

    /**
     * 支付平台无密退款 异步处理.
     *
     * @params array - 所有返回的参数，包括POST和GET
     */
    public function refund_notify(&$params) {
        $mer_key = $this->getConf('mer_key', __CLASS__);
        if ($this->is_return_vaild($params, $mer_key)) {
            $mdl_bills = app::get('ectools')->model('bills');
            $result_details = explode('^',$params['result_details']);//退款交易号  2016072821001004860242175248^0.01^SUCCESS
            $ret['bill_id'] = substr($params['batch_no'],8); //退款批次号
            switch ($result_details[2]) { //交易状态
                case 'SUCCESS':
                    $ret['status'] = 'succ';
                    $ret['payee_bank'] = $this->name;
                    $ret['payer_bank'] = $this->name;
                    echo 'success';//告知notify 服务不再通知
                    break;
                default:
                    $ret['status'] = 'error';
                    $bill = $mdl_bills->getRow('memo',array('bill_id'=>$ret['bill_id']));
                    $memo = $bill['memo'];
                    $memo .= $this->error_code($result_details[2]);
                    if(!$mdl_bills->update(array('memo'=>$memo),array('bill_id'=>$ret['bill_id'])) ){
                        logger::error("修改订单备注错误：支付宝原路返还异步失败未知bill_id:{$ret['bill_id']}".var_export($params,true));
                    }
                    echo 'success';//告知notify 服务不再通知
            }
        }else{
            logger::error("支付宝原路返还异步失败签名错误：".var_export($params,true));
            $ret['status'] = 'invalid'; //非法参数
        }
        return $ret;
    }

    /**
     * 生成签名.
     *
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key  签名用到的私钥
     *
     * @return string
     */
    private function _get_mac($key)
    {
        ksort($this->fields);
        reset($this->fields);
        $mac = '';
        foreach ($this->fields as $k => $v) {
            $mac .= "&{$k}={$v}";
        }
        $mac = substr($mac, 1);
        $mac = md5($mac.$key); //验证信息
        return $mac;
    }
    /**
     * 检验返回数据合法性.
     *
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key  签名用到的私钥
     *
     * @return bool
     */
    private function is_return_vaild($form, $key)
    {
        ksort($form);
        foreach ($form as $k => $v) {
            if ($k != 'sign' && $k != 'sign_type') {
                $signstr .= "&$k=$v";
            }
        }
        $signstr = ltrim($signstr, '&');
        $signstr = $signstr.$key;
        if ($form['sign'] == md5($signstr)) {
            return true;
        }
        //TODO 支付结果处理失败
        return false;
    }

    private function error_code($code) {
        $error_code = array(
            'ILLEGAL_SIGN' => '签名不正确',
            'ILLEGAL_DYN_MD5_KEY'=> '动态密钥信息错误',
            'ILLEGAL_ENCRYPT'=> '加密不正确',
            'ILLEGAL_ARGUMENT'=> '参数不正确',
            'ILLEGAL_SERVICEService'=>'参数不正确',
            'ILLEGAL_USER'=> '用户ID 不正确',
            'ILLEGAL_PARTNER'=> '合作伙伴ID 不正确',
            'ILLEGAL_EXTERFACE'=> '接口配置不正确',
            'ILLEGAL_PARTNER_EXTERFACE'=> '合作伙伴接口信息不正确',
            'ILLEGAL_SECURITY_PROFILE'=> '未找到匹配的密钥配置',
            'ILLEGAL_AGENT'=> '代理ID 不正确',
            'ILLEGAL_SIGN_TYPE'=> '签名类型不正确',
            'ILLEGAL_CHARSET'=> '字符集不合法',
            'ILLEGAL_CLIENT_IP'=> '客户端IP 地址无权访问服务',
            'HAS_NO_PRIVILEGE'=> '无权访问',
            'ILLEGAL_DIGEST_TYPE'=> '摘要类型不正确',
            'ILLEGAL_DIGEST'=> '文件摘要不正确',
            'ILLEGAL_FILE_FORMAT'=> '文件格式不正确',
            'ILLEGAL_ENCODING'=> '不支持该编码类型',
            'ILLEGAL_REQUEST_REFERER'=>'防钓鱼检查不支持该请求来源',
            'ILLEGAL_ANTI_PHISHING_KEY'=> '防钓鱼检查非法时间戳参数',
            'ANTI_PHISHING_KEY_TIMEOUT'=>' 防钓鱼检查时间戳超时',
            'ILLEGAL_EXTER_INVOKE_IP'=>' 防钓鱼检查非法调用IP',
            'BATCH_NUM_EXCEED_LIMIT'=>' 总笔数大于1000',
            'REFUND_DATE_ERROR'=>' 错误的退款时间',
            'BATCH_NUM_ERROR'=>'传入的总笔数格式错误',
            'DUBL_ROYALTY_IN_DETAIL'=>'同一条明细中存在两条转入转出账户相同的分润信息',
            'BATCH_NUM_NOT_EQUAL_TOTAL'=>'传入的退款条数不等于数据集解析出的退款条数',
            'SINGLE_DETAIL_DATA_EXCEED_LIMIT'=>'单笔退款明细超出限制',
            'DUBL_TRADE_NO_IN_SAME_BATCH'=>'同一批退款中存在两条相同的退款记录',
            'DUPLICATE_BATCH_NO'=>'重复的批次号',
            'TRADE_STATUS_ERROR' => '交易状态不允许退款',
            'BATCH_NO_FORMAT_ERROR'=>'批次号格式错误',
            'PARTNER_NOT_SIGN_PROTOCOL '=>'平台商未签署协议',
            'NOT_THIS_PARTNERS_TRADE'=>'退款明细非本合作伙伴的交易',
            'DETAIL_DATA_FORMAT_ERROR'=>'数据集参数格式错误',
            'SELLER_NOT_SIGN_PROTOCOL'=>'卖家未签署协议',
            'INVALID_CHARACTER_SET'=>'字符集无效',
            'ACCOUNT_NOT_EXISTS'=>'账号不存在',
            'EMAIL_USERID_NOT_MATCH Email'=>'和用户ID 不匹配',
            'REFUND_ROYALTY_FEE_ERROR'=>'退分润金额不合法',
            'ROYALTYER_NOT_SIGN_PROTOCOL'=>'分润方未签署三方协议',
            'RESULT_AMOUNT_NOT_VALID'=>'退收费、退分润或者退款的金额错误',
            'REASON_REFUND_ROYALTY_ERROR'=>'退分润错误',
            'TRADE_NOT_EXISTS'=>'交易不存在',
            'WHOLE_DETAIL_FORBID_REFUND'=>'整条退款明细都禁止退款',
            'TRADE_HAS_CLOSED'=>'交易已关闭，不允许退款',
            'TRADE_HAS_FINISHED'=>'交易已结束，不允许退款',
            'NO_REFUND_CHARGE_PRIVILEDGE'=>'没有退收费的权限',
            'RESULT_BATCH_NO_FORMAT_ERROR'=>'批次号格式错误',
            'BATCH_MEMO_LENGTH_EXCEED_LIMIT'=>'备注长度超过256 字节',
            'REFUND_CHARGE_FEE_GREATER_THAN_LIMIT'=>'退收费金额超过限制',
            'REFUND_TRADE_FEE_ERROR'=>'退交易金额不合法',
            'SELLER_STATUS_NOT_ALLOW'=>'卖家状态不正常',
            'SINGLE_DETAIL_DATA_ENCODING_NOT_SUPPORT'=>'单条数据集编码集不支持',
            'TXN_RESULT_ACCOUNT_STATUS_NOT_VALID'=>'卖家账户状态无效或被冻结',
            'TXN_RESULT_ACCOUNT_BALANCE_NOT_ENOUGH'=>'卖家账户余额不足',
            'CA_USER_NOT_USE_CA'=>'数字证书用户但未使用数字证书登录',
            'BATCH_REFUND_LOCK_ERROR'=>'同一时间不允许进行多笔并发退款',
            'REFUND_SUBTRADE_FEE_ERROR'=>'退子交易金额不合法',
            'NANHANG_REFUND_CHARGE_AMOUNT_ERROR'=>'退票面价金额不合法',
            'REFUND_AMOUNT_NOT_VALID'=>'退款金额不合法',
            'TRADE_PRODUCT_TYPE_NOT_ALLOW_REFUND'=>'交易类型不允许退交易',
            'RESULT_FACE_AMOUNT_NOT_VALID'=>'退款票面价不能大于支付票面价',
            'REFUND_CHARGE_FEE_ERROR'=>'退收费金额不合法',
            'REASON_REFUND_CHARGE_ERR'=>'退收费失败',
            'DUP_ROYALTY_REFUND_ITEM'=>'重复的退分润条目',
            'RESULT_ACCOUNT_NO_NOT_VALID'=>'账号无效',
            'REASON_TRADE_REFUND_FEE_ERR'=>'退款金额错误',
            'REASON_HAS_REFUND_FEE_NOT_MATCH'=>'已退款金额错误',
            'REASON_REFUND_AMOUNT_LESS_THAN_COUPON_FEE'=>'红包无法部分退款',
            'BATCH_REFUND_STATUS_ERROR'=>'退款记录状态错误',
            'BATCH_REFUND_DATA_ERROR'=>'批量退款后数据检查错误',
            'REFUND_TRADE_FAILED'=>'不存在退交易，但是退收费和退分润失败',
            'REFUND_FAIL'=>'退款失败（该结果码只会出现在做意外数据恢复时，找不到结果码的情况）',
            'ELLER_CONFLICT'=>'因商户账户手机登录号被释放，暂无法收付款。为保障资金安全，建议修改账户名。',
            'ROYALTY_RECEIVE_EMAIL_CONFLICT'=>'因分润收款账户手机登录号被释放，无法进行收付款。为了保障资金安全，建议修改账户名。',
            'ROYALTY_PAY_EMAIL_CONFLICT'=>'因分润付款账户手机登录号被释放，无法进行收付款。为了保障资金安全，建议修改账户名。',
            'TRADE_PRODUCT_NOT_ALLOW_REFUND'=>'交易产品不允许退款',
            'BUYER_ERROR'=>'因买家支付宝账户问题不允许退款',
        );
        return $error_code[$code];
    }
}
