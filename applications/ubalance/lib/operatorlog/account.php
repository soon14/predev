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



#余额宝会员
class ubalance_operatorlog_account{

    function __construct(){
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
    }


    function batch_save_log($newdata){
        $modify_flag = 0;
        $data = array();
        foreach($newdata as $key=>$val){
            $data['new'][$key] = $val;
            $data['old'][$key] = $val;
            $modify_flag++;
        }
        if($modify_flag>0){
            $memo  = "serialize".$this->delimiter."充值金额:".$newdata['money'].$this->delimiter.serialize($data);
            $this->objlog->logs('member', '批量充值', $memo);
        }
    }

















}//End Class
