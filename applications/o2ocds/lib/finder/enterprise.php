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
class o2ocds_finder_enterprise
{
    public $column_control = '操作';
    public $column_accounts = '员工';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $returnValue = '';
        if(vmc::singleton('desktop_user')->has_permission('o2ocds_edit_enterprise')) {
            $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=o2ocds&ctl=admin_enterprise&act=edit&p[0]=' . $row['enterprise_id'] . '"><i class="fa fa-edit"></i>编辑\查看</a>';
        }
        return $returnValue;
    }

    public function column_accounts($row) {
        $members = $this->app->model('relation')->getList('member_id,relation',array('relation_id'=>$row['enterprise_id'],'type'=>'enterprise'));
        $_return = [];
        foreach ($members as $item) {
            $member_id = $item['member_id'];
            $face_url = vmc::singleton('b2c_view_helper')->modifier_avatar($member_id);
            $face_size = ($item['relation'] == 'admin'?'25px':'25px');
            $face_border = ($item['relation'] == 'admin'?'border-color:#d64635':'');
            $_return[] = "<a href='index.php?app=b2c&ctl=admin_member&act=detail&p[0]=$member_id' target='_blank'><img src='$face_url' style='width:$face_size;padding:1px;height:$face_size;$face_border' class='img-circle img-thumbnail'/></a>";
        }
        return "<div style='white-space:normal;'>".implode("&nbsp;",$_return)."</div>";
    }
}
