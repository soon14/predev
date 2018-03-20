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


class serveradm_xhprof
{   
    static public function isExtension(){
        return extension_loaded('xhprof');
    }
    
    static public function begin($params = null , $ignore = null)
    {
        if(!self::isExtension()) die("没有xhprof扩展!"); // 可以return掉 则不影响正常程序
        $params = ($params)? $params : XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS;
        $ignore = ($ignore)? $ignore : array('ignored_functions' => array('call_user_func','call_user_func_array'));
        xhprof_enable($params,$ignore);
    }
    
    static public function end($source = null)
    {
        if(!self::isExtension()) die("没有xhprof扩展!"); // 可以return掉 则不影响正常程序
        $xhprof_data = xhprof_disable();
        $oXHProf = app::get("serveradm")->model("xhprof");
        //$run_id = $oXHProf->write_data($xhprof_data);
        
        include_once(dirname(__FILE__)."/../vendor/xhprof_lib/utils/xhprof_lib.php");
        include_once(dirname(__FILE__)."/../vendor/xhprof_lib/utils/xhprof_runs.php");
        $xhprof_runs = new XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof");
        
        $aData =  array(
                     'source'=>'xhprof',
                     'run_id'=>$run_id,
                     'request_uri'=>vmc::request()->get_request_uri(),
                     'app'=>$_GET['app'],
                     'ctl'=>$_GET['ctl'],
                     'act'=>$_GET['act'],
                     'wt'=>$xhprof_data["main()"]["wt"],
                     'mu'=>$xhprof_data["main()"]["mu"],
                     'pmu'=>$xhprof_data["main()"]["pmu"],
                     'addtime'=>time(),
                  );
        $oXHProf->save($aData);
    }
}