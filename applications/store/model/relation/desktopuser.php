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


class store_mdl_relation_desktopuser extends dbeav_model
{
    /**
     * 根据后台登陆的操作员id获取该操作员可操作的店铺id
     *
     * @param int $user_id 后台登陆会员(操作员)id
     * @param bool|false $is_super 是否超级管理员
     *
     * @return array
     */
    public function get_can_cashier_store_ids($user_id, $is_super = false){
        $can_cashier_store_ids = [];
        if($is_super == false){
            $condition['user_id'] = $user_id;
            $can_cashier_store_ids = $this->getList('store_id', $condition);

            if(is_array($can_cashier_store_ids) == true && count($can_cashier_store_ids) > 0){
                $temp = [];
                foreach($can_cashier_store_ids as $can_cashier_store_id){
                    $temp[] = $can_cashier_store_id['store_id'];
                }

                $can_cashier_store_ids = $temp;
            }else{
                $can_cashier_store_ids = [];
            }
        }else{
            $store_list = app::get('store')->model('store')->getList('store_id');
            $can_cashier_store_ids = array_keys(utils::array_change_key($store_list ,'store_id'));
        }

        return $can_cashier_store_ids;
    }
}//End Class
