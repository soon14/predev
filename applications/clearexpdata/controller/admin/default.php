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


class clearexpdata_ctl_admin_default extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        if (!$this->user->is_super()) {
            die("<div class='alert alert-danger'>只有超级管理员权限才可以进行如此危险的操作!</div>");
        }
    }

    public function clear()
    {
        if (!$_POST) {
            $this->pagedata['current_super'] = $this->user->user_data;
            $this->page('admin/clear.html');
        } else {
            $this->begin();
            if ($_POST['uname'] != $this->user->user_data['name']) {
                $this->end(false, '异常操作');
            }
            if (!$this->_trylogin($_POST)) {
                $this->end(false, '密码错误');
            }
            if ($this->_doclear()) {
                $this->end(true);
            } else {
                $this->end(false);
            }
        }
    }
    private function _doclear()
    {
        app::get('b2c')->model('goods')->delete(array('goods_id|than' => 0));
        app::get('b2c')->model('products')->delete(array('product_id|than' => 0));
        app::get('b2c')->model('goods_mark')->delete(array('mark_id|than' => 0));
        app::get('b2c')->model('goods_rate')->delete(array('goods_1|than' => 0));
        app::get('b2c')->model('goods_type')->delete(array('type_id|than' => 0));
        app::get('b2c')->model('goods_cat')->delete(array('cat_id|than' => 0));
        app::get('b2c')->model('goods_cat')->clean_cache();
        app::get('b2c')->model('brand')->delete(array('brand_id|than' => 0));
        app::get('b2c')->model('stock')->delete(array('stock_id|than' => 0));
        app::get('b2c')->model('orders')->delete(array('order_id|than' => 0));
        app::get('pam')->model('members')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('members')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('member_addrs')->delete(array('addr_id|than' => 0));
        app::get('b2c')->model('member_goods')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('member_couponlog')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('member_coupon')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('member_comment')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('member_msg')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('cart_objects')->delete(array('member_id|than' => 0));
        app::get('b2c')->model('member_goods')->delete(array('member_id|than' => 0));
        app::get('ectools')->model('bills')->delete(array('bill_id|than' => 0));
        app::get('b2c')->model('delivery')->delete(array('delivery_id|than' => 0));
        app::get('b2c')->model('order_log')->delete(array('log_id|than' => 0));
        return true;
    }

    private function _trylogin($params)
    {
        $type = pam_account::get_account_type('desktop');
        $user_data['login_name'] = $params['uname'];
        $arr = app::get('pam')->model('account')->getRow('*', array(
                'login_name' => $params['uname'],
                'login_password' => pam_encrypt::get_encrypted_password($params['password'], $type, $user_data),
                'account_type' => $type,
                'disabled' => 'false',
                ), 0, 1
            );

        return $arr;
    }
}
