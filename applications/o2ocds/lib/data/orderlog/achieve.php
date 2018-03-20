<?php

class o2ocds_data_orderlog_achieve
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function get_extend_title() {
        return array(
            'bn' => '货号',
            'name' => '名称',
            'spec_info' => '规格',
            'price' => '零售价',
            'buy_price' => '成交价',
            'o2ocds' => '分佣比例',
            'o2ocds_items' => '单件分佣',
            'nums' => '应结算数量',
            'amount' => '小计',
        );
    }

    public function get_extend_rows($rows){
        $rows = utils::array_change_key($rows ,'orderlog_id');
        $orderlog_items = app::get('o2ocds')->model('orderlog_items')->getList('*' ,array('orderlog_id' =>array_keys($rows)));
        $res = array();
        $extend_title = $this ->get_extend_title();
        foreach($orderlog_items as $item){
            $row = $rows[$item['orderlog_id']];
            foreach($extend_title as $k=>$v){
                $row[$k] = $item[$k];
            }
            $row['o2ocds'] = $row['o2ocds'][$row['type']][0];
            $row['amount'] = $row['o2ocds_items'][$row['type']];
            $row['o2ocds_items'] = $row['o2ocds_items'][$row['type']]/$row['nums'];
            $res[] = $row;
        }
        return $res;
    }



}