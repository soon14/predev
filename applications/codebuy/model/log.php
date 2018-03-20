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


class codebuy_mdl_log extends dbeav_model
{
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
                case 'code_id':
                    $key = 'code_id';
                    if(is_numeric($filter[$key])){
                        $filter_ids = array($filter[$key]);
                    }else{
                        $mdl_code = $this->app->model('code');
                        $ids = $mdl_code->getList('id',array('code|has'=>$filter[$key]));
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
