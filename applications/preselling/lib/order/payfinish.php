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


class preselling_order_payfinish
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->omath = vmc::singleton('ectools_math');
        $this->logger = vmc::singleton('b2c_order_log');
    }
    /**
     * 预售订单支付后的处理.
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        logger::debug('预售订单:'.$bill['order_id'] . 'payfinish exec');
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        $presell_id = $bill['order_id'];
        if (!$presell_id) {
            $msg = '未知预售订单ID';

            return false;
        }
        $mdl_orders = $this->app->model('orders');

        $order_sdf = $mdl_orders->dump($presell_id);
        if (!$order_sdf) {
            $msg = '未知预售订单';

            return false;
        }
        $money = $mdl_orders->payment_money($order_sdf,$msg);
        if($money != $bill['money']) {
            $msg = '支付金额错误'.'支付单'.var_export($bill,true).'订单：'.var_export($order_sdf,true);
            return false;
        }

        /*$payed = $omath->number_plus(array(
            $bill['money'],
            $order_sdf['payed'],
        ));*/
        switch ($bill['status']) {
            case 'succ':
                //定金支付，状态修改已付定金
                if($order_sdf['status'] == '0') {
                    $update['status'] = '1';
                    $update['deposit_pay_status'] = '1';
                    $update['deposit_bill_id'] = $bill['bill_id'];
                //尾款支付，状态修改已付定金
                }elseif($order_sdf['status'] == '1') {
                    if($order_sdf['deposit_pay_status'] != '1') {
                        $msg = '订单不是已支付状态';
                        return false;
                    }
                    $order_sdf['pay_status'] = '1';
                    $update['status'] = '2';
                    $update['balance_pay_status'] = '1';
                    $update['balance_bill_id'] = $bill['bill_id'];
                    if(!$order = $this->create_order($order_sdf,$msg)) {
                        return false;
                    }else{
                        $update['order_id'] = $order['order_id'];
                    };
                }
                break;
            case 'progress':
                //定金支付，状态修改已付定金
                if($order_sdf['status'] == '0') {
                    $update['status'] = '1';
                    $update['deposit_pay_status'] = '1';
                    $update['deposit_bill_id'] = $bill['bill_id'];
                    //尾款支付，状态修改预售成功
                }elseif($order_sdf['status'] == '1') {
                    if($order_sdf['deposit_pay_status'] != '1') {
                        $msg = '订单不是已支付状态';
                        return false;
                    }
                    $order_sdf['pay_status'] = '2';
                    $update['status'] = '2';
                    $update['balance_pay_status'] = '1';
                    $update['balance_bill_id'] = $bill['bill_id'];
                    if(!$order = $this->create_order($order_sdf,$msg)) {
                        return false;
                    }else{
                        $update['order_id'] = $order['order_id'];
                    };
                }
                break;
            default:
                return false;
        }

        if($update) {
            if (!$mdl_orders->update($update, array('presell_id' => $presell_id))) {
                $msg = '预售订单单据信息更新失败!';
                return false;
            }
        }
        return true;
    }

    private function create_order($order,&$msg) {
        $mdl_orders = app::get('b2c')->model('orders');
        $new_order_id = $mdl_orders->apply_id();
        //新订单标准数据
        $order_sdf = array(
            'member_id' => $order['member_id'],
            'memo' => $order['memo'],
            'pay_app' => $order['pay_app'],
            'dlytype_id' => $order['dlytype_id'],
            'createtime' => time() ,
            'platform' => 'mobile',
            'order_id' => $new_order_id, //订单唯一ID
            'weight' => $order['weight'], //货品总重量
            'quantity' => $order['nums'], //货品总数量
            'ip' => $order['ip'] , //下单IP地址
            'pay_status' => $order['pay_status'],
            'finally_cart_amount' => $this->omath->number_minus(array($order['order_total'],$order['cost_freight'])), //购物车优惠后总额
            'order_total' => $order['order_total'], //订单应付当前货币总额
            'cost_freight' => $order['cost_freight'], //运费
            'invoice_title' => $order['invoice_title'],
            'order_type' => 'preselling',//预售订单
            'consignee' => $order['consignee']
        );
        if($order_sdf['invoice_title']) {
            $order_sdf['need_invoice'] = true;
        }
        $order_sdf['items'][] = array(
            'order_id' => $new_order_id,
            'product_id' => $order['product_id'],
            'goods_id' => $order['goods_id'],
            'bn' => $order['bn'],
            'barcode' => $order['barcode'],
            'name' => $order['name'],
            'spec_info' => $order['spec_info'],
            'price' => $order['price'],
            'buy_price' => $order['buy_price'],
            'amount' => $order['amount'],
            'nums' => $order['nums'],
            'weight' => $order['weight'],
            'image_id' => $order['image_id'],
        );
        
        if(!$mdl_orders->save($order_sdf)) {
            $msg = '预售生产订单失败 预售订单id:'.$order['presell_id'].'订单信息'.var_export($order_sdf,true);
            vmc::singleton('b2c_order_log')->fail('create', '预售生产订单失败 预售订单id:'.$order['presell_id'] , $order_sdf);
            return false;
        }else{
            $this->logger->set_operator(array(
                'ident' => $order['member_id'],
                'name' => '系统',
                'model' => 'members',
            ));
            //订单创建时同步扩展服务
            foreach (vmc::servicelist('b2c.order.create.finish') as $service) {
                if (!$service->exec($order_sdf, $msg)) {
                    //记录日志，不中断
                    logger::error($order_sdf['order_id'].'创建出错！'.$msg);
                }
            }
            //订单相关业务异步处理队列
            system_queue::instance()->publish('b2c_tasks_order_related', 'b2c_tasks_order_aftercreate', $order_sdf);
            //记录订单日志
            $this->logger->set_order_id($order_sdf['order_id']);
            $this->logger->success('create', '订单创建成功',$order_sdf);
        };
        //预售单量计数
        $mdl_preselling =  app::get('preselling')->model('activity');
        $activity_participate_num = $mdl_preselling->getRow("order_num,activity_id",array('acitivity_id'=>$order['activity_id']));
        $activity_participate_num['order_num'] += 1;
        if(!$mdl_preselling->save($activity_participate_num)) {
            logger::error('活动统计预售单量失败!ORDER_ID:' . $order_sdf['presell_id']);
        };

        //库存冻结释放
        $stock_data = array();
        foreach ($order_sdf['items'] as $key => $value) {
            $stock_data[] = array(
                'sku'=>$value['bn'],
                'quantity'=>$value['nums']
            );
        }
        if(!vmc::singleton('b2c_goods_stock')->returned($stock_data,$msg)){
            logger::error('预售单创建成功，生成订单，库存回滚异常!ORDER_ID:'.$order_sdf['order_id'].'，'.$msg);
        }


        return $order_sdf;
    }

}
