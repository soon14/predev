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


class adjustprices_mdl_plan extends dbeav_model
{

    public function __construct($app)
    {
        parent::__construct($app);

    }
    public $defaultOrder = array(
        'createtime DESC',
    );

    public function pre_recycle($rows)
    {
        $this->recycle_msg = '删除成功!';
        $plan_ids = array_keys(utils::array_change_key($rows, 'plan_id'));
        if($this->app->model('job')->count(array('plan_id'=>$plan_ids))){
            $this->recycle_msg = '请先清空计划内需要调价商品';
            return false;
        }
        return true;
    }


}
