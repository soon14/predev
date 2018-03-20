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
class widgets_mdl_widgets extends dbeav_model{

    public function save(&$data, $mustUpdate = null, $mustInsert = false){
        $data['file_path'] = vmc::singleton('widgets_widgets')->get_widgets_file($data ,false);
        if(!parent::save($data, $mustUpdate, $mustInsert )){
            return false;
        }
        return true;
    }

}
