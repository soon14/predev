<?php
/*
 * 货单导入导出数据处理类
 * */
class importexport_data_b2c_delivery {
    public function get_extend_title(){
        return array(
            'bn' => '货号(bn)',
            'item_type' => '货物类型(item_type)',
            'name' => '名称(name)',
            'spec_info' => '规格(spec_info)',
            'sendnum' => '发货量(sendnum)',
        );
    }

    public function get_extend_rows($rows){
        $rows = utils::array_change_key($rows ,'delivery_id');
        $delivery_items = app::get('b2c')->model('delivery')->getList('*' ,array('delivery_id' =>array_keys($rows)));
        $res = array();
        $extend_title = $this ->get_extend_title();
        foreach($delivery_items as $item){
            $row = $rows[$item['delivery_id']];
            foreach($extend_title as $k=>$v){
                $row[$k] = $item[$k];
            }
            $res[] = $row;
        }
        return $res;
    }



}
