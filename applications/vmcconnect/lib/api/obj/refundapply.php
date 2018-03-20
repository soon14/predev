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

class vmcconnect_api_obj_refundapply extends vmcconnect_api_obj_base {

    protected $_fields = 'bill_id, money, currency, cur_rate, member_id, order_id, status, pay_mode, payee_account, payee_bank, payer_account, payer_bank, pay_app_id, pay_fee, ip, out_trade_no, memo, createtime, last_modify';

    /*
     * 退款审核单列表查询
     */
    public function read_queryPageList() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_list($fields, $params);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 根据Id查询退款审核单
     */
    public function read_queryById() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($id) && !isset($bill_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'id'
            );
            return $res;
        }
        $id = (isset($bill_id) && $bill_id) ? $bill_id : ((isset($id) && $id) ? $id : 0);
        if (!is_numeric($id) || $id < 0 || !$id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_byId($id, $fields);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 待处理退款单数查询
     */
    public function read_getWaitRefundNum() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $data = $this->_get_WaitRefundNum();

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 审核退款单
     */
    public function write_replyRefund() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($bill_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'bill_id'
            );
            return $res;
        }
        if (!isset($pay_app_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'pay_app_id'
            );
            return $res;
        }
        if (!isset($payer_account)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'payer_account'
            );
            return $res;
        }
        if (!isset($payer_bank)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'payer_bank'
            );
            return $res;
        }
        if (!isset($out_trade_no)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'out_trade_no'
            );
            return $res;
        }
        if (!is_numeric($bill_id) || $bill_id < 0 || !$bill_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'bill_id'
            );
            return $res;
        }
        if (!$pay_app_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'pay_app_id'
            );
            return $res;
        }
        if (!$payer_account) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'payer_account'
            );
            return $res;
        }
        if (!$payer_bank) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'payer_bank'
            );
            return $res;
        }
        if (!$out_trade_no) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'out_trade_no'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_reply_refund($params);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['modified'] = date('Y-m-d', ($data['modified'] ? $data['modified'] : time()));
        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    // ----------------------
    protected function _mod_bills() {
        static $mod_bills;
        if ($mod_bills) return $mod_bills;
        $mod_bills = app::get('ectools')->model('bills');
        return $mod_bills;
    }

    protected function _sort_refunds($rows) {
        if (!$rows || !is_array($rows)) return false;
        $orders = array();
        foreach ($rows as $_k => $_v) {
            $orders[$_v['bill_id']] = $_v;
        }
        return $orders;
    }

    protected function _get_refunds($filter = array(), $offset = 0, $limit = -1, $orderType = null) {

        !$filter && $filter = array();
        $filter['disabled'] = 'false';
        $filter['bill_type'] = 'refund';
        $filter['app_id'] = 'b2c';
        $filter['pay_object'] = 'order';

        $rows = $this->_mod_bills()->getList('*', $filter, $offset, $limit, $orderType);
        if (!$rows) return false;

        $get_refunds = $this->_sort_refunds($rows);
        return $get_refunds;
    }

    protected function _get_list($fields, $params) {
        $_orders = null;
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $_orders = $return;
        }
        if (!$_orders) {
            cachemgr::co_start();

            $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
            $_ids = (isset($params['ids']) && $params['ids']) ? $params['ids'] : null;
            $_status = (isset($params['status']) && $params['status']) ? $params['status'] : null;
            $_order_id = (isset($params['order_id']) && $params['order_id'] && is_numeric($params['order_id'])) ? $params['order_id'] : null;
            $_member_id = (isset($params['member_id']) && $params['member_id'] && is_numeric($params['member_id'])) ? $params['member_id'] : null;

            //
            $_create_start_date = (isset($params['create_start_date']) && $params['create_start_date']) ? $params['create_start_date'] : null;
            $_create_end_date = (isset($params['create_end_date']) && $params['create_end_date']) ? $params['create_end_date'] : null;

            $_modify_start_date = (isset($params['modify_start_date']) && $params['modify_start_date']) ? $params['modify_start_date'] : null;
            $_modify_end_date = (isset($params['modify_end_date']) && $params['modify_end_date']) ? $params['modify_end_date'] : null;


            $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
            $_page_size = (isset($params['page_size']) && $params['page_size']) ? $params['page_size'] : 20;
            (!$_page || $_page < 1) && $_page = 1;
            (!$_page_size || $_page_size < 1 || $_page_size > 100) && $_page_size = 20;

            $filter = array();
            $filter['bill_type'] = 'refund';
            $filter['pay_object'] = 'order';
            $_ids && $filter['bill_id|in'] = is_array($_ids) ? $_ids : explode(',', $_ids);
            if ($_status) {
                $_status_str = '';
                switch ($_status) {
                    case 0:
                        break;
                    default :
                    case 1:
                        $_status_str = 'ready';
                        break;
                    case 9:
                        $_status_str = 'succ';
                        break;
                    case 99:
                        $_status_str = 'dead';
                        break;
                }
                strlen($_status_str) && $filter['status'] = $_status_str;
            }

            $_create_start_date && $filter['createtime|bthan'] = is_numeric($_create_start_date) ? $_create_start_date : strtotime(date('Y-m-d 00:00:00', strtotime($_create_start_date)));
            $_create_end_date && $filter['createtime|sthan'] = is_numeric($_create_end_date) ? $_create_end_date : strtotime(date('Y-m-d 23:59:59', strtotime($_create_end_date)));
            $_modify_start_date && $filter['last_modify|bthan'] = is_numeric($_modify_start_date) ? $_modify_start_date : strtotime(date('Y-m-d 00:00:00', strtotime($_modify_start_date)));
            $_modify_end_date && $filter['last_modify|sthan'] = is_numeric($_modify_end_date) ? $_modify_end_date : strtotime(date('Y-m-d 23:59:59', strtotime($_modify_end_date)));

            $_order_id && $filter['order_id'] = $_order_id;
            $_member_id && $filter['member_id'] = $_member_id;

            $_orders = $this->_get_refunds($filter, ($_page - 1) * $_page_size, $_page_size);
            cachemgr::set($cache_key, $_orders, cachemgr::co_end());
        }

        if (!$_orders) return false;
        $res = $this->_field_rows($fields, $_orders);
        return $res;
    }

    protected function _get_refund($bill_id, $filter = array()) {
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $bill_id = is_numeric($bill_id) ? $bill_id : null;
        if (!$bill_id) return false;

        $filter = ($filter && is_array($filter)) ? $filter : array();
        $filter['bill_id'] = $bill_id;
        $filter['disabled'] = 'false';
        $filter['bill_type'] = 'refund';
        $filter['app_id'] = 'b2c';
        $filter['pay_object'] = 'order';
        $row = $this->_mod_bills()->getRow('*', $filter);
        if (!$row) return false;
        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_byId($bill_id, $fields) {
        $_row = null;
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $_row = $return;
        }
        if (!$_row) {
            cachemgr::co_start();
            $_row = $this->_get_refund($bill_id);
            cachemgr::set($cache_key, $_row, cachemgr::co_end());
        }
        if (!$_row) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $res = $this->_field_row($fields, $_row);

        return $res;
    }

    private function _get_count($filter = array()) {
        $filter = ($filter && is_array($filter)) ? $filter : array();
        $filter['disabled'] = 'false';
        $filter['bill_type'] = 'refund';
        $filter['app_id'] = 'b2c';
        $filter['pay_object'] = 'order';
        $filter['status'] = 'ready';
        return $this->_mod_bills()->count($filter);
    }

    protected function _get_WaitRefundNum() {
        $count = $this->_get_count();

        if ($count === false) return false;

        $res = array();
        $res['total_count'] = $count;
        return $res;
    }

    protected function _reply_refund($params) {
        $datas = array();
        $datas['bill_id'] = is_numeric($params['bill_id']) ? $params['bill_id'] : null;
        $datas['order_id'] = (isset($params['order_id']) && is_numeric($params['order_id'])) ? $params['order_id'] : null;
        if (!$datas['order_id']) {
            $_tmp_bill = $this->_get_refund($datas['bill_id']);
            if (!$_tmp_bill || !$_tmp_bill['order_id']) return false;
            $datas['order_id'] = $_tmp_bill['order_id'];
        }

        $datas['pay_app_id'] = trim($params['pay_app_id']);
        $datas['payer_account'] = trim($params['payer_account']);
        $datas['payer_bank'] = trim($params['payer_bank']);
        $datas['out_trade_no'] = trim($params['out_trade_no']);
        $datas['memo'] = trim($params['memo']);

        if (
                !$datas['bill_id'] ||
                !$datas['order_id'] ||
                !$datas['pay_app_id'] ||
                !$datas['payer_account'] ||
                !$datas['payer_bank'] ||
                !$datas['out_trade_no']
        ) return false;
        $datas['status'] = 'succ';

        $this->_begin();
        if (!vmc::singleton('ectools_bill')->generate($datas, $msg)) {
            $this->_end(false);
            return false;
        }
        $this->_end(true);

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['bill_id'] = $datas['bill_id'];

        return $res_data;
    }

}
