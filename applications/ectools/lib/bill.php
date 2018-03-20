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


class ectools_bill {
    /**
     * 共有构造方法
     * @params app object
     * @return null
     */
    public function __construct($app) {
        $this->app = $app;
        $this->mdl_bills = app::get('ectools')->model('bills');
    }
    /**
     * 创建账单
     * @params array - 账单数据
     * @params string - 支付单生成的记录
     * @return boolean - 创建成功与否
     */
    public function generate(&$sdf, &$msg = '') {
        if (!$sdf['bill_id']) {
            try {
                $sdf['bill_id'] = $this->mdl_bills->apply_id($sdf);
            }
            catch(Exception $e) {
                $msg = $e->getMessage();
                return false;
            }
        }
        if ($sdf['pay_object'] == 'order' && empty($sdf['order_id'])) {
            $msg = '未知订单号';
            return false;
        }
        if ($sdf['money'] < 0) {
            $msg = '金额错误';
            return false;
        }
        $sdf['ip'] = base_request::get_remote_addr();
        $sdf['pay_fee'] =  $sdf['pay_fee'] ? $sdf['pay_fee'] :0;
        $sdf['pay_mode'] = $sdf['pay_mode']?$sdf['pay_mode']:(in_array($sdf['pay_app_id'], array(
            '-1',
            'cod',
            'offline',
        )) ? 'offline' : 'online');
        switch ($sdf['pay_mode']) {
            case 'online':
                if ($sdf['bill_type'] == 'payment' && empty($sdf['pay_app_id'])) {
                    $msg = "未知在线付款应用程序";
                    return false;
                }
            break;
            case 'offline':
                //$sdf['status'] = 'succ';
                //case 'deposit':
            break;
            default:
                $msg = "暂不支持".$sdf['pay_mode'];
                return false;
        }
        if (!$this->mdl_bills->save($sdf)) {
            $msg = '单据保存失败';
            return false;
        } else {
            switch ($sdf['status']) {
                case 'succ':
                case 'progress':
                    $service_key = implode('.', array(
                        "ectools.bill",
                        $sdf['bill_type'],
                        $sdf['app_id'],
                        $sdf['pay_object'],
                        $sdf['status']
                    ));
                    /*
                    *订单付款成功  ectools.bill.payment.{appid}.order.succ
                    *订单付款到担保方成功  ectools.bill.payment.{appid}.order.progress
                    *订单退款成功  ectools.bill.refund.{appid}.order.succ
                    *订单退款到担保方成功  ectools.bill.refund.{appid}.order.progress
                    */

                    /*
                    *充值操作付款成功  ectools.bill.payment.{appid}.recharge.succ
                    */
                    logger::debug('支付单据保存成功,支付成功！service_key:'.$service_key);
                    foreach (vmc::servicelist($service_key) as $service) {
                        if (!$service->exec($sdf,$msg)) {
                            logger::error('支付成功回调service出错:'.$msg.'|bill_id:'.$sdf['bill_id']);
                            break;
                        }
                    }
                    break;
                case 'ready':
                    //付款单创建成功，准备中
                    $service_key = implode('.', array(
                        "ectools.bill",
                        $sdf['bill_type'],
                        $sdf['app_id'],
                        $sdf['pay_object'],
                        $sdf['status']
                    ));
                    /*
                    *订单退款准备中  ectools.bill.payment.{appid}.order.ready
                    */
                    logger::debug('准备中支付单据保存成功,service_key:'.$service_key);
                    foreach (vmc::servicelist($service_key) as $service) {
                        if (!$service->exec($sdf,$msg)) {
                            logger::error('准备中支付单保存service出错:'.$msg.'|bill_id:'.$sdf['bill_id']);
                            break;
                        }
                    }
                    break;
                default:
                    logger::debug('支付单据保存成功！'.var_export($sdf,1));
                    break;
                }
            }
            return true;
        }
    }
