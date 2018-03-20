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


class sale_mdl_reserve extends dbeav_model
{
    public function save(&$data, $mustUpdate = null, $mustInsert = false,&$msg=''){
        if (!$data['tel'] || !preg_match('/^1[34578]{1}[0-9]{9}$/', $data['tel'])) {
            $msg = '请输入正确的手机号';
            return false;
        }
        $data['member_id'] = vmc::singleton('b2c_user_object')->get_member_id();
        $sale = $this->getRow('id',array('member_id'=>$data['member_id'],'tel'=>$data['tel'],'goods_id'=>$data['goods_id'],'sale_id'=>$data['sale_id']));
        if(!empty($sale)){
            $msg = '您已经预约过此活动';
            return false;
        }
        parent::save($data, $mustUpdate = null, $mustInsert = false);
        return true;
    }
    public function modifier_member_id($row)
    {
        if ($row === 0 || $row == '0'){
            return ('无效会员');
        }else{
            return vmc::singleton('b2c_user_object')->get_member_name(null,$row);
        }
    }
    public function _filter($filter, $tableAlias = null, $baseWhere = null){
        if(!empty($filter)){
            $key = '';
            $filter_ids = array();
            switch(key($filter)){
                case 'sale_id':
                    $key = 'sale_id';
                    if(is_numeric($filter[$key])){
                        $filter_ids = array($filter[$key]);
                    }else{
                        $mdl_sales = $this->app->model('sales');
                        $ids = $mdl_sales->getList('id',array('name|has'=>$filter[$key]));
                        foreach($ids as $id){
                            $filter_ids[] = $id['id'];
                        }
                    }
                break;
                case 'member_id':
                    $key = 'member_id';
                    if(is_numeric($filter[$key])){
                        $filter_ids = array($filter[$key]);
                    }else{
                        $mdl_pam = app::get('pam')->model('members');
                        $ids = $mdl_pam->getList('member_id',array('login_account|has'=>$filter[$key]));
                        foreach($ids as $id){
                            $filter_ids[] = $id['member_id'];
                        }
                    }
                break;
                case 'goods_id':
                    $key = 'goods_id';
                    if(is_numeric($filter[$key])){
                        $filter_ids = array($filter[$key]);
                    }else{
                        $mdl_goods = app::get('b2c')->model('goods');
                        $ids = $mdl_goods->getList('goods_id',array('name|has'=>$filter[$key]));
                        foreach($ids as $id){
                            $filter_ids[] = $id['goods_id'];
                        }
                    }
                break;
                default:
                    $key = key($filter);
                    $filter_ids = array($filter[$key]);
                break;
            }
            if(!empty($filter_ids)){
                $filter[$key] = $filter_ids;
            }else{
                $filter[$key] = array('0');
            }
        }
        return parent::_filter($filter, $tableAlias = null, $baseWhere = null);
    }
}
