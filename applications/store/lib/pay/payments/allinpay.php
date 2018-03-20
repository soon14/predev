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


final class store_pay_payments_allinpay extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '店铺O2O跨境通联支付';
    public $version = 'v1.0';
    public $intro = '店铺O2O跨境通联支付';
    public $platform_allow = array(
        'store',
    ); //pc,mobile,app,store
//    private $push_url = 'http://120.197.60.33:99/Service.svc/AddOrderForm';
//    private $sign_url = "http://120.197.60.33:99/Service.svc/CheckSign";
//    private $MachineCode = '123456789';
//    private $key = '9781e7b9';
//    private $KJTUserName = 'GZKJTStore';

    private $push_url = 'http://www.cn02020.com:99/Service.svc/AddOrderForm';
    private $sign_url = "http://www.cn02020.com:99/Service.svc/CheckSign";
    private $MachineCode = '1453834374605';
    private $key = 'mGk89e$t';
    private $KJTUserName = 'GZKJTStore';
    private $SubmitDate = '';

    public function __construct($app)
    {
        parent::__construct($app);

        $this->SubmitDate = date("YmdHis");

        $this->callback_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'store_pay_payments_allinpay' => 'callback',
        ));
        $this->notify_url = vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
            'store_pay_payments_allinpay' => 'notify',
        ));
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
                'default' => '店铺O2O跨境通联支付',
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
                'default' => '店铺O2O跨境通联支付',
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


    /**订单推送
     * @param $params
     * @param $msg
     * @return bool|mixed
     */
    public function push_order($params, &$msg)
    {
        $data = $this ->get_order_data( $params);

        //==========以下为测试数据
        //    $data = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'order_test');
        //    $data = json_decode(stripcslashes($data), true);
        //    var_export($data);
        //==========以上为测试数据


        $data['MachineCode'] = $this->MachineCode;
        $data['Mac'] = $this->make_mac();
        $data['SubmitDate'] = $this->SubmitDate;
        logger::alert(var_export(json_encode($data),1));
        $res = $this->http_post_data($this->push_url, json_encode($data));
//        vmc::dump($res);
        $res = json_decode(json_decode($res, true), true);
        if ($res['Code'] == '0000' && $this->check_return($res)) {
            $content = $this->decode($res['JsonContent'], $this->key);

            return json_decode($content, true);
        } else {
            $msg = $res['Msg'];

            return false;
        }
    }

    /**
     * 提交支付信息的接口.
     *
     * @param array $params 提交信息的数组
     * @param string $msg 错误信息
     *
     * @return mixed false or null
     */
    public function dopay($params, &$msg)
    {
        if (!$params['out_trade_no']) {
            $msg = '请输入支付流水号';

            return false;
        }

        //构造回调数据
        $callbackData = array(
            'bill_id' => $params['bill_id'],//支付流水号
            'seller_email' => 'allinpay',//收款者（卖家）账户
            'payee_bank' => 'allinpay',//收款者（卖家）银行
            'buyer_email' => 'allinpay',//付款者（买家）账户
            'payer_bank' => 'allinpay',//付款者（买家）银行
            'total_fee' => $params['money'],//支付金额
            'out_trade_no' => $params['out_trade_no'],//支付平台交易号
            'order_id' => $params['order_id'],//支付订单id
        );

        //处理回调数据
        $payResult = $this->callback($callbackData);

        //查询支付单数据
        $paybiilColumns = 'bill_type, pay_object, pay_app_id';
        $paybillInfo = app::get('ectools')->model('bills')->getRow($paybiilColumns,
            array('bill_id' => $params['bill_id']));
        if (is_array($paybillInfo) === false) {
            $msg = '支付单数据错误';

            return false;
        }
        $paybillInfo = array_merge($paybillInfo, $payResult);

        //更改支付单状态,进行支付成功后的操作
        $objBill = vmc::singleton('ectools_bill');
        $paybillInfo['app_id'] = 'b2c';
        $result = $objBill->generate($paybillInfo, $msg);
        if (!$result) {
            logger::error('支付网关回调后，更新或保存支付单据失败！' . $msg . '.bill_export:' . var_export($payResult, 1));

            return false;
        }

        return true;
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
        $params = json_decode($params, true);
        logger::alert("异步请求参数" . date('ymd:h-i-sa') . var_export($params, 1));
        $ret['bill_id'] = $params['OrderFormNo'];
        $ret['payee_account'] = 'allinpay';
        $ret['payee_bank'] = 'allinpay';
        $ret['payer_account'] = 'allinpay';
        $ret['payer_bank'] = 'allinpay';
        $ret['money'] = $params['TotalMoney'];
        $ret['out_trade_no'] = $params['PayFlowNum'];
        echo 'OK';
        return $ret;
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
        $ret['bill_id'] = $params['bill_id']; //原样返回提交的支付单据ID
        $ret['payee_account'] = $params['seller_email']; //收款者（卖家）账户
        $ret['payee_bank'] = $params['payee_bank']; //收款者（卖家）银行
        $ret['payer_account'] = $params['buyer_email']; //付款者（买家）账户
        $ret['payer_bank'] = $params['payer_bank']; //付款者（买家）银行
        $ret['money'] = $params['total_fee'];
        $ret['out_trade_no'] = $params['out_trade_no']; //支付平台交易号
        $ret['order_id'] = $params['order_id'];//支付订单id
        $ret['status'] = 'succ';

        return $ret;
    }


    /**创建请求时的mac
     * @return string
     */
    private function make_mac()
    {
        $data = array(
            $this->MachineCode,
            $this->key,
            $this->SubmitDate
        );

        return strtoupper(md5(implode('|', $data)));
    }

    /**
     * 验证返回的Mac
     * @param $return
     * @return bool
     */
    private function check_return($return)
    {
        $data = array(
            $this->MachineCode,
            $this->SubmitDate,
            $this->KJTUserName,
            $return['ReturnDateTime']
        );
        $mac = strtoupper(md5(implode('|', $data)));
        if ($mac == $return['Mac']) {
            return true;
        }

        return false;
    }

    /**解密算法
     * @param $str
     * @param $key
     * @return bool|string
     */
    private function decode($str, $key)
    {
        $strBin = base64_decode($str);
        $str = mcrypt_cbc(MCRYPT_DES, $key, $strBin, MCRYPT_DECRYPT, $key);
        $str = $this->pkcs5Unpad($str);

        return $str;
    }

    private function pkcs5Unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }

        return substr($text, 0, -1 * $pad);
    }

    /**加密算法
     * @param $str
     * @param $key
     * @return string
     */
    private function encode($str, $key)
    {
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $str = $this->pkcs5Pad($str, $size);
        $aaa = mcrypt_cbc(MCRYPT_DES, $key, $str, MCRYPT_ENCRYPT, $key);
        $ret = base64_encode($aaa);

        return $ret;
    }

    private function pkcs5Pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }


    function http_post_data($url, $data_string)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string)
        ));
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        //    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $return_content;
    }


    /**
     * 临时添加，获取跨境订单信息
     */
    public function get_order_data($params)
    {
        $mdl_orders = app::get('b2c') ->model('orders');
        $subsdf = array(
            'items' => array(
                '*',
            ) ,
            ':dlytype' => array(
                '*',
            ),
        );
        $order_id = $params['order_id'];
        $order_sdf = $mdl_orders->dump($order_id, '*', $subsdf);
        //查出跨境订单用户身份证
        $mdl_seaport = app::get('acrossborders')->model('seaport_orders');
        $identity_card = $mdl_seaport->getRow('identity_card,taxes',array('order_id'=>$order_id));
        if(empty($identity_card['identity_card']))
        {
            $msg =  '身份信息不全';
            return false;
        }

        //查询出跨境平台商品编号
        $products = $order_sdf['items'];
        $product_ids = array_keys(utils::array_change_key($products,'product_id'));
        $mdl_productsfee = app::get('acrossborders')->model('productsfee');
        $across_product_ids  = $mdl_productsfee->getList('product_id,across_product_id',array('product_id'=>$product_ids));
        $across_product_ids = utils::array_change_key($across_product_ids,'product_id');

        $items = array();
        foreach($order_sdf['items'] as $k=>$product){
            $row['Supplier'] = 'GZKJT';
            $row['ftz'] = '0';
            $row['CopGNo'] = $across_product_ids[$product['product_id']]['across_product_id'];
            $row['Number'] = $product['nums'];
            $row['WebsitePrice'] = $product['buy_price'];
            $items[] = array(
                'ProductID' => $across_product_ids[$product['product_id']]['across_product_id'],
                'Number' => $product['nums'],
                'WebsitePrice' => $product['buy_price'],
            );
        }

        $result = array(
            'MachineCode' => '123456789',
            'SubmitDate' => '20150929125645',
            'Mac' => ' 73F3486AD29DA69C2D8EEA9B3105A5BA',
            'FactName'=>$order_sdf['consignee']['name'],
            'IDCard' => $identity_card['identity_card'],
            'Custom' => "true",
            'OrderFormContent' => array(
                'ClientUrl' => $this->callback_url,
                'BackStageUrl' => $this->notify_url,
                'PayCopName' => '易票联支付有限公司',
                'OrderFormNo' => $params['bill_id'],
                "Consignee" => $order_sdf['consignee']['name'],
                "Province" => $this->region_decode($order_sdf['consignee']['area']),
                "City" => $this->region_decode($order_sdf['consignee']['area'],1),
                "LiveAddress" =>  $order_sdf['consignee']['addr'],
                "ZipCode" => $order_sdf['consignee']['zip'],
                "Phone" => $order_sdf['consignee']['mobile'],
                "Remark" => $order_sdf['memo'],
                "DeliveryType" => "1",
                'TransFee' => (string)number_format($order_sdf['cost_freight'],2),
                'Tax' => (string)number_format($identity_card['taxes'],2),
                'OrderFormDetail' => $items,
            ),
        );

        return $result;
    }

    private function region_decode($area,$index=0)
    {
        if(!$area)
        {
            return false;
        }
        $city = array(
            '北京',
            '天津',
            '上海',
            '重庆',
        );
        $arr_region = explode('/',$area);
        if($index == 0)
        {
            $str = $arr_region[0];
            $str =  substr($str,(strpos($str,':')+1));
            if(strpos($str,':'))
            {
                $str =  substr($str,0,strpos($str,':'));
            }
            if(in_array($str,$city))
            {
                $str .= '市';
            }else{
                $str .= '省';
            }
            return $str;
        }elseif($index == 1)
        {
            return $arr_region[1];
        }elseif($index == 2)
        {
            $str = $arr_region[2];
            $str =  substr($str,0,strpos($str,':'));
            return $str;
        }
        return false;
    }

}
