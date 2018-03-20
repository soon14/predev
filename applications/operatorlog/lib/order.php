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



#订单管理
class operatorlog_order{

    function __construct(){
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
    }


    function editOrder_log($newdata,$olddata){
        $modify_flag = 0;
        $data = array();
        foreach($newdata as $key=>$val){
            if($newdata[$key] != $olddata[$key]){
                $data['new'][$key] = $val;
                $data['old'][$key] = $olddata[$key];
                $modify_flag++;
            }
        }
        if($modify_flag>0){
            $memo  = "serialize".$this->delimiter."编辑订单ID {$newdata['order_id']}".$this->delimiter.serialize($data);
            $this->objlog->logs('goods', '编辑订单', $memo);
        }
    }


    function printer_import_log($printer_name){
        $this->objlog->logs('order', '导入快递单模板', '导入快递单模板 '.$printer_name);
    }

}//End Class
