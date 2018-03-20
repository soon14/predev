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

class vmcconnect_logs_setting extends vmcconnect_logs_base {

    function sys_api_conf($newData, $oldData) {
        $modify_flag = 0;
        $data = array();
        foreach ($newData as $key => $val) {
            if (!$oldData || !isset($oldData[$key]) || $newData[$key] != $oldData[$key]) {
                $data['new'][$key] = $val;
                $data['old'][$key] = $oldData[$key];
                $modify_flag++;
            }
        }
        if ($modify_flag > 0) {
            $memo = "serialize" . $this->delimiter . "VMCC_修改系统配置 - API" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|setting|apis', 'VMCC_修改系统配置 - API', $memo);
        }
    }

    function sys_hook_conf($newData, $oldData) {
        $modify_flag = 0;
        $data = array();
        foreach ($newData as $key => $val) {
            if (!$oldData || !isset($oldData[$key]) || $newData[$key] != $oldData[$key]) {
                $data['new'][$key] = $val;
                $data['old'][$key] = $oldData[$key];
                $modify_flag++;
            }
        }
        if ($modify_flag > 0) {
            $memo = "serialize" . $this->delimiter . "VMCC_修改系统配置 - HOOK" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|setting|hooks', 'VMCC_修改系统配置 - HOOK', $memo);
        }
    }

    function sys_warning_conf($newData, $oldData) {
        $modify_flag = 0;
        $data = array();
        foreach ($newData as $key => $val) {
            if (!$oldData || !isset($oldData[$key]) || $newData[$key] != $oldData[$key]) {
                $data['new'][$key] = $val;
                $data['old'][$key] = $oldData[$key];
                $modify_flag++;
            }
        }


        if ($modify_flag > 0) {
            $memo = "serialize" . $this->delimiter . "VMCC_修改系统配置 - 警报设置" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|setting|warnings', 'VMCC_修改系统配置 - 警报设置', $memo);
        }
    }

}
