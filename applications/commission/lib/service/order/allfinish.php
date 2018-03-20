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
class commission_service_order_allfinish
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 订单完成，增加账户实际佣金
     */
    public function exec($order, &$msg)
    {
        $order_id = $order['order_id'];
        $orderlog = $this->app->model('orderlog')->getRow("*", array('order_id' => $order_id));
        if (!$orderlog || $orderlog['settle_status'] != 0) {
            return true;
        }
        $orderlog_achieve = $this->app->model('orderlog_achieve')->getList("*", array('orderlog_id' => $orderlog['orderlog_id']));
        //上级和上上级资金变动
        $member_service = vmc::singleton('commission_service_member');
        foreach ($orderlog_achieve as $k => $v) {
            $fund_data = array(
                'member_id' => $v['member_id'],
                'change_fund' => +$v['achieve_fund'], //实际变动
                'type' => '2',
                'opt_id' => '',
                'opt_type' => 'system',
                'mark' => "佣金收入增加",
                'frozen_change' => -$v['achieve_fund'],
                'extfield' => $order_id
            );
            if (false == $member_service->fund_change($fund_data, $msg)) {
                return false;
            }
        }
        if (false == $this->app->model('orderlog')->update(array('settle_status' => '1') ,array('orderlog_id' => $orderlog['orderlog_id']))
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
     * 单品已结算佣金统计
     */
    private function  _products_count($order_items, &$msg)
    {
        $tb_prefix = vmc::database()->prefix . "commission_";
        $ids = implode(',' , array_keys(utils::array_change_key( $order_items ,"product_id")));
        $sql = "UPDATE {$tb_prefix}products_count SET  commission_settle= case product_id ";
        foreach ($order_items as $k => $v) {
            $sql .= sprintf("WHEN %d THEN commission_settle+%f ", $v['product_id'], $v['product_fund']);
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