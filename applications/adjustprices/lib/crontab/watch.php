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


class adjustprices_crontab_watch extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_plan = app::get('adjustprices')->model('plan');
        $mdl_plan_job = app::get('adjustprices')->model('job');
        $mdl_products = app::get('b2c')->model('products');
        $plan_job_table_name = $mdl_plan_job->table_name(1);
        $products_table_name = $mdl_products->table_name(1);
        $t = time();
        //到时间，需调价有效计划
        $fix_carry_out_plan = $mdl_plan->getList('*', array('carry_out_time|notin' => array('', null),'carry_out_time|sthan' => $t, 'plan_status' => '1'));

        if ($fix_carry_out_plan) {
            $plan_ids = array_keys(utils::array_change_key($fix_carry_out_plan, 'plan_id'));
            $db = vmc::database();
            $trans_status = $db->beginTransaction();

            $mdl_plan->update(array('plan_status' => '2'), array('plan_id' => $plan_ids));
            $adjustprices_sql = "UPDATE $products_table_name INNER JOIN $plan_job_table_name ON $products_table_name.product_id = $plan_job_table_name.product_id  SET $products_table_name.price=$plan_job_table_name.end_price WHERE $plan_job_table_name.plan_id IN (".implode(',', $plan_ids).')';
            logger::debug('adjustprices carry_out SQL:'.$adjustprices_sql);
            if ($db->exec($adjustprices_sql, true) &&     $mdl_plan->update(array('plan_status' => '4'), array('plan_id' => $plan_ids))
            ) {
                $db->commit($trans_status);
            } else {
                $db->rollBack();
            }
        }

        //到时间，需回滚计划
        $fix_rollback_plan = $mdl_plan->getList('*', array('rollback_time|notin' => array('', null), 'rollback_time|sthan' => $t, 'plan_status' => '4'));

        if ($fix_rollback_plan) {
            $plan_ids = array_keys(utils::array_change_key($fix_rollback_plan, 'plan_id'));
            $db = vmc::database();
            $trans_status = $db->beginTransaction();

            $mdl_plan->update(array('plan_status' => '3'), array('plan_id' => $plan_ids));
            $adjustprices_sql_rollback = "UPDATE $products_table_name INNER JOIN $plan_job_table_name ON $products_table_name.product_id = $plan_job_table_name.product_id  SET $products_table_name.price=$plan_job_table_name.begin_price WHERE $plan_job_table_name.plan_id IN (".implode(',', $plan_ids).')';
            logger::debug('adjustprices rollback SQL:'.$adjustprices_sql_rollback);
            if ($db->exec($adjustprices_sql_rollback, true) &&     $mdl_plan->update(array('plan_status' => '5'), array('plan_id' => $plan_ids))
            ) {
                $db->commit($trans_status);
            } else {
                $db->rollBack();
            }
        }
    }
}
