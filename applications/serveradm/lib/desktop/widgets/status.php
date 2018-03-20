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




class serveradm_desktop_widgets_status implements desktop_interface_widget
{
    /**
     * 构造方法，初始化此类的某些对象
     * @param object 此应用的对象
     * @return null
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->render =  new base_render(app::get('serveradm'));
    }

    function get_order() {
        return 1;
    }
    function get_layout() {
        return 'bottom';
    }
    function get_title() {
        return false;
    }
    /**
     * 获取桌面widgets的html内容
     * @param null
     * @return string html内容
     */
    public function get_html($from,$to)
    {
        $render = $this->render;
        $render->pagedata['sections'] =  $this->sections();

        $oStatus = vmc::singleton("serveradm_status");
        $render->pagedata['cache'] =  $oStatus->getCacheInfo();
        $render->pagedata['kvstore'] =  $oStatus->getKVStorageInfo();
        $render->pagedata['db'] =  $oStatus->getMysqlStatus();
        $render->pagedata['xhprof'] =  $oStatus->getXHProfStatus();
        $render->pagedata['server'] =  $oStatus->getServerInfo();

        return $render->fetch('desktop/widgets/status.html');
    }


    // sesctions
    private function sections(){
        return array(
                    array(
                        "name"=>"缓存信息",
                        "file"=>"desktop/widgets/cache_status.html",
                    ),
                    array(
                        "name"=>"数据库信息",
                        "file"=>"desktop/widgets/db_status.html",
                    ),
                    array(
                        "name"=>"服务器信息",
                        "file"=>"desktop/widgets/server_status.html",
                    ),
                    array(
                        "name"=>"XHPROF",
                        "file"=>"desktop/widgets/xhprof_status.html",
                    ),
        );
    }
}
