<?php
/**
 *
 *  微信商户功能基本配置
 */
class wechat_ctl_admin_business_setting extends desktop_controller{



    /*
     * @param object $app
     */
    function __construct($app)
    {
        parent::__construct($app);
        $this->ui = new base_component_ui($this);
        $this->app = $app;
        header("cache-control: no-store, no-cache, must-revalidate");

    }//End Function

    public function index(){
        $this->pagedata['wxpay_classname']='wechat_payment_applications_wxpay';
        $jsapi_url = vmc::singleton('mobile_router')->gen_url(array('app'=>'b2c','ctl'=>'mobile_checkout','act'=>'dopayment','full'=>true));
        $jsapi_url = explode('/',$jsapi_url);
        array_pop($jsapi_url);
        $jsapi_dir = implode('/',$jsapi_url).'/';
        $this->pagedata['wx_business_api'] = array(
            'JSAPI网页支付'=>array(
                '支付授权目录'=>$jsapi_dir,
            ),
            '扫码支付回掉URL'=>array(
                '支付回调URL'=>vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
                    'wechat_payment_applications_wxpay' => 'notify',
                )),
            ),
            'APP客户端支付成功通知'=>array(
                '支付回调URL'=>vmc::openapi_url('openapi.ectools_payment', 'getway_callback', array(
                    'wechat_payment_applications_wxpayinapp' => 'notify',
                )),
            ),
            // '客户维权、通信告警'=>array(
            //     //'维权通知URL'=>vmc::openapi_url('openapi.weixin','safeguard'),
            //     '告警通知URL'=>vmc::openapi_url('openapi.weixin','alert')
            // )
        );
        $this->page('admin/business/setting.html');
    }

}
