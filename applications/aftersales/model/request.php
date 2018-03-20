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


class aftersales_mdl_request extends dbeav_model
{
    /*
     * @var 是否使用tag
     */
    public $has_tag = true;
    public $defaultOrder = array('createtime','DESC');
    public $has_many = array(
        'images' => 'image_attach@aftersales:contrast:request_id^target_id',
        );

    public $has_one = array(
        'order' => 'orders@b2c:append:order_id^order_id',
        'member' => 'members@b2c:append:member_id^member_id',
        'maddr' => 'member_addrs@b2c:append:member_addr_id^addr_id',
        'reship' => 'delivery@b2c:append:delivery_id^delivery_id',
        'refund' => 'bills@ectools:append:bill_id^bill_id',
    );
    public $subSdf = array(
        'default_sub' => array(
            'order' => array(
                '*',
            ),
            'member' => array(
                '*',
            ) ,
            'maddr' => array(
                '*',
            ),
            'reship' => array(
                '*',
            ),
            'refund' => array(
                '*',
            ),
            'images' => array(
                'image_id',
            ),
        ) ,

    );
    /**
     * 得到唯一的编号.
     *
     * @params null
     *
     * @return string 售后序号
     */
    public function apply_id()
    {
        $tb = $this->table_name(1);
        do {
            $i = substr(mt_rand() , -3);
            $request_id = '7'.date('ymdHis').$i;
            $row = $this->db->selectrow('SELECT request_id from '.$tb.' where request_id ='.$request_id);
        } while ($row);

        return $request_id;
    }

    /**
     * 修改finder显示的会员ID-变成会员用户名.
     *
     * @param array 单条数据数组
     *
     * @return string 会员登录名
     */
    public function modifier_member_id($member_id)
    {
        return "<a href='index.php?app=b2c&ctl=admin_member&act=detail&p[0]=$member_id' target='_blank'>".vmc::singleton('b2c_user_object')->get_member_name(null, $member_id).'</a>';
    }

    public function modifier_order_id($order_id)
    {
        return "<a href='index.php?app=b2c&ctl=admin_order&act=detail&p[0]=$order_id' target='_blank'>$order_id</a>";
    }

    public function modifier_status($col,$row){
        switch ($col) {
            case '1':
            return "等待审核";
            case '2':
            return "<span class='text-danger'><i class='fa fa-warning'></i> 已拒绝</span>";
            case '3':
            return "退货处理中";
            case '4':
            return "退款处理中";
            case '5':
            return "<span class='text-success'><i class='fa fa-heart'></i> 完成</span>";
        }
    }
}
