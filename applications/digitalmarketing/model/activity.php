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
class digitalmarketing_mdl_activity extends dbeav_model{
    var $has_many=array(
        'prize'=>'prize'
    );

    public function apply_bn(){
        $tb = $this->table_name(1);
        do{
            $bn= 'S'.str_pad(substr(preg_replace('/[a-z]|4/', '', uniqid()), -6), 8, rand(10, 99), STR_PAD_BOTH);
            $row = $this->db->selectrow('SELECT bn from '.$tb.' where bn ='.$bn);
        }while($row);
        return $bn;
    }

    public function modifier_opt_id($row) {
        if (is_null($row) || empty($row)) {
            return '-';
        }
        $obj_pam_account = app::get('pam')->model('account');
        $arr_pam_account = $obj_pam_account->getList('login_name', array(
            'account_id' => $row
        ));
        return $arr_pam_account[0]['login_name'] ? $arr_pam_account[0]['login_name'] : '未知操作员';
    }

    public function modifier_partin_nums($col ,$row) {
        if($col){
            $col = '<a target="_blank" href="index.php?app=digitalmarketing&ctl=admin_activity&act=partin&p[0]='.$row['activity_id'].'">'.$col.'</a>';
        }
        return $col;
    }

    public function modifier_win_nums($col,$row) {
        if($col){
            $col = '<a target="_blank" href="index.php?app=digitalmarketing&ctl=admin_activity&act=win&p[0]='.$row['activity_id'].'">'.$col.'</a>';
        }
        return $col;
    }
}