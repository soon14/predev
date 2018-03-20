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

class registercoupon_mdl_rule extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function save_update($data){
        if($data['cpns_id']<1){
            throw new Exception('请选择要使用的优惠券');
        }
        $data['from_time'] = strtotime($data['from_time']) ;
        $data['to_time'] = strtotime($data['to_time']);
        if($data['from_time'] >= $data['to_time'] ){
            throw new Exception('请设置正确的有效期');
        }
        $this ->save($data);
    }
}