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


class package_mdl_package extends dbeav_model
{
    public $has_many = array(
        'rules' => 'rules:delete',
    );
    public function save(&$package_data, $mustUpdate = null, $mustInsert = false){
        $flag = parent::save($package_data, $mustUpdate = null, $mustInsert = false);
        if($flag){
            $mdl_rules = $this->app->model('rules');
            $mdl_rules->delete(array('package_id'=>$package_data['id']));
            foreach($package_data['goods_id'] as $key=>$item){
                $rules_data = array(
                    'package_id'=>$package_data['id'],
                    'name'=>$package_data['name'],
                    'intro'=>$package_data['intro'],
                    'status'=>$package_data['status'],
                    'goods_id'=>$item,
                    'package_goods'=>$package_data['package_goods'],
                    'start'=>$package_data['start'],
                    'end'=>$package_data['end'],
                );
                if(!$mdl_rules->save($rules_data)){
                    return false;
                };
            }
        }
        return true;
    }
}
