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
class commission_finder_cash
{
    public $column_control = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        if($row['status'] != 1 ){
            return '<span class="label label-success">' . '已处理'. '</span>';
        }else{
            $cash_id = $row['cash_id'];
            $returnValue = '<span id="cash_control_'.$cash_id.'"><a class="btn btn-default btn-xs" href="javascript:cash('.$cash_id.' ,2);"><i class="fa fa-credit-card"></i>成功</a>';

            $returnValue .= '<a class="btn btn-default btn-xs"  href="javascript:cash('.$cash_id.',3);"><i class="fa fa-warning"></i> 失败</a></span>';
            return $returnValue;
        }



    }
}