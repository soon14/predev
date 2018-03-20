<?php

class store_order_pay extends store_foundation
{
    private $pay_data = [];
    private $pay_method_info = [];
    private $pay_instance = null;

    /**
     * 店铺支付
     *
     * @param $pay_data
     *
     * @return bool
     */
    public function store_pay($pay_data){
        $this->pay_data = utils::_filter_input($pay_data);

        $check_result = $this->check_pay_data();
        if($check_result === false){

            return false;
        }

        //去支付
        $pay_result = $this->to_pay();
        if($pay_result === false){

            return false;
        }

        return true;
    }
    /*
     * 会员卡支付，及未来混合支付
     * */
    public function store_pay_by_membercard($pay_data){

        $this->pay_data = utils::_filter_input($pay_data);

        $check_result = $this->check_pay_data();
        if($check_result === false){

            return false;
        }
        //去支付
        $pay_result = $this->to_pay();
        if($pay_result === false){

            return false;
        }

        return true;
    }
    /**
     * 检查是否可以支付
     *
     * @return bool
     */
    public function check_pay_data(){
        if(is_array($this->pay_data) === false){
            $this->msg = '支付数据错误';

            return false;
        }

        //获取支付单信息
        $pay_bill_info = $this->get_pay_bill_info();
        if(!$pay_bill_info){
            $this->msg = '没有支付单的信息';

            return false;
        }
        if($pay_bill_info['order_id'] !== $this->pay_data['order_id']){
            $this->msg = '支付单数据和订单数据对不上';

            return false;
        }

        if($pay_bill_info['pay_app_id'] === 'wxqrcode' && empty($this->pay_data['auth_code']) === true){
            $this->msg = '请扫描用户的微信二维码';

            return false;
        }

        $this->pay_data = array_merge($pay_bill_info, $this->pay_data);

        //获取支付方式信息
        $this->get_pay_method_info();
        if (!class_exists($this->pay_method_info['app_class'])) {
            $this->msg = '支付应用程序错误';

            return false;
        }

        return true;
    }

    /**
     * 检查支付结果
     *
     * @param array $pay_data
     * @param string $msg
     *
     * @return bool
     */
    public function check_pay_result($pay_data, &$msg = ''){
        $this->pay_data = utils::_filter_input($pay_data);

        $check_result = $this->check_pay_data();
        if($check_result === false){
            $msg = $this->msg;

            return false;
        }

        //去检查是否支付成功
        $pay_result = $this->to_check_pay_result();
        if($pay_result === false){
            $msg = $this->msg;

            return false;
        }

        return true;
    }

    /**
     * 获取请求支付接口的响应数据
     *
     * @return array
     */
    public function get_pay_response(){
        $pay_response = [];

        if(is_object($this->pay_instance) && method_exists($this->pay_instance ,'get_pay_response')){

            $pay_response = $this->pay_instance->get_pay_response();
        }

        return $pay_response;
    }

    /**
     * 获取支付单数据
     *
     * @return array
     */
    public function get_pay_data(){
        $order = app::get('b2c') ->model('orders') ->getRow('order_total,payed,pay_status' ,array('order_id' =>$this->pay_data['order_id']));
        $this->pay_data = array_merge($this->pay_data,$order);
        return $this->pay_data;
    }

    /**
     * 去支付
     */
    private function to_pay(){

        //实例化支付方式类对象
        $this->pay_instance = new $this->pay_method_info['app_class']($this->getPaymethodAppObject($this->pay_method_info['app_class']));
        if (!method_exists($this->pay_instance, 'dopay')) {
            $this->msg = '支付应用方法错误';

            return false;
        }

            $pay_result = $this->pay_instance->dopay($this->pay_data, $this->msg);
        if (!$pay_result) {

            return false;
        }

        return true;
    }

    /**
     * 去检查是否支付成功
     *
     * @return bool
     */
    private function to_check_pay_result(){
        //实例化支付方式类对象
        $this->pay_instance = new $this->pay_method_info['app_class']($this->getPaymethodAppObject($this->pay_method_info['app_class']));
        if (!method_exists($this->pay_instance, 'dopay')) {
            $this->msg = '支付应用方法错误';

            return false;
        }

        $pay_result = $this->pay_instance->check_pay_result($this->pay_data, $this->msg);
        if (!$pay_result) {

            return false;
        }

        return true;
    }

    /**
     * 获取支付单信息
     *
     * @return mixed
     */
    private function get_pay_bill_info(){
        $modelBills = app::get('ectools')->model('bills');
        $pay_bill_info = $modelBills->dump($this->pay_data['bill_id']);
        return $pay_bill_info;
    }

    /**
     * 获取支付方式信息
     */
    private function get_pay_method_info(){
        //获取支付方式信息
        $model_payment_applications = app::get('ectools')->model('payment_applications');
        $this->pay_method_info = $model_payment_applications->dump($this->pay_data['pay_app_id']);
    }

    /**
     * 根据支付方式类名,获取对应的app的对象
     *
     * @param string $payClassName 支付方式类名
     *
     * @return object
     */
    private function getPaymethodAppObject($payClassName){
        list($appName) = explode('_', $payClassName);

        return app::get($appName);
    }

}