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


 

class site_mdl_modules extends dbeav_model 
{
    public function format_params($params) 
    {
        if(isset($params['path'])){
            $params['path'] = preg_replace("/[^0-9a-zA-Z]/isU", "", $params['path']);
            if(empty($params['path'])){
                return false;
            }
        }
        return $params;
    }//End Function

    public function insert(&$params) 
    {
        $params = $this->format_params($params);
        if(!$params)    return false;
        if($id = parent::insert($params)){
            vmc::singleton('site_module_base')->create_site_config();
            return $id;
        }else{
            return false;
        }
    }//End Function

    public function update($params,$filter=array(),$mustUpdate = null)
    {
        $params = $this->format_params($params);
        if(!$params)    return false;
        if(parent::update($params, $filter)){
            vmc::singleton('site_module_base')->create_site_config();
            return true;
        }else{
            return false;
        }
    }//End Function

    public function delete($filter,$subSdf = 'delete') 
    {
        if(parent::delete($filter)){
            vmc::singleton('site_module_base')->create_site_config();
            return true;
        }else{
            return false;
        }
    }//End Function

    public function enable($content_id) 
    {
        if(!is_numeric($content_id))    return false;
        if($this->update(array('enable'=>'true'), array('content_id'=>intval($content_id)))){
            return vmc::singleton('site_module_base')->create_site_config();
        }
        return false;        
    }//End Function

    public function disable($content_id) 
    {
        if(!is_numeric($content_id))    return false;
        if($this->update(array('enable'=>'false'), array('content_id'=>intval($content_id)))){
            return vmc::singleton('site_module_base')->create_site_config();
        }
        return false;        
    }//End Function

    public function pre_recycle($params) 
    {
        $dbschema = $this->get_schema();
        $pkey = $dbschema['idColumn'];
        foreach($params AS $row){
            $pkeys[] = $row[$pkey];
        }
        $rows = $this->getList('is_native',array($pkey=>$pkeys),0,-1);
        foreach($rows AS $row){
            if($row['is_native'] == 'true'){
                //trigger_error("原生模块不得人工删除。", E_USER_WARNING);
                $this->recycle_msg = '该模块是系统的基础模块，无法删除';
                return false;
            }
        }
        return true;
    }//End Function
}//End Class
