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

class registercoupon_finder_rule
{

    public $column_edit = '操作';

    public function column_edit($row)
    {
        return '<a class="btn btn-default btn-xs" href="index.php?app=registercoupon&ctl=admin_rule&act=edit&p[0]=' . $row['rule_id'] . '" ><i class="fa fa-edit"></i> ' . ('编辑') . '</a>';
    }

    public $column_rule_status = '状态';

    public function column_rule_status($row)
    {
        if (isset($row['@row'])) {
            $row = $row['@row'];
        }
        $_return = '';
        if ($row['rule_status'] == '1') {
            $_return = $this->wrap_label('启用', 'success');
        }
        if ($row['rule_status'] == '0') {
            $_return = $this->wrap_label('禁用', 'warning');

        }

        return $_return;
    }

    private function wrap_label($c, $t)
    {
        return '<span class="label label-' . $t . '">' . $c . '</span>';
    }

    /*
     * 开启全列
     */
    public function row_style()
    {

    }
}