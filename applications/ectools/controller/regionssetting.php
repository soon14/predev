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



class ectools_ctl_regionssetting extends desktop_controller{



    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }



    function save_depth(){
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $rs = $this->app->setConf('system_area_depth',$_POST['area_depth']);
        if($rs){
            $this->end(vmc::singleton('ectools_regions_operation')->updateRegionData());
        }
        $this->end($rs);
    }

    function install(){
        set_time_limit(0);
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $package = vmc::service('ectools_regions.ectools_mdl_regions');
        $rs = $package->install();
        $this->end($rs);
    }

    function setDefault(){
        set_time_limit(0);
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $model = $this->app->model('regions');
        $model->clearOldData();
        $package = vmc::service('ectools_regions.ectools_mdl_regions');
        $rs = $package->install();
        if($rs) {
            $this->end(vmc::singleton('ectools_regions_operation')->updateRegionData());
        }
        $this->end($rs);
    }

    function save_regions_package(){
        $this->begin('index.php?app=ectools&ctl=regionssetting&act=index');
        $rs = app::get('base')->setConf('service.ectools_regions.ectools_mdl_regions' , $_POST['service']['ectools_regions.ectools_mdl_regions']);
        $this->end($rs);
    }

    function sel_region($path,$depth)
    {
        header('Content-type: text/html;charset=utf8');
        $region_select = vmc::singleton('ectools_regions_select');
        echo '&nbsp;-&nbsp;'.$region_select->get_area_select($this->app,$path,array('depth'=>$depth));
        //$regions = $this->app->model('regions');
        //echo '&nbsp;-&nbsp;'.$regions->get_area_select($path,array('depth'=>$depth));
    }
}
