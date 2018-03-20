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

class vmcconnect_api_obj_base {

    protected $base_res = array(
        'code' => 0,
        'result' => array(
        ),
    );

    public function setApp(&$app) {
        $this->app = $app;
    }

    public function _before() {
        
    }

    public function _after() {
        
    }

    protected function _field_arr($field) {
        $field = is_string($field) ? trim($field) : $field;
        $field_arr = array_map('trim', (is_array($field) ? $field : explode(',', $field)));
        return $field_arr;
    }

    protected function _field_row($field, $row) {
        if (!$row || !is_array($row)) return false;
        if (trim($field) == '*') return $row;
        $fields_arr = $this->_field_arr($field);
        $res = array();
        foreach ($fields_arr as $_f) {
            $res[$_f] = (isset($row[$_f])) ? $row[$_f] : null;
        }
        return $res;
    }

    protected function _field_rows($field, $rows) {
        if (!$rows || !is_array($rows)) return false;
        if (trim($field) == '*') return $rows;
        $fields_arr = $this->_field_arr($field);
        $res = array();
        foreach ($rows as $_k => $_v) {
            $_tmp = array();
            foreach ($fields_arr as $_f) {
                $_tmp[$_f] = (isset($_v[$_f])) ? $_v[$_f] : null;
            }
            $res[$_k] = $_tmp;
        }
        return $res;
    }

    protected function vard() {
        return call_user_func_array(array('myfun', 'vard'), func_get_args());
    }

    protected function _begin() {
        set_error_handler(array(&$this, '_errorHandler'), E_USER_ERROR | E_ERROR);
        if ($this->transaction_start) {
            trigger_error('The transaction has been started', E_USER_ERROR);
        }
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();
        $this->transaction_start = true;
    }

    protected function _end($result = true) {
        if (!$this->transaction_start) {
            trigger_error('The transaction has not started yet', E_USER_ERROR);
        }
        $this->transaction_start = false;
        $db = vmc::database();
        restore_error_handler();
        if ($result) {
            $db->commit($this->transaction_status);
        } else {
            $db->rollback();
        }
    }

}
