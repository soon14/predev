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
class commission_ctl_admin_type extends desktop_controller
{
    public function index()
    {
        $this->finder('commission_mdl_type_extend', array(
            'title' => ('商品类型分佣设置'),
            'use_buildin_recycle' => false,
            'finder_extra_view' => array(array('app' => 'commission', 'view' => '/admin/type/finder_extra.html'),),
        ));
    }

    public function update_commission()
    {
        $this->begin('index.php?app=commission&ctl=admin_type&act=index');
        $params = $_POST;
        if (!$params['type_id']) {
            $this->end(false, "参数错误");
        }
        $service_chk_column = vmc::singleton('commission_service_check_column');
        if (false == $service_chk_column->check_column_value($params, $msg)) {
            $this->end(false, $msg);
        }
        $this->app->model('type_extend')->update($params,
            array('type_id' => $params['type_id'])
        );
        $this->end(true);
    }

}
