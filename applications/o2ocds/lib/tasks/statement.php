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


class o2ocds_tasks_statement extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        ini_set('memory_limit','528M');
        $statement_day = app::get('o2ocds')->getConf('auto_statement_day');
        $statement_time = app::get('o2ocds')->getConf('statement_time');

        if(!$statement_time) {
            //初始化结算时间
            app::get('o2ocds')->setConf('statement_time',date('Y-m-d', strtotime('+'.$statement_day.' days')));
            return true;
        }
        //当前时间
        $current_time = date('Y-m-d');
        //到结算时间了进行结算
        if($current_time >= $statement_time) {
            $time = time();
            $mdl_orderlog_achieve = app::get('o2ocds')->model('orderlog_achieve');
            $orderlog_achieve_arr = $mdl_orderlog_achieve->getList('achieve_id,relation_id,type,achieve_fund', array('status' => 'ready'));
            if (!$orderlog_achieve_arr) {
                return true;
            }
            foreach($orderlog_achieve_arr as &$v) {
                $orderlog_achieve_arr_supplier_group[$v['type'].'_'.$v['relation_id']][] = $v;
                unset($v);//释放内存
            }
            $mdl_statement = app::get('o2ocds')->model('statement');
            foreach ($orderlog_achieve_arr_supplier_group as $orderlog_achieve_arr) {
                $statement_data = array(
                    'statement_id' => $mdl_statement->apply_id(),
                    'relation_id' => $orderlog_achieve_arr[0]['relation_id'],
                    'relation_type' => $orderlog_achieve_arr[0]['type'],
                    'op_id' => $this->user->user_id,
                    'createtime' => $time,
                    'status' => 'noconfirm',
                );

                foreach ($orderlog_achieve_arr as $orderlog_achieve) {
                    $statement_data['money'] += $orderlog_achieve['achieve_fund'];
                    $statement_data['statement_index'][] = array(
                        'statement_id' => $statement_data['statement_id'],
                        'achieve_id' => $orderlog_achieve['achieve_id'],
                    );
                    $update_orderlog_achieve = $orderlog_achieve;
                    $update_orderlog_achieve['status'] = 'process';
                    if (!$mdl_orderlog_achieve->save($update_orderlog_achieve)) {
                        logger::error('更新结算凭证状态失败!'.$update_orderlog_achieve['achieve_id']);
                        return false;
                    } else {
                        foreach (vmc::servicelist('o2ocds.orderlog_achieve.update') as $service) {
                            if (!$service->exec($update_orderlog_achieve, $msg)) {
                                logger::error($msg);
                            }
                        }
                    }
                }
                $mdl_statement->complete_bank($statement_data); //引用传递
                if (!$mdl_statement->save($statement_data)) {
                    logger::error('结算单保存失败');
                    return false;
                } else {
                    foreach (vmc::servicelist('o2ocds.statement.create') as $service) {
                        if (!$service->exec($statement_data, $msg)) {
                            logger::error($msg);
                        }
                    }
                }
            }
            //设置下次结算时间
            app::get('o2ocds')->setConf('statement_time',date('Y-m-d', strtotime('+'.$statement_day.' days',strtotime($current_time))));
        }
        return true;
    }
}
