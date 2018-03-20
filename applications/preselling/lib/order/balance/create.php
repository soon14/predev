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


class preselling_order_balance_create
{
    /**
     * 构造方法.
     *
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->obj_math = vmc::singleton('ectools_math');
    }
    /**
     * 预售订单标准数据生成.
     */
    public function generate(&$order_sdf,&$msg = '')
    {

        $sdf = app::get('preselling')->model('orders')->dump($order_sdf['presell_id']);
        $order_sdf = array_merge($sdf,$order_sdf);
        // 预售尾款订单创建前之行的方法
        $services = vmc::servicelist('preselling.order.balance.create.before');
        if ($services) {
            foreach ($services as $service) {
                $flag = $service->exec($order_sdf,$activity , $msg);
                if (!$flag) {
                    return false;
                }
            }
        }

        return true;
    }
    /**
     * 预售订单保存.
     *
     * @param array order_sdf
     * @param string message
     *
     * @return bool
     */
    public function save(&$sdf, &$msg = '')
    {
        $mdl_order = $this->app->model('orders');
        //must Insert
        $result = $mdl_order->save($sdf);
        if (!$result) {
            $msg = ('预售尾款未能保存成功');
            return false;
        } else {
            //预售订单创建时同步扩展服务
            foreach (vmc::servicelist('preselling.order.balance.create.finish') as $service) {
                if (!$service->exec($sdf, $msg)) {
                    //记录日志，不中断
                    logger::error($sdf['presell_id'].'创建出错！'.$msg);
                }
            }
            return true;
        }
    }
}
