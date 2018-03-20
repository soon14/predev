<?php
class preselling_ctl_mobile_member extends b2c_ctl_mobile_member
{
    public $title = '会员中心';

    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->member = $this->get_current_member();
        $this->set_tmpl('member');
    }

    /**
     * 我的预售订单.
     */
    public function orders_list($status = 'all', $page = 1)
    {
        $limit = 10;
        $time = time();
        $status_filter = array(
            'all' => array(
                'member_id' => $this->member['member_id'],
            ) ,
            's1' => array(
                'member_id' => $this->member['member_id'],
                'status' => '0',
            ) ,
            's2' => array(
                'member_id' => $this->member['member_id'],
                'status' => '1',
                'balance_starttime|bthan' => $time,
            ) ,
            's3' => array(
                'member_id' => $this->member['member_id'],
                'status' => '1',
                'balance_starttime|lthan' => $time,
                'balance_endtime|bthan' => $time,
            ) ,
            's4' => array(
                'member_id' => $this->member['member_id'],
                'status' => '2',
            ) ,
            's5' => array(
                'member_id' => $this->member['member_id'],
                'status' => '3',
            ) ,
        );

        if ($filter = $status_filter[$status]) {
        } else {
            $filter = array(
                'member_id' => $this->member['member_id'],
            );
        }
        $mdl_order = $this->app->model('orders');
        $order_list = $mdl_order->getList('*', $filter, ($page - 1) * $limit, $limit);
        //查询活动
        $activity_ids = array_keys(utils::array_change_key($order_list,'activity_id'));
        $activity_list = $this->app->model('activity')->getList('*',array('activity_id'=>$activity_ids));
        $activity_list = utils::array_change_key($activity_list,'activity_id');
        foreach($order_list as &$order) {
            $order['surplus_deposit_endtime'] = $activity_list[$order['activity_id']]['deposit_endtime']-$time;
            $order['surplus_balance_starttime'] = $time-$order['balance_starttime'];
            $order['surplus_balance_endtime'] = $order['balance_endtime']-$time;
            $order['is_refund'] = $activity_list[$order['activity_id']]['is_refund'];
        }
        $order_count = $mdl_order->count($filter);
        $this->pagedata['current_status'] = $status;
        $this->pagedata['order_list'] = $order_list;
        $this->pagedata['order_count'] = $order_count;
        $this->pagedata['pager'] = array(
            'total' => ceil($order_count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'preselling',
                'ctl' => 'mobile_member',
                'act' => 'orders_list',
                'args' => array(
                    $status,
                    ($token = time()),
                ) ,
            ) ,
            'token' => $token,
        );
        $this->page('mobile/default.html');
    }

    /*
     * 订单详情页
     * */
    public function detail($presell_id) {

        $mdl_order = $this->app->model('orders');
        $order = $mdl_order->dump($presell_id);
        if ($order['member_id'] != $this->app->member_id) {
            $this->splash('error', '非法操作!');
        }
        if($order['pay_status'] == '4' && $order['pay_status'] == '5') {
            $this->pagedata['refund_money'] = $mdl_order->refund_money($order['presell_id']);
        }
        $this->pagedata['dlytype'] = app::get('b2c')->model('dlytype')->getRow('*',array('dt_id'=>$order['dlytype_id']));
        $this->pagedata['payapp'] = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);
        $this->pagedata['order_pay_status'] = array(
            0 => ('未支付') ,
            1 => ('已支付') ,
            2 => ('已付款至到担保方') ,
            3 => ('部分付款') ,
            4 => ('部分退款') ,
            5 => ('全额退款') ,
        ) ;
        $this->pagedata['order'] = $order;
        $this->page('mobile/default.html');
    }

}


