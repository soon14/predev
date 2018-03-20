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


class supplier_export_statement
{
    private $col_head = array(
        'supplier_bn' => '供应商编号',
        'statement_id' => '结算流水号',
        'money' => '结算金额',
        'payee' => '收款人',
        'payee_account' => '收款者账户',
        'payee_bank' => '收款银行',
        'payer' => '付款人',
        'payer_account' => '付款者账户',
        'payer_bank' => '付款银行',
        'pay_fee' => '支付费率',
        'out_trade_no' => '支付平台流水号',
        'createtime' => '单据创建时间',
        'last_modify' => '最后更新时间',
        'status' => '状态',
        'voucher_id' => '结算凭证单号',
    );
    public function doexport($filter = array())
    {
        $mdl_statement = app::get('supplier')->model('statement');
        $statement_list = $mdl_statement->getList('*', $filter);
        $statement_list = utils::array_change_key($statement_list, 'statement_id');
        $statement_id_arr = array_keys($statement_list);
        $statement_index = app::get('supplier')->model('statement_index')->getList('*', array('statement_id' => $statement_id_arr));
        $exporter = new supplier_export_excel('browser', 'statement-'.date('YmdHis').'.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        foreach ($statement_index as $item) {
            $statement = $statement_list[$item['statement_id']];
            $row = array_replace($this->col_head, $statement, $item);
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
            }
        }
    }
}//End Class
