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
class logisticstrack_ctl_admin_custom extends desktop_controller
{
    public function __construct ($app)
    {
        parent::__construct ($app);
    }

    public function logistic ()
    {
        $this->finder ('logisticstrack_mdl_customer_logistic', array(
            'title' => '客户物流单列表',
            'use_buildin_filter' => true,
            'actions' => array(
                array(
                    'label' => ('设置物流状态同步API'),
                    'icon' => 'fa fa-cog',
                    'href' => 'index.php?app=logisticstrack&ctl=admin_tracker&act=apiset',
                ),
            )
        ));

    }
}