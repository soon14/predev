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


 
class serveradm_finder_xhprof{
    var $detail_basic = '基本信息';
    var $column_control = '操作';
    function __construct($app){
        $this->app = $app;
    }
    
    function column_control($row){
        return '<a href="index.php?app=serveradm&ctl=admin_xhprof&act=show&p[0]='.$row['run_id'].'"  target="blank">'."分析报告".'</a>';
    }
    
    function detail_basic($id){
        $render =  app::get('serveradm')->render();
        $oXHProf = $render->app->model('xhprof');
        $aData=$oXHProf->dump($id);
        $render->pagedata['data'] = $aData;
        return $render->fetch('admin/xhprof_detail.html',$this->app->app_id);
    }
}
