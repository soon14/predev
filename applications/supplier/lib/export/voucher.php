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


class supplier_export_voucher
{
    private $col_head = array(
        'supplier_bn' => '供应商编号',
        'voucher_id' => '结算凭证单号',
        'delivery_id' => '订单号',
        'status' => '状态',
        'createtime' => '单据创建时间',
        'last_modify' => '最后更新时间',
        'bn' => 'sku货号',
        'name' => '货品名称',
        'spec_info' => '货品规格',
        's_num' => '应结算数量',
        's_price' => '结算单价',
        's_subprice' => '应结算小记',
    );
    public function doexport($filter = array())
    {

        $mdl_voucher = app::get('supplier')->model('voucher');
        $voucher_list = $mdl_voucher->getList('*', $filter);
        $voucher_list = utils::array_change_key($voucher_list,'voucher_id');
        $voucher_id_arr = array_keys($voucher_list);
        $voucher_items = app::get('supplier')->model('voucher_items')->getList('*',array('voucher_id'=>$voucher_id_arr));
        $exporter = new supplier_export_excel('browser','voucher-'.date('YmdHis').'.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        foreach ($voucher_items as $item) {
            $voucher = $voucher_list[$item['voucher_id']];
            $row = array_replace($this->col_head,$voucher,$item);
            $this->_format($row);
            $exporter->addRow($row);
        }
        $exporter->finalize();
        exit();
    }

    private function _format(&$row){
        $col_head = array_keys($this->col_head);
        foreach ($row as $key => &$value) {
            if(!in_array($key,$col_head)){
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
