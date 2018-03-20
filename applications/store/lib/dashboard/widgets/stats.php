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


class store_dashboard_widgets_stats implements desktop_interface_widget
{
    private $get_html_condition = [];
    private $model_orders = null;
    private $model_order_items = null;

    function __construct($app)
    {
        $this->app = $app;
        $this->render = new base_render(app::get('b2c'));

        $this->model_orders = app::get('store')->model('storeorder');
        $this->model_order_items = app::get('b2c')->model('order_items');
    }

    /**
     * 获取挂件在页面上的位置
     *
     * @return string
     */
    function get_layout()
    {
        return 'top';
    }

    /**
     * 获取挂件排序
     *
     * @return int
     */
    function get_order()
    {
        return 1;
    }

    /**
     * 获取挂件标题
     */
    function get_title()
    {
        return;
    }

    /**
     * 设置查询条件
     *
     * @param $get_html_condition
     */
    public function set_get_html_condition($get_html_condition){
        $this->get_html_condition = $get_html_condition;
    }

    /**
     * 获取挂件html数据
     *
     * @param $from
     * @param $to
     *
     * @return string
     */
    function get_html($from, $to)
    {
        $render = $this->render;

        //某段时间售卖商品总件数
        $render->pagedata['goods_count'] = $this->get_total_goods_num();

        //某段时间交易完成单数
        $render->pagedata['order_count'] = $this->get_total_order_num();

        //某段时间内交易完成单总金额
        $render->pagedata['total_order_money'] = $this->get_total_order_money();

        //某段时间交易单平均金额
        $render->pagedata['amount_avg'] = $this->get_order_avg_money();

        return $render->fetch('admin/dashboard/widgets/stats.html', 'store');
    }

    /**
     * 某段时间售卖商品总件数
     */
    private function get_total_goods_num(){
        $total_goods_num = 0;

        //获取满足条件的订单的id数组
        $order_ids = $this->get_meet_condition_order_ids();

        if(count($order_ids) >= 0){
            //查询商品总件数
            $total_goods_num_sql = "SELECT
                    SUM(nums) AS total_goods_num
                FROM
                    vmc_b2c_order_items
                WHERE
                    order_id IN ('" . implode("', '", $order_ids) . "')";
            $total_goods_num_result = $this->model_order_items->db->select($total_goods_num_sql);

            if(is_array($total_goods_num_result) == true && count($total_goods_num_result) > 0){
                $total_goods_num = $total_goods_num_result['0']['total_goods_num'];
            }
        }

        return $total_goods_num;
    }

    /**
     * 某段时间交易完成单数
     */
    private function get_total_order_num(){
        //获取满足条件的订单的id数组
        $order_ids = $this->get_meet_condition_order_ids();

        //某段时间所有订单条件
        $order_condition = [
            'order_id' => $order_ids,
            'pay_status'=> '1'
        ];

        $total_order_num = $this->model_orders->count($order_condition);

        return $total_order_num;
    }

    /**
     * 某段时间内交易完成单总金额
     */
    private function get_total_order_money(){
        $db = vmc::database();
        $total_order_money_sql = "SELECT SUM(order_total) AS total_order_money
                FROM vmc_b2c_orders WHERE  order_id IN ('" . implode("', '", $order_ids) . "')
                AND pay_status='1'";
        $total_order_money_result = $db->selectrow($total_order_money_sql);
        return $total_order_money_result['total_order_money'];
    }

    /**
     * 某段时间交易单平均金额
     */
    private function get_order_avg_money(){
        $order_avg_money = 0;

        //获取订单数量
        $order_count = $this->get_total_order_num();

        if($order_count > 0){
            //获取订单总金额
            $total_order_money = $this->get_total_order_money();
            $order_avg_money = $total_order_money / $order_count;
        }

        return $order_avg_money;
    }


    /**
     * 获取满足条件的订单的id数组
     *
     * @return array
     */
    private function get_meet_condition_order_ids(){
        $order_ids = [];

        $order_condition = [
            'createtime|between' => [
                $this->get_html_condition['from'],
                $this->get_html_condition['to']
            ]
        ];

        //如果当前只显示单一店铺, 只查询这个店铺的订单
        if($this->get_html_condition['show_single_store'] == true){
            $order_condition['store_id'] = $this->get_html_condition['store_id'];
        }else{
            $order_condition['store_id|than'] = '0';
        }

        $order_infos = $this->model_orders->getList('order_id', $order_condition);

        if(is_array($order_infos) == true && count($order_infos) >= 0) {
            foreach($order_infos as $order_info){
                $order_ids[] = $order_info['order_id'];
            }
        }

        return $order_ids;
    }
}
