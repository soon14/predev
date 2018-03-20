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
class commission_ctl_admin_cash extends desktop_controller{
    public function index()
    {
        $this->finder('commission_mdl_cash', array(
            'title' => ('提现申请'),
            'use_buildin_recycle' => true,
            'finder_extra_view' => array(array('app' => 'commission', 'view' => '/admin/cash/finder_extra.html'),),
        ));
    }

    /*
     * 提现处理
     */
    public function do_cash(){
        $this ->begin('index.php?app=commission&ctl=admin_cash&act=index');
        try{
            $this ->app ->model('cash') ->do_cash($_GET['cash_id'] ,$_GET['status']);
        }catch (Exception $e){
            $this ->end(false );
        }
        $this ->end(true );
    }
}
