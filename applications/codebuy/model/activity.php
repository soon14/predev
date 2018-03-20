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


class codebuy_mdl_activity extends dbeav_model
{
    public function save(&$data, $mustUpdate = null, $mustInsert = false,&$msg=''){
        if(!$data['id']){
            $data['id'] = 0;
        }
        $sql1 = "select id from vmc_codebuy_activity where batch='".$data['batch']."' and id <> ".$data['id'];
        $sql2 = "select id,name from vmc_codebuy_activity where goods_id=".$data['goods_id']." and status='0' and id <> ".$data['id'];
        $db = vmc::database();
        $check_batch = $db->select($sql1);
        if(!empty($check_batch)){
            $msg = '批次号重复!';
            return false;
        }
        $check_goods = $db->select($sql2);
        if(!empty($check_goods)){
            $msg = '此商品正在'.$check_goods[0]['name'].'的优购码活动中。';
            return false;
        }
        parent::save($data, $mustUpdate = null, $mustInsert = false);
        return true;
    }
}
