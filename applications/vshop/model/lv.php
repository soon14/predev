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


class vshop_mdl_lv extends dbeav_model
{
    /**
     * @获取店铺等级列表信息
     *
     * @param $cols 查询字段
     * @param $filter 查询过滤条件
     */
    public function getMLevel($cols = '*', $filter = array())
    {
        $rows = $this->getList($cols, $filter);

        return  $rows ? $rows : array();
    }

    public function get_default_lv()
    {
        $ret = $this->getList('shop_lv_id', array('default_lv' => 1));

        return $ret[0]['shop_lv_id'];
    }

    public function pre_recycle($data)
    {
        $shop = $this->app->model('shop');
        foreach ($data as $val) {
            $aData = $shops->getList('shop_id', array('shop_lv_id' => $val['shop_lv_id']));
            if ($aData) {
                $this->recycle_msg = ('该等级下存在店铺,不能删除');

                return false;
            }

            if ($val['default_lv']) {
                $this->recycle_msg = ('该等级是默认店铺等级，不能删除');

                return false;
            }
        }

        if ($this->count() == count($data)) {
            $this->recycle_msg = ('至少需要有一个店铺等级存在，并且需为默认');

            return false;
        }

        return true;
    }

    public function validate(&$data, &$msg)
    {
        $extits_mlvid = $data['shop_lv_id']?$data['shop_lv_id']:-1;

        if ($data['name'] == '') {
            $msg = ('等级名称不能为空！');
            return false;
        }
        if($this->count(array('name'=>$data['name'],'shop_lv_id|notin'=>array($extits_mlvid)))){
            $msg = '重复的等级名称';
            return false;
        }


        if (($data['default_lv'] == 1) && $this->count(array('default_lv'=>'1','shop_lv_id|notin'=>array($extits_mlvid)))) {
            $msg = '已存在默认店铺等级,请在添加后切换';
            return false;
        }
        if ($data['dis_count'] < 0 or $data['dis_count'] > 1) {
            $msg = ('店铺折扣率不是有效值！');
            return false;
        }
        
        if ($data['dis_count'] == 0) {
            $data['dis_count'] = '0.0';
        }

        return true;
    }

    public function is_exists($name)
    {
        $row = $this->getList('shop_lv_id', array('name' => $name));
        if (!$row) {
            return false;
        } else {
            return true;
        }
    }
}
