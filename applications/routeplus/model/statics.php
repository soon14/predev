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

class routeplus_mdl_statics extends dbeav_model
{

    public function save(&$data,$mustUpdate = null, $mustInsert = false){
        if(!$data['id']){
            if($this->count(array('custom_url'=>$data['custom_url']))){
                return false;
            }
            if($this->count(array('url'=>$data['url']))){
                return false;
            }
        }
        $is_save = parent::save($data,$mustUpdate,$mustInsert);
        if(!$is_save){
            return false;
        }
        return
        vmc::singleton('routeplus_rstatics')->set_dispatch($data['custom_url'], $data) &&
        vmc::singleton('routeplus_rstatics')->set_genurl($data['url'], $data);
    }

    public function delete($filter, $subSdf = 'delete')
    {
        $rows = $this->getList('*', $filter);
        $res = parent::delete($filter, $subSdf);
        if ($res) {
            foreach ($rows as $row) {
                vmc::singleton('routeplus_rstatics')->del_dispatch($row['custom_url']);
                vmc::singleton('routeplus_rstatics')->del_genurl($row['url']);
            }
        }

        return $res;
    }//End Function

    /*modifier_*/
    public function modifier_url($col){
        return vmc::base_url(true).'/'.$col;
    }
    public function modifier_custom_url($col){
        return vmc::base_url(true).'/'.$col;
    }
}//End Class
