<?php

class o2ocds_data_statement
{
    public function __construct($app)
    {
        $this->app = $app;
    }


    public function get_extend_title() {
        return array(
            'name' => '企业/店铺名称',
        );
    }

    public function get_extend_rows($rows) {
        foreach($rows as $k=>$row) {
            if($row['relation_type'] == 'store') {
                if($store = $this->app->model('store')->getRow('name',array('store_id'=>$row['relation_id']))) {
                    $rows[$k]['name'] =  $store['name'];
                };
            }else{
                if($enterprise = $this->app->model('enterprise')->getRow('name',array('enterprise_id'=>$row['relation_id']))) {
                    $rows[$k]['name'] =  $enterprise['name'];
                };
            }
        }
        return $rows;
    }







}
