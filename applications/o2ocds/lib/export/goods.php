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


class o2ocds_export_goods
{
    private $col_head = array(
        'date' => '日期',
        'store_name' => '门店',
        'sno' => '门店编号',
        'enterprise_name' => '所属经销商',
        'goods_name' => '商品名称',
        'bn' => '货号',
        'goods_count' => '销售数量',
        'goods_sum' => '销售金额',
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
        $SQL = "SELECT os.name as store_name,os.sno,oe.`name` AS enterprise_name,CONCAT(boi.name, ' ', IFNULL(boi.spec_info,'')) AS goods_name,boi.bn,COUNT(boi.nums) AS goods_count,SUM(boi.amount) AS goods_sum
                  FROM `vmc_b2c_orders` bo
                  LEFT JOIN `vmc_o2ocds_service_code` osc ON osc.order_id = bo.order_id
                  LEFT JOIN `vmc_b2c_order_items` boi ON osc.order_id = boi.`order_id`
                  LEFT JOIN `vmc_o2ocds_store` os ON os.store_id = osc.store_id
                  LEFT JOIN `vmc_o2ocds_enterprise` oe ON oe.`enterprise_id` = osc.`enterprise_id`
                  WHERE osc.store_id IS NOT NULL $where_str GROUP BY os.store_id,boi.goods_id  ";
        $data = app::get('o2ocds')->model('store')->db->select($SQL);
        $exporter = new supplier_export_excel('browser', 'order-' . date('YmdHis') . '.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        foreach ($data as $row) {
            $date['date'] = date('Y-m-d', $filter['createtime|bthan']) . ' - ' . date('Y-m-d', $filter['createtime|lthan']);
            $row = array_merge($date, $row);
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
                case 'last_modified':
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