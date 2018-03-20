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
class widgets_mdl_widgets_category extends dbeav_model{

    public function save(&$data, $mustUpdate = null, $mustInsert = false){
        $data['dir'] = $data['category_key'].'/';
        if($data['parent_id']){
            $parent = $this ->getRow('*' ,array('cid' => $data['parent_id']));
            $data['parent_path'] = $data['parent_id'] .($parent['parent_path'] ? ','.$parent['parent_path'] :'');
            $data['dir'] =  $parent['dir'] .$data['dir'];
        }
        if(!parent::save($data ,$mustUpdate ,$mustInsert)){
            return false;
        }
        if($parent){
            $parent['has_children'] = 'true';
            if(!$this ->save($parent)){
                return false;
            }
        }
        return true;
    }
}