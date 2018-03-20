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
class o2ocds_service_enterprise_createafter
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 企业注册成功以后
     */
    public function exec($enterprise, &$msg = '')
    {
        if($enterprise['enterprise_id']) {
            //保存关系
            $relation = array(
                'relation_id' => $enterprise['enterprise_id'],
                'member_id' => $enterprise['member_id'],
                'type' => 'enterprise',
                'relation' => 'admin',
                'time' => time()
            );
            if(!$this->app->model('relation')->save($relation)) {
                $msg = '企业管理员关系'.$enterprise['name'].'关系保存失败';
                logger::alert($msg);
                return false;
            };
        }
        return true;
    }

}
