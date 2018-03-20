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


class store_finder_storeorder
{

    public $column_control = '操作';
    public $column_payment = '收款单';
    public $column_control_order = HEAD;
    public function column_control($row)
    {
        $order_id = $row['order_id'];
        if ( vmc::singleton('desktop_user') ->has_permission('order_detail')) {
            $opt_btn = "<a href='index.php?app=b2c&ctl=admin_order&act=detail&p[0]=" . $row['order_id'] . "' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> 处理订单</a>";
        }
        $order = app::get('b2c') ->model('orders') ->getRow('*' ,array('order_id' => $order_id));
        $print_receipt = $order['pay_status'] =='1' ?'<li>
                    <a target="_blank" href="index.php?app=store&ctl=admin_pay&act=print_receipt&orderId='.$order_id.'&singlepage=1&renew=1">重打小票</a>
				</li>' :'';
        $print_btn = <<<HTML
        <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default" data-toggle="dropdown"   data-close-others="true" aria-expanded="false"><i class="fa fa-print"></i> 打印</button>
        <ul class="dropdown-menu" role="menu">
                $print_receipt
                <li>
                    <a target="_blank" href="index.php?app=store&ctl=admin_pay&act=print_cashier_credentials&orderId=$order_id&singlepage=1">重打凭证</a>
				</li>
				<li class="divider">
				</li>
				<li>
                    <a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=1&p[1]=$order_id">打印购物小票</a>
				</li>
				<li>
                    <a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=2&p[1]=$order_id">打印配货单</a>
				</li>
				<li class="divider">
				</li>
                <li>
                    <a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=3&p[1]=$order_id">打印订单详情</a>
                </li>
			</ul>
        </div>
HTML;

        return $opt_btn . $print_btn;
    }


    /**
     * 开启全列方法?
     *
     * @param $row
     *
     * @return string
     */
    public function row_style($row){
        if($row['status'] == 'finish' || $row['status'] == 'dead'){

            return 'text-muted';
        }
    }
    public function column_payment($row){
        $order_id = $row['order_id'];
        $bills = app::get('ectools') ->model('bills') ->getList('bill_id' ,array('order_id'=>$order_id ,'status'=>'succ' ,'bill_type' =>'payment'));
        return implode("," ,array_keys(utils::array_change_key($bills ,'bill_id')));
    }

}
