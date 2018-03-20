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
class o2ocds_mdl_invitation extends dbeav_model {

    public $has_tag = true;
    public $defaultOrder = array('createtime','DESC');

    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                $columns[$k] = $v['label'];
            }
        }
        $columns['use_account_name'] = '使用会员';
        $columns['account_name'] = '来自账号';

        return $columns;
    }

    //重写goods filter
    public function _filter($filter, $tbase = '', $baseWhere = null)
    {
        if($filter['use_account_name']) {
            if($members = app::get('pam')->model('members')->getList('member_id',array('login_account|has'=>$filter['use_account_name']))) {
                $filter['use_member_id'] = array_keys(utils::array_change_key($members,'member_id'));
            }else{
                $filter['use_member_id'] = '-1';
            };
            unset($filter['use_account_name']);
        }
        if($filter['account_name']) {
            if($members = app::get('pam')->model('members')->getList('member_id',array('login_account|has'=>$filter['account_name']))) {
                $filter['member_id'] = array_keys(utils::array_change_key($members,'member_id'));
            }else{
                $filter['member_id'] = '-1';
            };
            unset($filter['account_name']);
        }
        return parent::_filter($filter);
    }

    /**
     *
     * @params null
     * @return string 邀请码
     */
    public function apply_code()
    {
        $tb = $this->table_name(1);
        do{
            $new_code = $this->rand_str(6);
            $row = $this->db->selectrow('SELECT invitation_code from '.$tb.' where invitation_code ='.$new_code);
        }while($row);

        return $new_code;
    }

    /*
     * 查看业务员邀请的总的店铺
     * */
    public function get_store_ids($member_id) {
        //业务员查看邀请的店铺订单
        if($store_ids = $this->getList('*',array('member_id'=>$member_id,'store_id|noequal'=>'0'))) {
            $store_ids = array_keys(utils::array_change_key($store_ids,'store_id'));
            return $store_ids;
        };
        return false;
    }

    public function modifier_use_member_id($col) {
        if($members = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$col))) {
            return $members['login_account'];
        };
        return '';
    }

    public function modifier_member_id($col) {
        if($members = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$col))) {
            return $members['login_account'];
        };
        return '';
    }

    public function rand_str($length){
        $str = '0123456789X';//10个字符
        $strlen = 10;
        while($length > $strlen){
            $str .= $str;
            $strlen += 10;
        }
        $str = str_shuffle($str);
        return substr($str,0,$length);
    }

}