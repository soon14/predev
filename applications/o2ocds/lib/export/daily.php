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


class o2ocds_export_daily
{
    private $col_head = array(
        'date' => '日期',
        'name' => '门店',
        'sno' => '门店编号',
        'area' => '区域',
        'enterprise_name' => '所属经销商',
        'member_count' => '下单人数',
        'order_count' => '下单订单数',
        'order_sum' => '销售额',
        'quantity' => '下单商品数',
    );

    public function doexport($filter = array())
    {
        if ($filter) {
            $filter['createtime|bthan'] = strtotime(date('Y-m-d', $filter['createtime|bthan']) . ' 00:00:00');
            $filter['createtime|lthan'] = strtotime(date('Y-m-d', $filter['createtime|lthan']) . ' 23:59:59');
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $where_str = "AND  ";
            $where_str .= $dbeav_filter->dbeav_filter_parser($filter, 'bo', false, app::get('b2c')->model('orders'));
        }
        $SQL = "SELECT os.name,os.sno,os.`area`,oe.`name` as enterprise_name,count(bo.order_id) as order_count,count(distinct(bo.member_id)) as member_count,SUM(bo.order_total) as order_sum,SUM(bo.quantity) as quantity
          FROM `vmc_b2c_orders` bo
          LEFT JOIN `vmc_o2ocds_service_code` osc ON osc.order_id = bo.order_id
          LEFT JOIN `vmc_o2ocds_store` os ON os.store_id = osc.store_id
          LEFT JOIN `vmc_o2ocds_enterprise` oe ON  oe.`enterprise_id` = osc.`enterprise_id`
          WHERE osc.store_id iS NOT NULL $where_str GROUP BY osc.store_id ";
        $data = app::get('o2ocds')->model('store')->db->select($SQL);
        $exporter = new supplier_export_excel('browser', 'order-' . date('YmdHis') . '.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        foreach ($data as $row) {
            $date['date'] = date('Y-m-d',$filter['createtime|bthan']).' - '.date('Y-m-d',$filter['createtime|lthan']);
            $row = array_merge($date,$row);
            $this->_format($row);
            $exporter->addRow($row);
        }
        $exporter->finalize();
        exit();
    }

    private function _format(&$row)
    {
        $col_head = array_keys($this->col_head);
        foreach ($row as $key => &$value) {
            if (!in_array($key, $col_head)) {
                unset($row[$key]);
                continue;
            }
            switch ($key) {
                case 'createtime':
                case 'last_modify':
                    $value = date('Y-m-d H:i:s', $value);
                    break;
                default:
                case 'area':
                    $value = vmc::singleton('base_view_helper')->modifier_region($value);
                    break;
            }
        }
    }


}