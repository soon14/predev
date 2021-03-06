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


class groupbooking_order_createorder
{
    public function exec($params = null)
    {
        $this->logger = vmc::singleton('b2c_order_log');

        $filter = array(
            'filter_sql'=>"gb_id = {$params['main_id']} OR main_id = {$params['main_id']}",
            'pay_status|in' => array('1','2')
        );
        $mdl_groupbooking_orders = app::get('groupbooking')->model('orders');
        if($orders_list = $mdl_groupbooking_orders->getList('*',$filter)) {
            $new_order_ids = array();
            $gb_ids = array();
            $mdl_orders = app::get('b2c')->model('orders');
            $mdl_participate_member = app::get('groupbooking')->model('participate_member');
            foreach($orders_list as $order) {
                $this->logger->set_operator(array(
                    'ident' => $order['member_id'],
                    'name' => '系统',
                    'model' => 'members',
                ));
                $order_sdf = $this->generate($order,$new_order_ids);
                if(!$mdl_orders->save($order_sdf)) {
                    logger::error('拼团生产订单失败 拼团订单id:'.$order['gb_id'].'订单信息'.var_export($order_sdf,true));
                    $this->logger->fail('create', '拼团生产订单失败 拼团订单id:'.$order['gb_id'] , $order_sdf);
                    return false;
                }else{
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
                //库存冻结释放
                $stock_data = array();
                foreach ($order_sdf['items'] as $key => $value) {
                    $stock_data[] = array(
                        'sku'=>$value['bn'],
                        'quantity'=>$value['nums']
                    );
                }
                if(!vmc::singleton('b2c_goods_stock')->returned($stock_data,$msg)){
                    logger::error('团购单创建成功，生成订单，库存回滚异常!ORDER_ID:'.$order_sdf['order_id'].'，'.$msg);
                }
                if(!$mdl_groupbooking_orders->update(array('status'=>'1','order_id'=>$order_sdf['order_id']),array('gb_id'=>$order['gb_id']))) {
                    logger::error('拼团订单状态修改失败 拼团订单id'.var_export($gb_ids,true));
                    return false;
                };
                if($mdl_participate_member->update(array('status'=>'1'),array('gb_id'=>$order['gb_id']))) {
                    logger::error('修改参与用户状态修改， 拼团订单id'.$order['gb_id']);
                };
            }
        };
        return true;
    }

    private function get_order_id($in_order_ids) {
        $new_order_id = app::get('b2c')->model('orders')->apply_id();
        if(in_array($new_order_id,$in_order_ids)) {
            $this->get_order_id($in_order_ids);
        }
        return $new_order_id;
    }

    private function generate($order,&$new_order_ids) {
        $new_order_id = $this->get_order_id($new_order_ids);
        $new_order_ids[] = $new_order_id;
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
            'finally_cart_amount' => $order['order_total'], //购物车优惠后总额
            'order_total' => $order['order_total'], //订单应付当前货币总额
            'cost_freight' => $order['cost_freight'], //运费
            'invoice_title' => $order['invoice_title'],
            'order_type' => 'groupbooking',//拼团订单
            'consignee' => array(
                'name' => $order['consignee_name'],
                'area' => $order['consignee_area'],
                'address' => $order['consignee_address'],
                'zip' => $order['consignee_zip'],
                'tel' => $order['consignee_tel'],
                'email' => $order['consignee_email'],
                'mobile' => $order['consignee_mobile'],
            ),
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
        return $order_sdf;
    }


}