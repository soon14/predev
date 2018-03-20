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


class restrict_mdl_member_log extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /*
     * 统计该活动的购买的数量/过滤掉订单取消作废的订单
     * */
    public function sum_quantity($filter) {
        $filter = $this->_filter($filter);
        $sql = "SELECT sum(vmc_restrict_member_log.quantity) as sum FROM `vmc_restrict_member_log`  INNER JOIN `vmc_b2c_orders` bo ON  vmc_restrict_member_log.order_id = bo.order_id
                WHERE $filter"." AND bo.status  <> 'dead'";
        if($res  = $this->db->select($sql)) {
            return $res[0]['sum']?:0;
        };
        return false;
    }




}
