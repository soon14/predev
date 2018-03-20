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
class commission_finder_member_relation
{
    public $column_control = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function column_control($row)
    {
        $children = vmc::database()->select("SELECT count(*) as count FROM {$this ->app->model('member_relation')->table_name(1)} WHERE  parent_id = {$row['member_id']}");
        if($children[0]['count'] >0){
            $url_preview = 'index.php?app=commission&ctl=admin_member&act=index&p[0]='.$row['member_id'].'&p[1]=1';

            $returnValue = '<a class="btn btn-default btn-xs" href="'.$url_preview.'"><i class="fa fa-external-link"></i> 下级</a>';

            return $returnValue;
        }

    }
}