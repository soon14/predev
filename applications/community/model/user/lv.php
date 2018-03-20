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


class community_mdl_user_lv extends dbeav_model
{

    public function get_default_lv()
    {
        $ret = $this->getList('user_lv_id', array('default_lv' => 1));

        return $ret[0]['user_lv_id'];
    }

    public function validate(&$data, &$msg)
    {
        $extits_mlvid = $data['user_lv_id']?$data['user_lv_id']:-1;

        if ($data['name'] == '') {
            $msg = ('等级名称不能为空！');
            return false;
        }
        if($this->count(array('name'=>$data['name'],'user_lv_id|notin'=>array($extits_mlvid)))){
            $msg = '重复的等级名称';
            return false;
        }


        if (($data['default_lv'] == 1) && $this->count(array('default_lv'=>'1','user_lv_id|notin'=>array($extits_mlvid)))) {
            $msg = '已存在默认用户等级,请在添加后切换';
            return false;
        }
        return true;
    }

    public function pre_recycle($data)
    {
        $users = $this->app->model('users');
        foreach ($data as $val) {
            $aData = $users->getList('user_id', array('user_lv_id' => $val['user_lv_id']));
            if ($aData) {
                $this->recycle_msg = ('该等级下存在用户,不能删除');

                return false;
            }

            if ($val['default_lv']) {
                $this->recycle_msg = ('该等级是默认用户等级，不能删除');

                return false;
            }
        }

        if ($this->count() == count($data)) {
            $this->recycle_msg = ('至少需要有一个用户等级存在，并且需为默认');

            return false;
        }

        return true;
    }

}
