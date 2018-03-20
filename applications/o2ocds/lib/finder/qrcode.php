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
class o2ocds_finder_qrcode
{
    public $column_qrcode = '二维码ID(批次号+5位序号)';
    public $column_qrcode_image = '二维码图片';
    public $column_sno = '店铺编号';
    public $column_eno = '企业编号';
    public $column_control = '操作';


    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_control($row)
    {
        $returnValue = '';
        if(vmc::singleton('desktop_user')->has_permission('o2ocds_edit_qrcode')) {
            $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=o2ocds&ctl=admin_qrcode&act=edit&p[0]='.$row['qrcode_id'].'"><i class="fa fa-edit"></i>编辑</a>';
        }
        return $returnValue;
    }

    public function column_qrcode($row){
        return $row['prefix'].$row['qrcode'];
    }

    public function column_qrcode_image($row) {
        $qrcode = $row['prefix'].$row['qrcode'];
        return "<a target='_blank' class='btn btn-xs btn-default' href='index.php?app=o2ocds&ctl=admin_qrcode&act=qrcode&p[0]=$qrcode&singlepage=1'><i class='fa fa-qrcode'></i></a>";
    }

    public function column_sno($row) {
        if($sno = $this->app->model('store')->getRow('sno',array('store_id'=>$row['store_id']))['sno']) {
            return $sno;
        };
        return '';
    }

    public function column_eno($row) {
        if($eno = $this->app->model('enterprise')->getRow('eno',array('enterprise_id'=>$row['enterprise_id']))['eno']) {
            return $eno;
        };
        return '';
    }

}
