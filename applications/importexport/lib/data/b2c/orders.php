<?php
class importexport_data_b2c_orders {

    public function get_extend_title(){
        return array(
            'name' => '商品名称(name)',
            'spec_info' => '规格(spec_info)',
            'bn' => '货号(bn)',
            'barcode' => '条码(barcode)',
            'price' => '销售价(price)',
            'buy_price' => '成交价(buy_price)',
            'nums' => '数量(nums)',
            'amount' => '小计(amount)',
            'sendnum' => '已发货数量(sendnum)'
        );
    }

    public function get_extend_rows($rows){
        $rows = utils::array_change_key($rows ,'order_id');
        $order_items = app::get('b2c')->model('order_items')->getList('*' ,array('order_id' =>array_keys($rows)));
        $res = array();
        $extend_title = $this ->get_extend_title();
        foreach($order_items as $item){
            $row = $rows[$item['order_id']];
            foreach($extend_title as $k=>$v){
                $row[$k] = $item[$k];
            }
            $res[] = $row;
        }
        return $res;
    }


    public function handle_rows(&$rows){

    }

}
