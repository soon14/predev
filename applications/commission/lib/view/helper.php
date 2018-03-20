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


class commission_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    //佣金结算状态
    public function modifier_settle($v)
    {
        switch($v){
            case '0' :
                $result ='未结算';break;
            case '1' :
                $result ='已结算';break;
            case '2' :
                $result ='无效';break;
            default:$result ='无效';break;
        }
        return $result;
    }
    //帐变详细类型
    public function modifier_fundlog_type($v)
    {
        switch($v){
            case '1' :
                $result ='冻结中';break;
            case '2' :
                $result ='已到帐';break;
            case '3' :
                $result ='无效';break;
            case '4' :
                $result ='冻结中';break;
            case '5' :
                $result ='提现成功';break;
            case '6' :
                $result ='提现失败';break;
            default:$result ='无效';break;
        }
        return $result;
    }

    /*
     * 真实帐变分类
     */
    public function modifier_fund_type($v){
        switch($v){
            case '2' :
                $result ='佣金收入';break;
            case '4' :
                $result ='提现成功扣除';break;
            case '6' :
                $result ='提现失败退回';break;
            default:$result ='-';break;
        }
        return $result;
    }

    //人民币换算程闪币
    public function modifier_to_coin($v){
        $exchage = app::get('commission')->getConf('exchange');
        return $exchage*$v;
    }
}
