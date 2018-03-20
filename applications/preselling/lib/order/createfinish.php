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


class preselling_order_createfinish
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function exec(&$order_sdf,&$msg) {

        //购买计数
        vmc::singleton('b2c_openapi_goods', false)->counter(array(
            'goods_id' => $order_sdf['goods_id'],
            'buy_count' => $order_sdf['nums'],
            'buy_count_sign' => md5($order_sdf['goods_id'] . 'buy_count' . ($order_sdf['nums'] * 1024)),//计数签名
        ));
        //组织冻结库存数组
        $freeze_data[] = array(
            'sku' => $order_sdf['bn'],
            'quantity' => $order_sdf['nums'],
        );
        //库存冻结
        if (!vmc::singleton('b2c_goods_stock')->freeze($freeze_data, $msg)) {
            logger::error('库存冻结异常!ORDER_ID:' . $order_sdf['presell_id'] . ',' . $msg);
        }
        //记录参与用户
        $mdl_participate_member = $this->app->model('participate_member');
        $participate_data = array(
            'presell_id' => $order_sdf['presell_id'],
            'activity_id' => $order_sdf['activity_id'],
            'member_id' => $order_sdf['member_id'],
            'createtime' => $order_sdf['createtime']
        );
        if(!$mdl_participate_member->save($participate_data)) {
            logger::error('记录参加人数失败!ORDER_ID:' . $order_sdf['presell_id']);
        };
        //参与人数计数
        $mdl_preselling =  app::get('preselling')->model('activity');
        $activity_participate_num = $mdl_preselling->getRow("participate_num,activity_id",array('acitivity_id'=>$order_sdf['activity_id']));
        $activity_participate_num['participate_num'] += 1;
        if(!$mdl_preselling->save($activity_participate_num)) {
            logger::error('活动统计参与人次失败!ORDER_ID:' . $order_sdf['presell_id']);
        };

        return true;
    }
}