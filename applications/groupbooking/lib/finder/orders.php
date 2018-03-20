<?php

/**
 * Created by PhpStorm.
 * User: cp
 * Date: 2017/5/24
 * Time: 16:25
 */

class groupbooking_finder_orders
{
    public $detail_products = '子订单信息';
    public $column_is_main = '是否是主单';
    public $column_activity_name = '拼团名称';
    public $column_login_account = '账户';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function detail_products($gb_id)
    {
        $render = $this->app->render();
        if($child_orders = $this->app->model('orders')->getList('*',array('main_id'=>$gb_id))) {
            $render->pagedata['child_orders'] = $child_orders;
            $member_ids = array_keys(utils::array_change_key($child_orders,'member_id'));
            $members = app::get('pam')->model('members')->getList('member_id,login_account',array('member_id'=>$member_ids));
            $render->pagedata['members'] = utils::array_change_key($members,'member_id');
        }
        $render->pagedata['order'] = $this->app->model('orders')->getRow('*',array('gb_id'=>$gb_id));
        return $render->fetch('admin/order/detail.html');
    }

    public function column_activity_name($row) {
        $activity_name = app::get('groupbooking')->model('activity')->getRow('name',array('activity_id'=>$row['@row']['activity_id']));
        return $activity_name['name'];
    }

    public function column_login_account($row) {
        $login_account = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$row['@row']['member_id']));
        return $login_account['login_account'];
    }

    public function column_is_main($row) {
        if($row['@row']['main_id'] == '0') {
            return '是';
        }
        return '否';
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }




}
