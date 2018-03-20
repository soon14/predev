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


class store_mdl_store extends dbeav_model
{
    public function __construct($app){
        parent::__construct($app);
    }

    public function save(&$data, $mustUpdate = null, $mustInsert = false){
        if(!parent::save($data)){
            return false;
        }
        if(is_array($data['store_users'])){
            $mdl_relation = $this ->app ->model('relation_desktopuser');
            $data['store_users'] = array_flip(array_flip($data['store_users']));
            if(!$mdl_relation ->delete(array('store_id' => $data['store_id']))){
                return false;
            }
            foreach($data['store_users'] as $v){
                $relation_data =array(
                    'store_id' =>$data['store_id'],
                    'user_id' => $v
                );
                if(!$mdl_relation->save($relation_data)){
                    return false;
                }
            }
        }
        return true;
    }

}//End Class
