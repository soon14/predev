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


class o2ocds_desktop_widgets_stats implements desktop_interface_widget
{
    function __construct($app)
    {
        $this->app = $app;
        $this->render = new base_render(app::get('o2ocds'));
        $this->tb_prefix = vmc::database()->prefix . $this->app->app_id . '_';
    }

    function get_layout()
    {
        return 'top';
    }

    function get_order()
    {
        return 1;
    }

    function get_title()
    {
        return;
    }

    function get_html($from, $to)
    {
        $render = $this->render;
        $mdl_order = app::get('o2ocds')->model('orderlog');

        $where_ft = $mdl_order->_filter(array(
            'createtime|between' => array($from, $to)
        ));
        //平台佣金总额
        $SQL_total_amount = "SELECT sum(`order_fund`) as total_amount_sum FROM `{$this->tb_prefix}orderlog` WHERE 1 AND " . $where_ft;
        //平台佣金单数
        $SQL_total_orders = "SELECT count(`orderlog_id`) as total_orders_sum FROM `{$this->tb_prefix}orderlog` WHERE 1 AND " . $where_ft;
        //平台可结算总额(未结算)
        $SQL_amount = "SELECT sum(`order_fund`) as amount_sum FROM `{$this->tb_prefix}orderlog` WHERE (`settle_status`='0') AND " . $where_ft;

        $render->pagedata['total_amount_sum'] = $mdl_order->db->select($SQL_total_amount);
        $render->pagedata['total_orders_sum'] = $mdl_order->db->select($SQL_total_orders);
        $render->pagedata['amount_sum'] = $mdl_order->db->select($SQL_amount);

        return $render->fetch('desktop/widgets/stats.html');
    }
}

?>
