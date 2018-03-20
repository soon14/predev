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
class commission_service_order_finish
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 分佣记录，请在事务中调用
     */
    public function exec($order, &$msg)
    {
        $order_id = $order['order_id'];
        $orderlog = $this->app->model('orderlog')->count(array('order_id' => $order_id));
        if ($orderlog) {
            $msg = "该订单佣金已清算过";
            return false;
        }
        //查找上级、上上级，决定是否需要计算
        $parent = $this->app->model('member_relation')->getRow('*',
            array('member_id' => $order['member_id']));
        if ($parent['parent_id'] <1) {
            $msg = "该用户没有上级用户";
            return true;
        }
        //按成交价计算
        $order_items = $this->app_b2c->model('order_items')->getList("product_id ,goods_id ,buy_price ,amount ,nums",
            array('order_id' => $order_id));
        $order_items = utils::array_change_key($order_items, "product_id");

        $orderlog_datail =array();
        foreach(vmc::servicelist('commission.mode') as $service){
            $orderlog_datail = $service ->create($order_items ,$parent);
            if($orderlog_datail){
                break;
            }
        }
        if(!$orderlog_datail){
            return true;
        }

        $orderlog = array(
            'order_id' => $order_id,
            'from_id' => $order['member_id'],
            'settle' => 0, //未结算
            'order_fund' => $orderlog_datail['order_fund'],
            'items' => $orderlog_datail['items'],
            'achieve' => $orderlog_datail['achieve'],
            'createtime' => time()
        );

        if (false == $this->app->model('orderlog')->save($orderlog)) {
            $msg = "佣金写入失败";
            return false;
        }
        //上级和上上级...资金变动
        $member_service = vmc::singleton('commission_service_member');
        foreach ($orderlog_datail['achieve'] as $k => $v) {
            $fund_data = array(
                'member_id' => $v['member_id'],
                'change_fund' => 0, //实际变动
                'type' => '1',
                'opt_id' => '',
                'opt_type' => 'system',
                'mark' => "佣金收入",
                'frozen_change' => +$v['achieve_fund'],
                'extfield' => $order_id
            );
            if (false == $member_service->fund_change($fund_data, $msg)) {
                return false;
            }
        }
        unset($v);
        //单品佣金统计
        if (false == $this->_products_count($orderlog_datail['items'], $msg)) {
            return false;
        }

        return true;

    }

    /*
     * 单品佣金统计
     */
    private function  _products_count($order_items, &$msg)
    {
        $tb_prefix = vmc::database()->prefix . "commission_";
        $sql = "INSERT INTO {$tb_prefix}products_count(product_id ,commission_total) VALUES";
        $i = 0;
        foreach ($order_items as $k => $v) {
            $sql .= ($i == 0 ? '' : ',') . "({$v['product_id']} ,{$v['product_fund']} )";
            $i++;
        }
        $sql .= "ON DUPLICATE KEY UPDATE commission_total= commission_total +VALUES(commission_total)";
        $re = vmc::database()->exec($sql);
        if (!$re) {
            $msg = "佣金统计失败";

            return false;
        }

        return true;
    }
}