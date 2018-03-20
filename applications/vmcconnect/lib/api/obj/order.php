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

class vmcconnect_api_obj_order extends vmcconnect_api_obj_base {

    protected $_fields = 'order_id, createtime, last_modified, status, confirm, pay_status, payed, is_cod, need_shipping, ship_status, pay_app, dlytype_id, member_id, consignee_name, consignee_area, consignee_address, consignee_zip, consignee_tel, consignee_email, consignee_mobile, weight, quantity, need_invoice, invoice_title, finally_cart_amount, cost_freight, cost_protect, cost_payment, cost_tax, currency, cur_rate, memberlv_discount, pmt_goods, pmt_order, order_total, platform, memo, remarks, addon, ip';
    protected $_ord_fields = 'order_id, createtime, last_modified, status, confirm, pay_status, payed, is_cod, need_shipping, ship_status, pay_app, dlytype_id, member_id,  weight, quantity, need_invoice, invoice_title, finally_cart_amount, cost_freight, cost_protect, cost_payment, cost_tax, currency, cur_rate, memberlv_discount, pmt_goods, pmt_order, order_total, platform, memo, remarks, addon, ip, items, consignee';

    /*
     * 获取单个订单
     */
    public function read_getbyId() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }
        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_byId($order_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;


        // 返回数据
        return $res;
    }

    /*
     * 订单检索
     */
    public function read_search() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_search_orders($fields, $params);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 批量查询未付款订单
     */
    public function read_notPayOrderInfo() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_notPayOrderInfo($fields, $params);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 未付款订单单条记录查询
     */
    public function read_notPayOrderById() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }
        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_notPayById($order_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 查询商家备注
     */
    public function read_remarkByOrderId() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }
        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_get_remarkByOrderId($order_id);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 商家订单备注修改
     */
    public function write_remarkUpdate() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }
        if (!isset($remark)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'remark'
            );
            return $res;
        }
        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

//        if (!strlen($remark)) {
//            $res['code'] = 61;
//            $res['msg_strs'] = array(
//                'remark'
//            );
//            return $res;
//        }
        // 返回数据
        $data = $this->_set_remarkUpdate($order_id, $remark);
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

    /*
     * 订单作废
     */
    public function write_cancel() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_set_cancel($order_id);
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

    /*
     * 订单付款
     */
    public function bill_write_pay() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!isset($money)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'money'
            );
            return $res;
        }

        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!is_numeric($money) || $money < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'money'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_write_pay($params);
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

    /*
     * 订单退款
     */
    public function bill_write_refund() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!isset($money)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'money'
            );
            return $res;
        }

        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!is_numeric($money) || $money < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'money'
            );
            return $res;
        }

        if (!$payee_bank) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'payee_bank'
            );
            return $res;
        }

        if (!$payee_account) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'payee_bank'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_write_refund($params);
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

    /*
     * 订单发货
     */
    public function delivery_write_send() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!isset($dlycorp_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'dlycorp_id'
            );
            return $res;
        }

        if (!isset($dlyplace_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'dlyplace_id'
            );
            return $res;
        }

        if (!isset($send) || !is_array($send)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'send'
            );
            return $res;
        }

        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!is_numeric($dlycorp_id) || $dlycorp_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'dlycorp_id'
            );
            return $res;
        }

        if (!is_numeric($dlyplace_id) || $dlyplace_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'dlyplace_id'
            );
            return $res;
        }

        if (!$logistics_no) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'logistics_no'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_delivery_send($params);
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

    /*
     * 订单退货
     */
    public function delivery_write_reship() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!isset($dlycorp_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'dlycorp_id'
            );
            return $res;
        }

        if (!isset($dlyplace_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'dlyplace_id'
            );
            return $res;
        }

        if (!isset($send) || !is_array($send)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'send'
            );
            return $res;
        }

        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!is_numeric($dlycorp_id) || $dlycorp_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'dlycorp_id'
            );
            return $res;
        }

        if (!is_numeric($dlyplace_id) || $dlyplace_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'dlyplace_id'
            );
            return $res;
        }

        if (!$logistics_no) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'logistics_no'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_delivery_reship($params);
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

    /*
     * 订单归档完成
     */
    public function write_end() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($order_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        if (!is_numeric($order_id) || $order_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'order_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_set_end($order_id);
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

    /*
     * 修改订单收货地址
     */
    public function write_modifyOrderAddr() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        // 返回数据
        $data = array();
        $data['msg'] = 'ping';
        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    // ----------------------------
    protected function _mod_orders() {
        static $mod_orders;
        if ($mod_orders) return $mod_orders;
        $mod_orders = app::get('b2c')->model('orders');
        return $mod_orders;
    }

    protected function _sort_orders($rows) {
        if (!$rows || !is_array($rows)) return false;
        $orders = array();
        foreach ($rows as $_k => $_v) {
            $orders[$_v['order_id']] = $_v;
        }
        return $orders;
    }

    protected function _get_orders($filter = array(), $offset = 0, $limit = -1, $orderType = null) {

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_types = $return;
            return $return;
        }
        cachemgr::co_start();

        !$filter && $filter = array();
        $filter['disabled'] = 'false';
        $rows = $this->_mod_orders()->getList('*', $filter, $offset, $limit, $orderType);
        if (!$rows) return false;

        $get_orders = $this->_sort_orders($rows);
        cachemgr::set($cache_key, $get_orders, cachemgr::co_end());

        return $get_orders;
    }

    protected function _get_notPayOrderInfo($fields, $params) {

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
            $_startDate = (isset($params['startDate']) && $params['startDate']) ? $params['startDate'] : null;
            $_startDate && is_numeric($_startDate) && $_startDate = date('Y-m-d', $_startDate);

            $_endDate = (isset($params['endDate']) && $params['endDate']) ? $params['endDate'] : null;
            $_endDate && is_numeric($_endDate) && $_endDate = date('Y-m-d', $_endDate);

            $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
            $_pageSize = (isset($params['pageSize']) && $params['pageSize']) ? $params['pageSize'] : 20;
            (!$_page || $_page < 1) && $_page = 1;
            (!$_pageSize || $_pageSize < 1 || $_pageSize > 100) && $_pageSize = 20;

            if (!$_startDate && !$_endDate) {
                $_startDate = date('Y-m-d', strtotime('-7 day'));
                $_endDate = date('Y-m-d');
            } elseif (!$_startDate || !$_endDate) {
                !$_startDate && $_endDate && $_startDate = date('Y-m-d', strtotime($_endDate . ' -7 day'));
                !$_endDate && $_startDate && $_endDate = date('Y-m-d', strtotime($_startDate . ' +7 day'));
            }

            if ((strtotime($_endDate) - strtotime($_startDate)) > (3600 * 24 * 31)) {
                $_endDate = date('Y-m-d', strtotime($_startDate . ' +31 day'));
            }

            $filter = array(
                'status' => 'active',
                'pay_status' => '0',
                'createtime|between' => array(
                    strtotime(date('Y-m-d 00:00:00', strtotime($_startDate))),
                    strtotime(date('Y-m-d 23:59:59', strtotime($_endDate))),
                ),
            );

            $_orders = $this->_get_orders($filter, ($_page - 1) * $_pageSize, $_pageSize);
            cachemgr::set($cache_key, $_orders, cachemgr::co_end());
        }

        if (!$_orders) return false;
        $res = $this->_field_rows($fields, $_orders);

        return $res;
    }

    protected function _get_order($order_id, $filter = array()) {
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_types = $return;
            return $return;
        }
        cachemgr::co_start();

        $order_id = is_numeric($order_id) ? $order_id : null;
        if (!$order_id) return false;

        $filter = ($filter && is_array($filter)) ? $filter : array();
        $filter['order_id'] = $order_id;
        $filter['disabled'] = 'false';

        $row = $this->_mod_orders()->dump($filter, '*', array(
            'items' => array(
                '*',
            ),
        ));
        if (!$row) return false;
        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_byId($order_id, $fields) {
        $_order = $this->_get_order($order_id);
        if (!$_order) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_ord_fields;
        $res = $this->_field_row($fields, $_order);
        return $res;
    }

    protected function _get_notPayById($order_id, $fields) {
        $_order = null;
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $_order = $return;
        }
        if (!$_order) {
            cachemgr::co_start();

            $filter = array(
                'status' => 'active',
                'pay_status' => '0',
            );
            $_order = $this->_get_order($order_id, $filter);
            cachemgr::set($cache_key, $_order, cachemgr::co_end());
        }

        if (!$_order) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_ord_fields;
        $res = $this->_field_row($fields, $_order);
        return $res;
    }

    protected function _get_remarkByOrderId($order_id) {
        $_order = $this->_get_order($order_id);
        if (!$_order) return false;

        $res = array();
        $res['remarks'] = trim($_order['remarks']);
        return $res;
    }

    protected function _set_remarkUpdate($order_id, $remark) {
        $order_id = is_numeric($order_id) && $order_id ? $order_id : null;
        if (!$order_id) return false;
        !strlen($remark) && $remark = '';
        $data = array();
        $data['order_id'] = $order_id;
        $data['remarks'] = $remark;

        $this->_begin();
        if (!$this->_mod_orders()->save($data)) {
            $this->_end(false);
            return false;
        }
        $this->_end(true);

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['order_id'] = $order_id;

        return $res_data;
    }

    protected function _get_search_orders($fields, $params) {
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
            $_startDate = (isset($params['startDate']) && $params['startDate']) ? $params['startDate'] : null;
            $_startDate && is_numeric($_startDate) && $_startDate = date('Y-m-d', $_startDate);

            $_endDate = (isset($params['endDate']) && $params['endDate']) ? $params['endDate'] : null;
            $_endDate && is_numeric($_endDate) && $_endDate = date('Y-m-d', $_endDate);

            $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
            $_pageSize = (isset($params['pageSize']) && $params['pageSize']) ? $params['pageSize'] : 20;


            /*
             * 1 待付款 2 待发货 3 已发货 9 已完成 99 已作废
             */
            $_order_state = (isset($params['order_state']) && $params['order_state']) ? $params['order_state'] : null;

            //  0 降序 1 升序
            $_sort_type = (isset($params['sort_type']) && $params['sort_type']) ? $params['sort_type'] : null;
            // 0 订单创建时间查询 1 按修改时间查询 
            $_date_type = (isset($params['date_type']) && $params['date_type']) ? $params['date_type'] : null;

            $_get_sort = 'createtime desc';
            switch ($_sort_type) {
                default :
                case 0:
                    $_get_sort = 'createtime desc';
                    break;
                case 1:
                    $_get_sort = 'createtime asc';
                    break;
            }

            $_date_field = 'createtime';
            switch ($_date_type) {
                default :
                case 0:
                    $_date_field = 'createtime';
                    break;
                case 1:
                    $_date_field = 'last_modified';
                    break;
            }


            (!$_page || $_page < 1) && $_page = 1;
            (!$_pageSize || $_pageSize < 1 || $_pageSize > 100) && $_pageSize = 20;

            if (!$_startDate && !$_endDate) {
                $_startDate = date('Y-m-d', strtotime('-7 day'));
                $_endDate = date('Y-m-d');
            } elseif (!$_startDate || !$_endDate) {
                !$_startDate && $_endDate && $_startDate = date('Y-m-d', strtotime($_endDate . ' -7 day'));
                !$_endDate && $_startDate && $_endDate = date('Y-m-d', strtotime($_startDate . ' +7 day'));
            }

            if ((strtotime($_endDate) - strtotime($_startDate)) > (3600 * 24 * 31)) {
                $_endDate = date('Y-m-d', strtotime($_startDate . ' +31 day'));
            }



            // 'status' => 'active', 'pay_status' => 0,
            $filter = array(
                $_date_field . '|between' => array(
                    strtotime(date('Y-m-d 00:00:00', strtotime($_startDate))),
                    strtotime(date('Y-m-d 23:59:59', strtotime($_endDate))),
                ),
            );

            switch ($_order_state) {
                default :
                case 1: // 1 待付款
                    $filter['status'] = 'active';
                    $filter['pay_status'] = '0';
                    break;
                case 2: // 2 待发货
                    $filter['status'] = 'active';
                    $filter['pay_status|in'] = array(
                        '1',
                        '2',
                        '3',
                    );
                    $filter['ship_status'] = '0';
                    break;
                case 3: // 3 已发货
                    $filter['status'] = 'active';
                    $filter['ship_status|in'] = array(
                        '1',
                        '2',
                    );
                    break;
                case 9: // 9 已完成
                    $filter['status'] = 'finish';
                    break;
                case 99: // 99 已作废
                    $filter['status'] = 'dead';
                    break;
            }

            $_orders = $this->_get_orders($filter, ($_page - 1) * $_pageSize, $_pageSize);
            cachemgr::set($cache_key, $_orders, cachemgr::co_end());
        }

        if (!$_orders) return false;

        $res = $this->_field_rows($fields, $_orders);
        return $res;
    }

    protected function _set_cancel($order_id) {

        $order_id = is_numeric($order_id) && $order_id ? $order_id : null;
        if (!$order_id) return false;

        $data = array();
        $data['order_id'] = $order_id;

        $this->_begin();

        if (!vmc::singleton('b2c_order_cancel')->generate($data, $msg)) {
            $this->_end(false);
            return false;
        }

        $this->_end(true);

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['order_id'] = $order_id;

        return $res_data;
    }

    protected function _set_end($order_id) {

        $order_id = is_numeric($order_id) && $order_id ? $order_id : null;
        if (!$order_id) return false;

        $data = array();
        $data['order_id'] = $order_id;

        $this->_begin();

        if (!vmc::singleton('b2c_order_end')->generate($data, $msg)) {
            $this->_end(false);
            return false;
        }

        $this->_end(true);

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['order_id'] = $order_id;

        return $res_data;
    }

    protected function _write_pay($params) {
        $order_id = ($params && isset($params['order_id'])) ? $params['order_id'] : null;
        $money = ($params && isset($params['money'])) ? $params['money'] : null;

        $pay_mode = ($params && isset($params['pay_mode'])) ? $params['pay_mode'] : 'offline';
        $pay_app_id = ($params && isset($params['pay_app_id'])) ? $params['pay_app_id'] : 'cod';
        $payee_account = ($params && isset($params['payee_account'])) ? $params['payee_account'] : '__usr__';

        $order = $this->_get_byId($order_id, $fields);
        if (!$order_id || !$order || !$money) return false;
        $bill_sdf = array(
            'bill_type' => 'payment',
            'pay_object' => 'order',
            'member_id' => $order['member_id'],
            'status' => 'succ',
            'order_id' => $order_id,
            'money' => $money,
            'pay_mode' => $pay_mode,
            'pay_app_id' => $pay_app_id,
            'payee_account' => $payee_account,
        );

        $mdl_bills = app::get('ectools')->model('bills');

        $exist_bill = $mdl_bills->getRow('*', array(
            'member_id' => $order['member_id'],
            'order_id' => $order['order_id'],
            'status' => 'ready',
        ));
        if ($exist_bill) {
            $bill_sdf = array_merge($exist_bill, $bill_sdf);
        }
        $bill_sdf['app_id'] = 'b2c';

        $this->_begin();

        if (!vmc::singleton('ectools_bill')->generate($bill_sdf, $msg)) {
            $this->_end(false);
            return false;
        }

        $this->_end(true);

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['order_id'] = $order_id;

        return $res_data;
    }

    protected function _write_refund($params) {
        $order_id = ($params && isset($params['order_id'])) ? $params['order_id'] : null;
        //$money = ($params && isset($params['money'])) ? $params['money'] : null;

        $pay_mode = ($params && isset($params['pay_mode'])) ? $params['pay_mode'] : 'offline';
        $pay_app_id = ($params && isset($params['pay_app_id'])) ? $params['pay_app_id'] : 'cod';
        $payee_account = ($params && isset($params['payee_account'])) ? $params['payee_account'] : '__usr__';

        $payee_bank = ($params && isset($params['payee_bank'])) ? $params['payee_bank'] : '__bank__';

        $out_trade_no = ($params && isset($params['out_trade_no'])) ? $params['out_trade_no'] : null;
        $pay_fee = ($params && isset($params['pay_fee'])) ? $params['pay_fee'] : null;
        $payer_bank = ($params && isset($params['payer_bank'])) ? $params['payer_bank'] : null;
        $payer_account = ($params && isset($params['payer_account'])) ? $params['payer_account'] : null;
        $memo = ($params && isset($params['memo'])) ? $params['memo'] : null;

        //$return_score = ($params && isset($params['return_score'])) ? $params['return_score'] : 0;

        $order = $this->_get_byId($order_id, $fields);
        if (!$order_id || !$order || !$money) return false;

        $bill = app::get('ectools')->model('bills');
        $obj_bill = vmc::singleton('ectools_bill');

        $money = $order['payed'];
        $return_score = $order['score_g'];

        $bill_sdf = array(
            'bill_type' => 'refund',
            'pay_object' => 'order',
            'member_id' => $order['member_id'],
            'status' => 'succ',
            'app_id' => 'b2c',
            'order_id' => $order_id,
            'money' => $money,
            'pay_mode' => $pay_mode,
            'pay_app_id' => $pay_app_id,
            'payee_account' => $payee_account,
            'payee_bank' => $payee_bank,
            'out_trade_no' => $out_trade_no,
            'pay_fee' => $pay_fee,
            'payer_bank' => $payer_bank,
            'payer_account' => $payer_account,
            'memo' => $memo,
            'return_score' => $return_score,
        );

        $msg = null;

        $this->_begin();

        if (!$obj_bill->generate($bill_sdf, $msg)) {
            $this->_end(false);
            return false;
        }

        //退积分，即新建负积分记录对冲
        vmc::singleton('b2c_member_integral')->change(array(
            'member_id' => $order['member_id'],
            'order_id' => $order['order_id'],
            'change' => $return_score * -1,
            'change_reason' => 'refund',
            'op_model' => 'shopadmin',
                ), $msg);


        $this->_end(true);

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['order_id'] = $order_id;

        return $res_data;
    }

    protected function _delivery_send($params) {
        $order_id = ($params && isset($params['order_id'])) ? $params['order_id'] : null;
        $dlycorp_id = ($params && isset($params['dlycorp_id'])) ? $params['dlycorp_id'] : null;
        $dlyplace_id = ($params && isset($params['dlyplace_id'])) ? $params['dlyplace_id'] : null;
        $send_router = ($params && isset($params['send_router'])) ? $params['send_router'] : 'selfwarehouse';
        $logistics_no = ($params && isset($params['logistics_no'])) ? $params['logistics_no'] : null;
        $memo = ($params && isset($params['memo'])) ? $params['memo'] : null;
        $send = ($params && isset($params['send'])) ? $params['send'] : null;

        $order = $this->_get_byId($order_id, $fields);
        $dlyplace = app::get('b2c')->model('dlyplace')->dump($dlyplace_id);
        if (!$order_id || !$order || !$dlycorp_id || !$dlyplace_id || !$logistics_no || !$dlyplace || !$send || !is_array($send))
                return false;

        $delivery_sdf = array(
            'order_id' => $order_id,
            'delivery_type' => 'send', //发货
            'member_id' => $order['member_id'],
            'dlycorp_id' => $dlycorp_id, //实际选择的物流公司
            'dlyplace_id' => $dlyplace_id, //实际选择的发货地点id
            'send_router' => $send_router, //发货路由类型
            'logistics_no' => $logistics_no,
            'cost_freight' => $order['cost_freight'],
            'consignor' => $dlyplace['consignor'], //sdf array
            'consignee' => $order['consignee'], //sdf array
            'status' => 'succ',
            'memo' => $memo,
        );

        $msg = null;
        $obj_bill = vmc::singleton('ectools_bill');

        $this->_begin();

        $obj_delivery = vmc::singleton('b2c_order_delivery');
        //
        if (!$obj_delivery->generate($delivery_sdf, $send, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
            $this->_end(false);
            return false;
        }
        //开始判断是否进行发货，仓库货品状态发生改变
        //需要获取delivery信息
        $delivery_items = app::get('b2c')->model('delivery')->getList('delivery_id', array(
                                    'logistics_no' => $delivery_sdf['logistics_no'],
                                    'delivery_type' => $delivery_sdf['delivery_type']
        ));
        if (isset($delivery_sdf['logistics_no']) && $delivery_sdf['logistics_no'] != '') {
            //如果存在发货单，即表示仓库有变动
            $this->_end(true);

            $res_data = array();
            $res_data['modified'] = time();
            $res_data['order_id'] = $order_id;
            $res_data['logistics_no'] = $delivery_sdf['logistics_no'];    //增加发货单单号
            $res_data['delivery_id'] = $delivery_items;    //增加货单流水号
            $res_data['delivery_type'] = $delivery_sdf['delivery_type'];     //增加业务类型（发货or退货）
            $res_data['send_router'] = $delivery_sdf['send_router'];     //增加发货类型
            $res_data['consignor'] = $delivery_sdf['consignor'];     //增加发货人信息
            $res_data['consignee'] = $delivery_sdf['consignee'];     //增加收货人信息
            $res_data['status'] = $delivery_sdf['status'];     //增加发货单状态

            return $res_data;
        }else{
            //如果不存在，即仓库未变动
            $this->_end(true);

            $res_data = array();
            $res_data['comment'] = "仓库未出货";
            $res_data['modified'] = time();
            $res_data['order_id'] = $order_id;

            return $res_data;
        }

    }

    protected function _delivery_reship($params) {
        $order_id = ($params && isset($params['order_id'])) ? $params['order_id'] : null;
        $dlycorp_id = ($params && isset($params['dlycorp_id'])) ? $params['dlycorp_id'] : null;

        $dlyplace_id = ($params && isset($params['dlyplace_id'])) ? $params['dlyplace_id'] : null;
        $send_router = ($params && isset($params['send_router'])) ? $params['send_router'] : 'return';
        $logistics_no = ($params && isset($params['logistics_no'])) ? $params['logistics_no'] : null;
        $memo = ($params && isset($params['memo'])) ? $params['memo'] : null;
        $cost_freight = ($params && isset($params['cost_freight'])) ? $params['cost_freight'] : null;
        $send = ($params && isset($params['send'])) ? $params['send'] : null;

        $order = $this->_get_byId($order_id, $fields);
        $dlyplace = app::get('b2c')->model('dlyplace')->dump($dlyplace_id);
        if (!$order_id || !$order || !$dlycorp_id || !$dlyplace_id || !$logistics_no || !$dlyplace || !$send || !is_array($send))
                return false;
        $delivery_items = app::get('b2c')->model('delivery')->getlist('delivery_id,status',array(
                                            'logistics_no' => $params['logistics_no'],
                                            'delivery_type' => 'reship',
                                            'order_id' => $params['order_id'],
                                            ));

        $delivery_sdf = array(
            'order_id' => $order_id,
            'delivery_type' => 'reship', //退货
            'member_id' => $order['member_id'],
            'dlycorp_id' => $dlycorp_id, //实际选择的物流公司
            'dlyplace_id' => $dlyplace_id, //实际选择的退货地点id
            'send_router' => 'return', //固定客户退回类型
            'logistics_no' => $logistics_no,
            'cost_freight' => $cost_freight,
            'consignor' => $order['consignee'], //sdf array
            'consignee' => $dlyplace['consignor'], //退回发货地
            'status' => 'succ',
            'memo' => $memo,
        );


        $msg = null;

        $this->_begin();

        $obj_delivery = vmc::singleton('b2c_order_delivery');
        if (!$obj_delivery->generate($delivery_sdf, $send, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
            $this->_end(false);
            return false;
        }

        $this->_end(true);

        //组合数组信息
        $res_data = array();
        $res_data['modified'] = time();
        $res_data['order_id'] = $order_id;

        //退货单需点击确认后方可返回如下数据
        if (isset($delivery_items[0]['status']) && $delivery_items[0]['status'] == 'succ') {
            $res_data['confirm'] = 'true';   //订单已确认
            $res_data['logistics_no'] = $delivery_sdf['logistics_no'];    //增加发货单单号
            $res_data['delivery_id'] = $delivery_items;    //增加货单流水号
            $res_data['delivery_type'] = $delivery_sdf['delivery_type'];     //增加业务类型（发货or退货）
            $res_data['send_router'] = $delivery_sdf['send_router'];     //增加发货类型
            $res_data['consignor'] = $delivery_sdf['consignor'];     //增加发货人信息
            $res_data['consignee'] = $delivery_sdf['consignee'];     //增加收货人信息
            $res_data['status'] = $delivery_sdf['status'];     //增加发货单状态
        }elseif (isset($delivery_items[0]['status']) && $delivery_items[0]['status'] == 'ready') {
            //组合数组信息
            $res_data['confirm'] = 'false';    //订单未确认
        }

        return $res_data;
    }

}
