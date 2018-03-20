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


class ectools_payment_api extends base_openapi
{
    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app)
    {
        $this->app = $app ? $app : app::get('ectools');
    }
    //&$controller = null
    public function redirect_getway(&$sdf, &$msg = '')
    {
        $pay_app_info = $this->app->model('payment_applications')->dump($sdf['pay_app_id']);
        $pay_app_class = $pay_app_info['app_class'];
        if (!class_exists($pay_app_class)) {
            $msg = '支付应用程序错误';

            return false;
        }
        $pay_app_instance = new $pay_app_class();
        if (!method_exists($pay_app_instance, 'dopay')) {
            $msg = '支付应用程序错误';

            return false;
        }
        if(!$pay_app_instance->dopay($sdf,$msg)){
            return false;
        }else{
            return true;
        }
    }

    //原来退款入口
    public function refund_redirect_getway(&$sdf, &$msg = '')
    {
        $pay_app_info = $this->app->model('payment_applications')->dump($sdf['pay_app_id']);
        $pay_app_class = $pay_app_info['app_class'];
        if (!class_exists($pay_app_class)) {
            $msg = '支付应用程序错误';

            return false;
        }
        @$pay_app_instance = new $pay_app_class();
        if (!method_exists($pay_app_instance, 'dorefund')) {
            $msg = '支付应用程序错误';

            return false;
        }
        //调用原来退还接口
        if(!$refund_result = $pay_app_instance->dorefund($sdf,$msg)){
            logger::error('退款返回失败'.$msg.var_export($sdf,true));
            return false;
        }else{
            logger::debug('退款返回成功'.var_export($refund_result, 1));
            $mdl_bills = $this->app->model('bills');
            $obj_bill = vmc::singleton('ectools_bill');
            if ($refund_result['bill_id'] && ($bill = $mdl_bills->dump($refund_result['bill_id']))) {
                $bill['status'] = 'succ';
                $refund_result['auto_refund'] = 'true';
                $refund_result = array_merge($bill, $refund_result);
                //update bill
                if (!$obj_bill->generate($refund_result, $msg)) {
                    logger::error('支付网关回调后，更新或保存支付单据失败！'.$msg.'.bill_export:'.var_export($refund_result, 1));
                }
            }else{
                $msg  .= '未知退款单';
                logger::error('退款返回成功.平台退款单丢失'.$msg.var_export($refund_result,true));
                return false;
            }
            return true;
        }
    }

    //支付网关回调入口
    public function getway_callback($pay)
    {
        $mdl_bills = $this->app->model('bills');
        $obj_bill = vmc::singleton('ectools_bill');
        $params = vmc::singleton('base_component_request')->get_params(true);
        $pay_app_class = key($pay);
        if(!stripos($pay_app_class,'_')){
            //兼容处理
            $pay_app_class = 'ectools_payment_applications_'.$pay_app_class;
        }
        $pay_app_method = current($pay);
        $pay_app_instance = new $pay_app_class();
        $pay_result = $pay_app_instance->$pay_app_method($params);
        logger::debug('支付网关回调params:'.var_export($params,
                1)."\n".$pay_app_class."\n".$pay_app_method."\n".var_export($pay_result, 1));
        if (!$pay_result || empty($pay_result['status'])) {
            $pay_result['status'] = 'error';
        }
        if ($pay_result['bill_id'] && ($bill = $mdl_bills->dump($pay_result['bill_id']))) {
            $pay_result = array_merge($bill, $pay_result);
            //update bill
            if (!$obj_bill->generate($pay_result, $msg)) {
                logger::error('支付网关回调后，更新或保存支付单据失败！'.$msg.'.bill_export:'.var_export($pay_result, 1));
            }
        }

        // Redirect page.
        if ($pay_app_method != 'notify' && $pay_result['return_url']) {
            //for ecmobilecenter
            if (preg_match('/^http([^:]*):\/\//', $pay_result['return_url'])) {
                header('Location: '.$pay_result['return_url']);
            } else {
                header('Location: '.strtolower(vmc::request()->get_schema().'://'.vmc::request()->get_host()).$pay_result['return_url']);
            }
        }
    }
    /**
     * 为了缩短openapi URL
     */
    public function gc($pay){
        $this->getway_callback($pay);
    }
}
