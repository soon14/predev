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



class codebuy_finder_activity{
    public function __construct($app)
    {
        $this->app = $app;
    }
    var $column_control = '操作';
    public function column_control($row)
    {
        $download_html = "<a onclick=\"$('#codebuy_modal').find('input[name=activity_id]').val(".$row['id'].")\" class='btn btn-xs btn-default' href='#codebuy_modal' class='btn btn-xs btn-defaut' data-toggle='modal'><i class='fa fa-share-square'></i> 发行优购码</a>";
        $code_list = "<a class='btn btn-xs btn-default' href='index.php?app=codebuy&ctl=admin_code&act=index&p[0]=".$row['id']."' class='btn btn-xs btn-defaut' ><i class='fa fa-list'></i> 查看已发行优购码</a>";
        $edit_html = "<a href='index.php?app=codebuy&ctl=admin_activity&act=edit&p[0]=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i>编辑</a>";
        return $edit_html.$download_html.$code_list;
    }

    var $column_number = '已发行总量';
    public function column_number($row)
    {
        $db = vmc::database();
        $count = $db->exec('select count(id) as count from vmc_codebuy_code where activity_id='.$row['id']);
        if($count['rs'][0]['count']){
            $_return = $count['rs'][0]['count'];
        }else{
            $_return = 0;
        }
        return $_return;
    }

    var $column_member = '已使用总量';
    public function column_member($row)
    {
        $db = vmc::database();
        $count = $db->exec('select count(id) as count from vmc_codebuy_log where activity_id='.$row['id']);
        if($count['rs'][0]['count']){
            $_return = $count['rs'][0]['count'];
        }else{
            $_return = 0;
        }
        return $_return;
    }
}
