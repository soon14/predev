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


class o2ocds_export_order
{
    private $col_head = array(
        'name' => '商品名称',
        'bn' => '货号',
        'barcode' => '条码',
        'price' => '销售价',
        'buy_price' => '成交价',
        'nums' => '数量',
        'amount' => '小计',
        'sendnum' => '已发货数量',
        'sno' => '门店编号',
        'store_name' => '门店名称',
        'eno' => '经销商编号',
        'enterprise_name' => '所属经销商',
        'salesman_account' => '所属业务员',
        'integral' => '店员积分',
        'salesclerk_account' => '核销店员',
    );

    public function doexport($filter = array())
    {
        $a = '';
        $order_columns = app::get('b2c')->model('orders')->_columns();
        $need_column = array(
            'order_id', 'createtime', 'last_modified', 'status', 'confirm',
            'pay_status', 'payed', 'is_cod', 'need_shipping','ship_status',
            'pay_app','member_id','consignee_name','consignee_area','consignee_address','consignee_zip','consignee_tel',
            'consignee_email','consignee_mobile','weight','quantity','need_invoice','invoice_title','invoice_addon',
            'score_u','score_g','finally_cart_amount','cost_freight','cost_protect','cost_payment','cost_tax','currency',
            'cur_rate','memberlv_discount','pmt_goods','pmt_order','order_total','platform','memo','remarks','addon','ip','disabled'
        );

        foreach ($order_columns as $col => $val) {
            if (!$val['deny_export'] && in_array($col, $need_column)) {
                //不进行导出导入字段
                $title[$col] = $val['label'];
                $a .= ',' . 'bo.' . $col;
            }
        }
        $this->col_head = array_merge((array)$title, $this->col_head);
        if($filter) {
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $where_str = "WHERE ";
            $where_str .= $dbeav_filter->dbeav_filter_parser($filter, 'bo', false, app::get('b2c')->model('orders'));
        }
        $SQL = "SELECT
            bo.order_id,bo.createtime,bo.last_modified,bo.status,bo.confirm,bo.pay_status,bo.payed,bo.is_cod,bo.need_shipping,bo.ship_status,bo.pay_app,bd.dt_name as dlyeypte_id,bo.member_id,bo.consignee_name,bo.consignee_area,bo.consignee_address,bo.consignee_zip,bo.consignee_tel,bo.consignee_email,bo.consignee_mobile,bo.weight,bo.quantity,bo.need_invoice,bo.invoice_title,bo.invoice_addon,bo.score_u,bo.score_g,bo.finally_cart_amount,bo.cost_freight,bo.cost_protect,bo.cost_payment,bo.cost_tax,bo.currency,bo.cur_rate,bo.memberlv_discount,bo.pmt_goods,bo.pmt_order,bo.order_total,bo.platform,bo.memo,bo.remarks,bo.addon,bo.ip,bo.disabled
            ,boi.name,boi.bn,boi.barcode,boi.price,boi.buy_price,boi.nums,boi.amount,boi.sendnum,os.sno,os.name AS store_name,oe.eno,oe.name AS enterprise_name,pm.`login_account` AS salesman_account,osc.`integral`,opm.`login_account` AS salesclerk_account
            FROM `vmc_b2c_orders` bo
            LEFT JOIN `vmc_b2c_dlytype` bd ON bd.dt_id = bo.dlytype_id
            LEFT JOIN `vmc_b2c_order_items` boi ON bo.order_id = boi.order_id
            LEFT JOIN `vmc_o2ocds_service_code` osc ON osc.order_id = boi.order_id
            LEFT JOIN `vmc_pam_members` opm ON opm.member_id = osc.member_id
            LEFT JOIN `vmc_o2ocds_store` os ON os.store_id = osc.`store_id`
            LEFT JOIN `vmc_o2ocds_enterprise` oe ON oe.enterprise_id = osc.`enterprise_id`
            LEFT JOIN `vmc_o2ocds_invitation` oi ON oi.store_id = os.`store_id`
            LEFT JOIN `vmc_pam_members` pm ON pm.`member_id` = oi.`use_member_id`
            $where_str ";
        $orders =   app::get('b2c')->model('orders')->db->select($SQL);
        $exporter = new supplier_export_excel('browser', 'order-' . date('YmdHis') . '.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        $member_ids = array_keys(utils::array_change_key($orders,'member_id'));
        $member_list =  app::get('pam')->model('members')->getList('member_id,login_account',array('member_id'=>$member_ids));
        $member_list = utils::array_change_key($member_list,'member_id');
        $pay_list = app::get('ectools')->model('payment_applications')->getList('*');
        $pay_list = utils::array_change_key($pay_list,'app_id');
        foreach ($orders as $row) {
            $row['member_id'] = $member_list[$row['member_id']]['login_account'];
            $row['pay_app'] = $pay_list[$row['pay_app']]['name'];
            $this->_format($row,$order_columns);
            $exporter->addRow($row);
        }
        $exporter->finalize();
        exit();
    }

    private function _format(&$row,$order_columns)
    {
        $col_head = array_keys($this->col_head);
        foreach ($row as $key => &$value) {
            if (!in_array($key, $col_head)) {
                unset($row[$key]);
                continue;
            }
            if(is_array($order_columns[$key]['type'])) {
                $value = $order_columns[$key]['type'][$value];
            }
            switch ($key) {
                case 'createtime':
                case 'last_modified':
                    $value = date('Y-m-d H:i:s', $value);
                    break;
                case 'consignee_area':
                    $value = vmc::singleton('base_view_helper')->modifier_region($value);
                    break;
                default:
            }
        }
    }
}//End Class
