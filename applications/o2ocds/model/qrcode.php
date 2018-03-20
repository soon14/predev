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
class o2ocds_mdl_qrcode extends dbeav_model {

    public $defaultOrder = array('createtime','DESC');

    /*
     * 创建二维码
     * */
    public function create_qrcode($data,&$msg) {

        if(!$data['prefix']) {
            $msg = '缺少批次号';
            return false;
        };
        if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $data['prefix'])>0){
            echo '批次号格式不正确';
        }
        $num=$data['number'];
        if($qrcode = $this->getRow('qrcode',array('prefix'=>$data['prefix']),'qrcode DESC')) {
            $qrcode = $qrcode['qrcode'];
        }else{
            $qrcode = 10000;
        };
        if(!$data['createtime']) {
            $data['createtime'] = time();
        }
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();
        for($i=0;$i<$num;$i++) {
            $qrcode += 1;
            unset($data['qrcode_id']);
            $data['qrcode'] = $qrcode;
            if(!$this->save($data)) {
                $db->rollback();
                return false;
            };
        };
        $db->commit($this->transaction_status);
        return true;
    }

    /*
     *  获取二维码id
     *  @params 二维码
     *  @params 错误信息
     * */
    public function get_qrcode_id($qrcode,&$msg) {
        if(!$qrcode) {
            $msg = '未知二维码';
            return false;
        }
        $prefix = substr($qrcode, 0, strlen($qrcode) - 5);
        $qrcode_number = substr($qrcode, -5);
        if($qrcode_id = $this->getRow('*',array('prefix'=>$prefix,'qrcode'=>$qrcode_number))['qrcode_id']) {
            return $qrcode_id;
        };
       return false;
    }

    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                $columns[$k] = $v['label'];
            }
        }
        $columns['enterprise_name'] = '所属企业';
        $columns['store_name'] = '所属店铺';
        $columns['sales_member_name'] = '所属业务员';
        $columns['qr_code'] = '二维码';

        return $columns;
    }

    //重写goods filter
    public function _filter($filter, $tbase = '', $baseWhere = null)
    {
        if($filter['enterprise_name']) {
            if($enterprise = $this->app->model('enterprise')->getList('enterprise_id',array('name|has'=>$filter['enterprise_name']))) {
                $filter['enterprise_id'] = array_keys(utils::array_change_key($enterprise,'enterprise_id'));
                unset($filter['enterprise_name']);
            }
        }
        if($filter['store_name']) {
            if($store = $this->app->model('store')->getList('store_id',array('name|has'=>$filter['store_name']))) {
                $filter['store_id'] = array_keys(utils::array_change_key($store,'store_id'));
                unset($filter['store_name']);
            }
        }

        if($filter['qr_code']) {
            if($qrcode_id = $this->get_qrcode_id($filter['qr_code'],$msg)) {
                $filter['qrcode_id'] = $qrcode_id;
                unset($filter['qr_code']);
            }
        }
        return parent::_filter($filter);
    }

}
