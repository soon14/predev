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

class vmcconnect_logs_app extends vmcconnect_logs_base {

    function app_create($newData) {
        $modify_flag = 0;
        $data = array();
        foreach ($newData as $key => $val) {
            $data['new'][$key] = $val;
            $data['old'][$key] = null;
            $modify_flag++;
        }
        if ($modify_flag > 0) {
            $memo = "serialize" . $this->delimiter . "VMCC_创建应用-ID{$newData['app_id']}" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|apps|' . $newData['app_id'] . '|create', 'VMCC_创建应用', $memo);
        }
    }

    function app_update($newData, $oldData) {
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
            $memo = "serialize" . $this->delimiter . "VMCC_修改应用配置-ID{$newData['app_id']}" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|apps|' . $newData['app_id'] . '|update', 'VMCC_修改应用配置', $memo);
        }
    }

    function app_batch_update($setData, $filter, $newDatas, $oldDatas) {

        $data['new']['setter'] = $setData;
        $data['old']['setter'] = null;

        $data['new']['filter'] = $filter;
        $data['old']['filter'] = null;

        $_newData = array();
        $_oldData = array();

        foreach ($newDatas as $key => $val) {
            $_tmp = array();
            $app_id = $val['app_id'];
            foreach ($setData as $_k => $_v) {
                isset($val[$_k]) && $_tmp[$_k] = $val[$_k];
            }
            $_newData[$app_id] = $_tmp;
        }
        foreach ($oldDatas as $key => $val) {
            $_tmp = array();
            $app_id = $val['app_id'];
            foreach ($setData as $_k => $_v) {
                isset($val[$_k]) && $_tmp[$_k] = $val[$_k];
            }
            $_oldData[$app_id] = $_tmp;
        }


        $data['new']['datas'] = $_newData;
        $data['old']['datas'] = $_oldData;

        $memo = "serialize" . $this->delimiter . "VMCC_批量修改应用配置" . $this->delimiter . serialize($data);
        $this->objlog->logs('vmcconnect|apps|batch_update', 'VMCC_批量修改应用配置', $memo);
    }

    function app_hook_create($newData) {
        $modify_flag = 0;
        $data = array();
        foreach ($newData as $key => $val) {
            $data['new'][$key] = $val;
            $data['old'][$key] = null;
            $modify_flag++;
        }
        if ($modify_flag > 0) {
            $_app_id = isset($newData['app_id']) ? $newData['app_id'] : null;
            if (!$_app_id) {
                $hook_data = app::get('vmcconnect')->model('hooks')->dump($newData['hook_id']);
                $hook_data && $_app_id = $hook_data['app_id'];
            }
            $memo = "serialize" . $this->delimiter . "VMCC_创建应用HOOK服务-ID{$newData['hook_id']}" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|hooks|' . $_app_id . '-' . $newData['hook_id'] . '|create', 'VMCC_创建应用HOOK服务', $memo);
        }
    }

    function app_hook_update($newData, $oldData) {
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
            $_app_id = isset($newData['app_id']) ? $newData['app_id'] : null;
            if (!$_app_id) {
                $hook_data = app::get('vmcconnect')->model('hooks')->dump($newData['hook_id']);
                $hook_data && $_app_id = $hook_data['app_id'];
            }
            $memo = "serialize" . $this->delimiter . "VMCC_修改应用HOOK服务配置-ID{$newData['hook_id']}" . $this->delimiter . serialize($data);
            $this->objlog->logs('vmcconnect|hooks|' . $_app_id . '-' . $newData['hook_id'] . '|update', 'VMCC_修改应用HOOK服务配置', $memo);
        }
    }

    function app_hook_batch_update($setData, $filter, $newDatas, $oldDatas) {

        $data['new']['setter'] = $setData;
        $data['old']['setter'] = null;

        $data['new']['filter'] = $filter;
        $data['old']['filter'] = null;

        $_newData = array();
        $_oldData = array();

        foreach ($newDatas as $key => $val) {
            $_tmp = array();
            $_hook_id = $val['hook_id'];
            foreach ($setData as $_k => $_v) {
                isset($val[$_k]) && $_tmp[$_k] = $val[$_k];
            }
            $_newData[$_hook_id] = $_tmp;
        }
        foreach ($oldDatas as $key => $val) {
            $_tmp = array();
            $_hook_id = $val['hook_id'];
            foreach ($setData as $_k => $_v) {
                isset($val[$_k]) && $_tmp[$_k] = $val[$_k];
            }
            $_oldData[$_hook_id] = $_tmp;
        }


        $data['new']['datas'] = $_newData;
        $data['old']['datas'] = $_oldData;

        $memo = "serialize" . $this->delimiter . "VMCC_批量修改应用HOOK服务配置" . $this->delimiter . serialize($data);
        $this->objlog->logs('vmcconnect|hooks|batch_update', 'VMCC_批量修改应用HOOK服务配置', $memo);
    }

    function app_allow_api_items($app_id, $newItems, $oldItems) {
        $data['new']['items'] = $newItems;
        $data['old']['items'] = $oldItems;

        $memo = "serialize" . $this->delimiter . "VMCC_修改应用允许API-ID{$app_id}" . $this->delimiter . serialize($data);
        $this->objlog->logs('vmcconnect|apps|' . $app_id . '|api|items|allow', 'VMCC_修改应用允许API', $memo);
    }

    function app_allow_hook_items($app_id, $newItems, $oldItems) {
        $data['new']['items'] = $newItems;
        $data['old']['items'] = $oldItems;

        $memo = "serialize" . $this->delimiter . "VMCC_修改应用允许HOOK-ID{$app_id}" . $this->delimiter . serialize($data);
        $this->objlog->logs('vmcconnect|apps|' . $app_id . '|hooks|items|allow', 'VMCC_修改应用允许HOOK', $memo);
    }

    function app_hook_allow_items($app_id, $hook_id, $newItems, $oldItems) {
        $data['new']['items'] = $newItems;
        $data['old']['items'] = $oldItems;

        $memo = "serialize" . $this->delimiter . "VMCC_修改应用HOOK服务可用Item-ID{$hook_id}" . $this->delimiter . serialize($data);
        $this->objlog->logs('vmcconnect|hooks|' . $app_id . '-' . $hook_id . '|items|allow', 'VMCC_修改应用HOOK服务可用Item', $memo);
    }

}
