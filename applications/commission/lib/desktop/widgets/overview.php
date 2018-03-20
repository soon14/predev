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


class commission_desktop_widgets_overview implements desktop_interface_widget
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->render = new base_render(app::get('commission'));
    }

    public function get_order()
    {
        return 1;
    }

    public function get_layout()
    {
        return 'left';
    }

    public function get_title()
    {
        return false;
    }

    public function get_html($from, $to)
    {
        //FROM_UNIXTIME
        $render = $this->render;
        $mdl_orderlog = $this->app->model('orderlog');
        $order_ft_filter = array(
            'createtime|between' => array(
                $from,
                $to
            )
        );
        $order_where_ft = $mdl_orderlog->_filter($order_ft_filter);
        $date_str_format = '%Y/%m/%d';
        if (($to - $from) / 24 / 60 / 60 > 62) {
            //按月统计
            $date_str_format = '%Y/%m';

        }
        $SQL_order_analytics = "SELECT FROM_UNIXTIME(createtime,'$date_str_format') as date_str,count(createtime) as count,sum(order_fund) as amount FROM vmc_commission_orderlog  WHERE (settle_status <>'2')  AND " . $order_where_ft . ' GROUP BY date_str ORDER BY date_str';
        //vmc::dump($SQL_order_analytics);
        $data_arr = $mdl_orderlog->db->select($SQL_order_analytics);
        foreach ($data_arr as $key => $value) {
            $count_arr[] = array($value['date_str'], intval($value['count']));
            $amount_arr[] = array($value['date_str'], $value['amount']);
        }
        if (count($count_arr) < 20) {
            $render->pagedata['show_date_str'] = true;
        } else {
            $render->pagedata['show_date_str'] = false;
        }
        $render->pagedata['count_arr'] = $count_arr;
        $render->pagedata['amount_arr'] = $amount_arr;

        return $render->fetch('desktop/widgets/overview.html');
    }
}
