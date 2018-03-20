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


class experiencestore_mdl_activity_order extends dbeav_model
{

    public function __construct(&$app){
        $this ->app = $app;
        parent::__construct($app);
    }

    public function _filter($filter, $tableAlias = null, $baseWhere = null){
        if($filter['subject_title']){
            $subject_id= $this ->app->model('activity_subject') ->getList('id', array('title|has'=>$filter['subject_title']));
            if($subject_id){
                $filter['subject_id'] = array_keys(utils::array_change_key($subject_id ,'id'));
            }else{
                $filter['subject_id'] = 0;
            }
            unset($filter['subject_title']);
        }
        if($filter['store_name']){
            $store_id= $this ->app->model('store') ->getList('id', array('name|has'=>$filter['store_name']));
            if($store_id){
                $filter['store_id'] = array_keys(utils::array_change_key($store_id ,'id'));
            }else{
                $filter['store_id']  =0;
            }
            unset($filter['store_name']);
        }
        return parent::_filter($filter, $tableAlias , $baseWhere );
    }

    public function apply_id()
    {

        $tb = $this->table_name(1);
        do{
            $i = substr(mt_rand() , -3);
            $new_id =  '7' . date('ymdHis') . $i;
            $row = $this->db->selectrow('SELECT id from '.$tb.' where id ='.$new_id);
        }while($row);

        return $new_id;

    }
    public function modifier_member_id($row)
    {
        if ($row === 0 || $row == '0'){
            return ('非会员顾客');
        }
        else{
            return vmc::singleton('b2c_user_object')->get_member_name(null,$row);
        }
    }

    public function modifier_need_ticket($row)
    {
        if ($row =='true'){
            return '是';
        }
        return '否 ';
    }
}
