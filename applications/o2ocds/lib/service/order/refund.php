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
class o2ocds_service_order_refund
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 订单退款时
     */
    public function exec($bill, &$msg)
    {
        $order_id = $bill['order_id'];
        $orderlog = $this->app->model('orderlog')->getRow("*", array('order_id' => $order_id));
        //已结算后，就不能进行退款操作
        if (!$orderlog || $orderlog['settle_status'] != 0) {
            return true;
        }
        $orderlog_achieve = $this->app->model('orderlog_achieve')->getList("*", array('orderlog_id' => $orderlog['orderlog_id']));
        //上级和上上级资金变动 ；仅修改冻结资金
        $member_service = vmc::singleton('o2ocds_service_member');
        foreach ($orderlog_achieve as $k => $v) {
            $fund_data = array(
                'member_id' => $v['member_id'],
                'change_fund' => 0, //实际变动
                'type' => '3',
                'opt_id' => '',
                'opt_type' => 'system',
                'mark' => "订单退款，佣金失效扣除",
                'frozen_change' => -$v['achieve_fund'],
                'extfield' => $order_id
            );
            if (false == $member_service->fund_change($fund_data, $msg)) {
                return false;
            }
        }
        if (false == $this->app->model('orderlog')->update(array('settle_status' => '2' ) ,array('orderlog_id' => $orderlog['orderlog_id']))
        ) {
            $msg = "分佣记录状态修改失败";

            return false;
        };
        $order_item = $this ->app ->model('orderlog_items')->getList('*' ,array('orderlog_id' => $orderlog['orderlog_id']));
        if(false == $this ->_products_count($order_item ,$msg)){
            $msg = "分佣统计失败";

            return false;
        }
        return true;
    }
    /*
    * 单品总佣金减少
    */
    private function  _products_count($order_items, &$msg)
    {
        $tb_prefix = vmc::database()->prefix . "o2ocds_";
        $ids = implode(',' , array_keys(utils::array_change_key( $order_items ,"product_id")));
        $sql = "UPDATE {$tb_prefix}products_count SET  o2ocds_total= case product_id ";
        foreach ($order_items as $k => $v) {
            $sql .= sprintf("WHEN %d THEN o2ocds_total-%f ", $v['product_id'], $v['product_fund']);
        }
        $sql .= "END WHERE product_id IN ($ids)";
        $re = vmc::database()->exec($sql);
        if (!$re) {
            $msg = "佣金统计失败";

            return false;
        }

        return true;
    }

}