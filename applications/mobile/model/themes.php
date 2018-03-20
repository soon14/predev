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




class mobile_mdl_themes extends dbeav_model
{

    public $defaultOrder = array('is_used', 'asc');

    public function delete_file($filter)
    {
        $rows = $this->getList('*',$filter);
        foreach($rows AS $row){
            if($row['theme'] == vmc::singleton('mobile_theme_base')->get_default()){
                trigger_error("默认模板不能删除，请重新选择。", E_USER_ERROR);
                return false;
            }
        }
        foreach($rows AS $row){
            vmc::singleton('mobile_theme_install')->remove_theme($row['theme']);
        }
        return true;
    }//End Function

}//End Class
