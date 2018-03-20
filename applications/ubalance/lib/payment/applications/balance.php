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


final class ubalance_payment_applications_balance extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '余额支付';
    public $version = '1.0';
    public $intro = "余额方式支付";
    public $platform_allow = array(
        'pc',
        'mobile',
        'app',
        'wxapp'
    );

    /**
     * 显示支付接口表单选项设置
     * @params null
     * @return array - 字段参数
     */
    public function setting()
    {
        return array(
            'display_name' => array(
                'title' => '余额宝方式',
                'type' => 'text',
                'default' => '余额宝方式'
            ),
            'order_num' => array(
                'title' => '排序',
                'type' => 'number',
                'default' => 0
            ),
            'status' => array(
                'title' => '是否开启此支付方式',
                'type' => 'radio',
                'options' => array(
                    'true' => '是',
                    'false' => '否',
                ),
                'default' => 'false',
            ),
        );
    }

    public function dopay($payment, &$msg)
    {

        //vmc::dump($payment);
        $bill_arr = array(
            'order_id'=>$payment['order_id'],
            'member_id'=>$payment['member_id'],
            'bill_id'=>$payment['bill_id'],
            'money'=>$payment['money']
        );
        $bill_encrypt_str = utils::encrypt($bill_arr);
        if(base_mobiledetect::is_mobile()||
        base_mobiledetect::is_wechat()||
        base_component_request::is_wxapp()
        ){
            $router_app = 'mobile';
        }else{
            $router_app = 'site';
        }
        $action_url = app::get($router_app)->router()->gen_url(array(
            'app'=>'ubalance',
            'ctl'=>$router_app.'_balancepay',
            'args'=>array($bill_encrypt_str),
            'full'=>1
        ));
        header('Location: '.$action_url);
        return true;
    }

    public function callback(&$recv)
    {


    }

    public function notify(&$recv)
    {
        $this->callback($recv);
    }
}
