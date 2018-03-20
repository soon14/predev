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



class sale_finder_sales{
    public function __construct($app)
    {
        $this->app = $app;
    }
    var $column_control = '操作';
    public function column_control($row)
    {
        $reserve_list = "<a class='btn btn-xs btn-default' href='index.php?app=sale&ctl=admin_reserve&act=index&p[0]=".$row['id']."' class='btn btn-xs btn-defaut' ><i class='fa fa-list'></i> 查看已预约客户</a>";
        $edit_list = "<a href='index.php?app=sale&ctl=admin_index&act=edit&p[0]=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i>编辑</a>";
        return $reserve_list.$edit_list;
    }

    var $column_number = '抢购数量';
    public function column_number($row)
    {
        $db = vmc::database();
        $count = $db->exec('select sum(number) as count from vmc_sale_reserve where sale_id='.$row['id'].' and status=\'1\'');
        if($count['rs'][0]['count']){
            $_return = $count['rs'][0]['count'];
        }else{
            $_return = 0;
        }
        return $_return;
    }

    var $column_member = '已预约会员数';
    public function column_member($row)
    {
        $db = vmc::database();
        $count = $db->exec('select count(id) as count from vmc_sale_reserve where sale_id='.$row['id']);
        if($count['rs'][0]['count']){
            $_return = $count['rs'][0]['count'];
        }else{
            $_return = 0;
        }
        return $_return;
    }
}
