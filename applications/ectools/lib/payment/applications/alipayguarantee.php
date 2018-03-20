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


final class ectools_payment_applications_alipayguarantee extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '支付宝担保交易';
    public $version = 'v15.10.19';
    public $intro = '支付宝www.alipay.com担保易接口(create_partner_trade_by_buyer)';
    public $platform_allow = array(
        'pc',
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

        $this->callback_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'ectools_payment_applications_alipayguarantee' => 'callback',
        ));
        $this->notify_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'ectools_payment_applications_alipayguarantee' => 'notify',
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
                'default' => '支付宝担保交易',
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
            ),
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
    public function dopay($params, &$msg)
    {
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);
        /*
         * 担保交易
         */
        $this->add_field('service', 'create_partner_trade_by_buyer');

        $this->add_field('out_trade_no', $params['bill_id']); //支付单据ID
        $this->add_field('partner', $mer_id);
        $this->add_field('seller_id', $mer_id);
        $this->add_field('payment_type', 1);
        $this->add_field('quantity', 1);
        $this->add_field('price', number_format($params['money'], 2, '.', ''));
        $this->add_field('total_fee', number_format($params['money'], 2, '.', ''));
        $this->add_field('logistics_type', 'EXPRESS'); //EMS \ POST \ EXPRESS
        $this->add_field('logistics_fee', 0);
        $this->add_field('logistics_payment', 'BUYER_PAY');
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

    public function dosendgoods($params, &$msg)
    {
        $mer_id = $this->getConf('mer_id', __CLASS__);
        $mer_key = $this->getConf('mer_key', __CLASS__);
        $ready_send_params = array(
            'service' => 'send_goods_confirm_by_platform',
            'partner' => $mer_id,
            '_input_charset' => 'utf-8',
            'sign_type' => 'MD5',
            'trade_no' => $params['out_trade_no'],
            'logistics_name' => $params['logistics_name'],
            'invoice_no' => $params['logistics_no'],
            'transport_type' => 'EXPRESS',
            'seller_ip' => base_request::get_remote_addr(),
        );
        foreach ($ready_send_params as $key => $value) {
            $this->add_field($key, $value);
        }
        $ready_send_params['sign'] = $this->_get_mac($mer_key);

        $result = vmc::singleton('base_httpclient')->post($this->submit_url, $ready_send_params);
        $result_arr = $this->xml2array($result);
        if (!$result_arr || empty($result_arr)) {
            $msg = '发货请求出错';

            return false;
        }
        if ($result_arr['alipay']['is_success'] == 'F') {
            $msg = 'TRADE_NOT_EXIST';

            return false;
        }
        if ($result_arr['alipay']['is_success'] == 'T') {
            $msg = '发货状态同步到支付宝成功';

            return $result_arr;
        }
    }

    private function xml2array($xml)
    {
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        return $arr;
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
                case 'WAIT_SELLER_SEND_GOODS':
                    $ret['status'] = 'progress';//已付款至支付宝,等待发货
                break;
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
        if ($ret['status'] == 'succ' || $ret['status'] == 'progress') {
            echo 'success';//告知notify 服务不再通知
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
}
