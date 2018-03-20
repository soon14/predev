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


class experiencestore_finder_activity_schedule
{
    public $column_control = '操作';
    public $column_1 = '剩余可预约';
    public $column_2 = '状态';
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {

        $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=experiencestore&ctl=admin_activity&act=edit_schedule&p[0]='.$row['id'].'"><i class="fa fa-edit"></i> 编辑</a> <a class="btn btn-default btn-xs" href="index.php?app=experiencestore&ctl=admin_activity&act=edit_schedule&p[0]='.$row['id'].'&p[1]=true"><i class="fa fa-copy"></i> 复制</a>';
        return $returnValue;
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }

    public function column_1($row){
        $row = $row['@row'];
        return $row['ticket_amount'] -$row['sale_amount']-$row['reserve'];
    }

    public function column_2($row){
        $row = $row['@row'];
        if($row['from_time']>time()){
            return '未开始';
        }elseif($row['to_time']<time()){
            return '已结束';
        }else{
            return '进行中';
        }
    }
}
