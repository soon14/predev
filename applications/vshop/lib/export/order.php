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


class vshop_export_order
{
    private $col_head = array(
        'shop_id' => '微店铺ID',
        'delivery_id' => '单号',
        'consignee_name' => '收货人',
        'consignee_area' => '收货地区',
        'consignee_addr' => '收货地址',
        'consignee_zip' => '收货地邮编',
        'consignee_tel' => '收货人电话',
        'consignee_mobile' => '收货人手机',
        'createtime' => '单据创建时间',
        'last_modify' => '单据最后更新时间',
        'status' => '状态',
        'bn' => 'sku货号',
        'name' => '货品名称',
        'spec_info' => '商品规格',
        'weight' => '重量',
        'sendnum' => '发货数量',
    );
    public function doexport($filter = array())
    {

        $mdl_reldelivery = app::get('vshop')->model('reldelivery');
        if($filter['vshop_id']){
            $vshop_id = $filter['vshop_id'];
            unset($filter['vshop_id']);
        }
        $delivery_list = $mdl_reldelivery->getDeliveryList($vshop_id, 'd.*', $filter);
        $delivery_list = utils::array_change_key($delivery_list,'delivery_id');
        $delivery_id_arr = array_keys($delivery_list);
        $delivery_items = app::get('b2c')->model('delivery_items')->getList('delivery_id,bn,name,spec_info,weight,sendnum',array('delivery_id'=>$delivery_id_arr));
        $exporter = new vshop_export_excel('browser','order-'.date('YmdHis').'.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        foreach ($delivery_items as $item) {
            $delivery = $delivery_list[$item['delivery_id']];
            $row = array_replace($this->col_head,$delivery,$item);
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
                case 'consignee_area':
                    $value = vmc::singleton('base_view_helper')->modifier_region($value);
                    break;
            }
        }
    }
}//End Class
