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

class o2ocds_ctl_admin_statement extends desktop_controller
{
    public function index()
    {
        $this->finder('o2ocds_mdl_statement', array(
            'title' => ('财务结算单'),
            'use_buildin_recycle' => $this ->has_permission('o2ocds_statement_delete'),
            'use_buildin_export'=>$this ->has_permission('o2ocds_statement_export'),
            'use_buildin_set_tag' => $this ->has_permission('o2ocds_statement_tag'),
            'use_buildin_filter' => true,
        ));
    }

    public function edit($statement_id)
    {
        $mdl_statement = $this->app->model('statement');
        $statement = $mdl_statement->dump($statement_id, '*', 'statement_index');
        $achieve_id_arr = array_keys(utils::array_change_key($statement['statement_index'], 'achieve_id'));
        $orderlog_achieve = $this->app->model('orderlog_achieve')->getList('*', array('achieve_id' => $achieve_id_arr));
        foreach($orderlog_achieve as &$v) {
            $orderlog_id  = $v['orderlog_id'];
            $orderlog_items = $this->app->model('orderlog_items')->getList('*', array('orderlog_id' => $orderlog_id));
            $v['items'] = $orderlog_items;
        }
        $this->pagedata['achieve_list'] = $orderlog_achieve;
        $this->pagedata['statement'] = $statement;
        //查询分佣者的信息
        if($statement['relation_type']) {
            $filter = array();
            $filter[$statement['relation_type']] = $statement['relation_id'];

            $o2ocds_info = $this->app->model($statement['relation_type'])->dump($statement['relation_id']);
            $this->pagedata['o2ocds'] = $o2ocds_info;
        }
        $this->page('admin/statement/edit.html');
    }
    public function update($statement_id)
    {
        $this->begin('index.php?app=o2ocds&ctl=admin_statement&act=index');
        $statement_data = $_POST['statement'];
        $mdl_statement = $this->app->model('statement');
        $mdl_orderlog_achieve = $this->app->model('orderlog_achieve');
        $statement = $mdl_statement->dump($statement_id, '*', 'statement_index');
        if ($statement['status'] == 'succ') {
            unset($statement_data['status']);
        }
        if ($statement_data['status'] == 'succ') {
            foreach ($statement['statement_index'] as $index) {
                $update_voucher = array(
                    'achieve_id' => $index['achieve_id'],
                    'status' => 'succ',
                );
                if (!$mdl_orderlog_achieve->save($update_voucher)) {
                    $this->end(false, '更新结算凭证状态失败!');
                } else {
                    $update_voucher['statement_id'] = $index['statement_id'];
                    foreach (vmc::servicelist('o2ocds.orderlog_achieve.update') as $service) {
                        if (!$service->exec($update_voucher, $msg)) {
                            logger::error($msg);
                            $this->end(false, '更新结算凭证状态失败!'.$msg);
                        }
                    }
                }
            }
        }
        $op_name = $this->user->get_name();
        $memo_pad_left = '['.date('Y-m-d H:i:s').']'.$op_name.'：';
        $statement_data['memo'] = $statement_data['memo'] ? $statement_data['memo'] : '更新操作';
        $statement_data['memo'] = $statement['memo'].'<br>'.$memo_pad_left.$statement_data['memo'];
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'statement_log')) {
            $obj_log->statement_log($statement_data,'update');
        }
        if ($mdl_statement->save($statement_data)) {
            foreach (vmc::servicelist('o2ocds.statement.update') as $service) {
                if (!$service->exec($statement_data, $msg)) {
                    logger::error($msg);
                    $this->end(false, '结算单更新失败!'.$msg);
                }
            }
            $this->end(true);
        }
        $this->end(false);
    }

    /*
     * 生产结算单页面显示
     * */
    public function create()
    {
        $achieve_id_arr = $_POST['achieve_id'];
        $mdl_orderlog_achieve = $this->app->model('orderlog_achieve');
        $orderlog_achieve_arr = $mdl_orderlog_achieve->getList('*', array('achieve_id' => $achieve_id_arr, 'status' => 'ready'));
        $achieve_id_arr = array_keys(utils::array_change_key($orderlog_achieve_arr, 'achieve_id'));
        if (!$achieve_id_arr) {
            die('没有选择结算凭证或没有待结算状态的凭证!');
        }
        $this->pagedata['achieve_id_serialize'] = serialize($achieve_id_arr);
        $this->pagedata['achieve_id_arr'] = $achieve_id_arr;
        $type_arr = array();
        $relation_arr = array();
        foreach($orderlog_achieve_arr as $v) {
            $relation_arr[$v['relation_id']][] = $v;
            $type_arr[$v['type']][] = $v;
        }
        if (count($relation_arr) >1 || count($type_arr) > 1) {
            $this->pagedata['multiple_supplier'] = 'true';
        }else{
            //查询分佣者的信息
            if($orderlog_achieve_arr[0]['type']) {
                $o2ocds_info = $this->app->model($orderlog_achieve_arr[0]['type'])->getRow('*',array('relation_id'=>$orderlog_achieve_arr[0]['relation_id']));
                $this->pagedata['o2ocds'] = $o2ocds_info;
            }
        }
        $this->display('admin/statement/create.html');
    }

    /*
     * 单会员进行生产结算单
     * */
    public function docreate()
    {
        $this->begin();
        $achieve_id_serialize = $_POST['achieve_id_serialize'];
        $achieve_id_arr = unserialize($achieve_id_serialize);
        $mdl_orderlog_achieve = $this->app->model('orderlog_achieve');
        $orderlog_achieve_arr = $mdl_orderlog_achieve->getList('achieve_id,relation_id,type,achieve_fund', array('achieve_id' => $achieve_id_arr, 'status' => 'ready'));
        if (!$orderlog_achieve_arr) {
            $this->end();
        }
        $mdl_statement = $this->app->model('statement');
        $statement_data = $_POST['statement'];
        $statement_data['statement_id'] = $mdl_statement->apply_id();
        $statement_data['relation_id'] = $orderlog_achieve_arr[0]['relation_id'];
        $statement_data['relation_type'] = $orderlog_achieve_arr[0]['type'];
        $statement_data['op_id'] = $this->user->user_id;
        $statement_data['createtime'] = time();
        if ($statement_data['status'] == 'process') {
            $new_orderlog_achieve_status = 'process';
        }elseif ($statement_data['status'] == 'succ') {
            $new_orderlog_achieve_status = 'succ';
        } else {
            $new_orderlog_achieve_status = 'process';
        }
        foreach ($orderlog_achieve_arr as $orderlog_achieve) {
            $statement_data['money'] += $orderlog_achieve['achieve_fund'];
            $statement_data['statement_index'][] = array(
                'statement_id' => $statement_data['statement_id'],
                'achieve_id' => $orderlog_achieve['achieve_id'],
            );
            $update_orderlog_achieve = $orderlog_achieve;
            $update_orderlog_achieve['status'] = $new_orderlog_achieve_status;
            if (!$mdl_orderlog_achieve->save($update_orderlog_achieve)) {
                $this->end(false, '更新结算凭证状态失败!'.$update_orderlog_achieve['achieve_id']);
            } else {
                foreach (vmc::servicelist('o2ocds.orderlog_achieve.update') as $service) {
                    if (!$service->exec($update_orderlog_achieve, $msg)) {
                        logger::error($msg);
                        $this->end(false, '更新结算凭证状态失败!'.$msg);
                    }
                }
            }
        }
        $mdl_statement->complete_bank($statement_data); //引用传递
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'statement_log')) {
            $obj_log->statement_log($statement_data,'add');
        }
        if (!$mdl_statement->save($statement_data)) {
            $this->end(false, '结算单保存失败!');
        } else {
            foreach (vmc::servicelist('o2ocds.statement.create') as $service) {
                if (!$service->exec($statement_data, $msg)) {
                    logger::error($msg);
                    $this->end(false, '结算单保存失败!'.$msg);
                }
            }
        }
        $this->end(true);
    }

    /*
     * 多个会员进行生产结算单
     * */
    public function docreate_multiple()
    {
        $this->begin();
        $achieve_id_serialize = $_POST['achieve_id_serialize'];
        $achieve_id_arr = unserialize($achieve_id_serialize);
        $mdl_orderlog_achieve = $this->app->model('orderlog_achieve');
        $orderlog_achieve_arr = $mdl_orderlog_achieve->getList('achieve_id,relation_id,type,achieve_fund', array('achieve_id' => $achieve_id_arr, 'status' => 'ready'));
        if (!$orderlog_achieve_arr) {
            $this->end();
        }

        foreach($orderlog_achieve_arr as $v) {
            $orderlog_achieve_arr_supplier_group[$v['type'].'_'.$v['relation_id']][] = $v;
        }
        $mdl_statement = $this->app->model('statement');
        foreach ($orderlog_achieve_arr_supplier_group as $orderlog_achieve_arr) {
            $statement_data = $_POST['statement'] ? $_POST['statement'] : array();
            $statement_data['statement_id'] = $mdl_statement->apply_id();
            $statement_data['relation_id'] = $orderlog_achieve_arr[0]['relation_id'];
            $statement_data['relation_type'] = $orderlog_achieve_arr[0]['type'];
            $statement_data['op_id'] = $this->user->user_id;
            $statement_data['createtime'] = time();

            if ($statement_data['status'] == 'process') {
                $new_orderlog_achieve_status = 'process';
            } elseif ($statement_data['status'] == 'succ') {
                $new_orderlog_achieve_status = 'succ';
            } else {
                $new_orderlog_achieve_status = 'process';
            }
            foreach ($orderlog_achieve_arr as $orderlog_achieve) {
                $statement_data['money'] += $orderlog_achieve['achieve_fund'];
                $statement_data['statement_index'][] = array(
                    'statement_id' => $statement_data['statement_id'],
                    'achieve_id' => $orderlog_achieve['achieve_id'],
                );
                $update_orderlog_achieve = $orderlog_achieve;
                $update_orderlog_achieve['status'] = $new_orderlog_achieve_status;
                if (!$mdl_orderlog_achieve->save($update_orderlog_achieve)) {
                    $this->end(false, '更新结算凭证状态失败!'.$update_orderlog_achieve['achieve_id']);
                } else {
                    foreach (vmc::servicelist('o2ocds.orderlog_achieve.update') as $service) {
                        if (!$service->exec($update_orderlog_achieve, $msg)) {
                            logger::error($msg);
                            $this->end(false, '更新结算凭证状态失败!'.$msg);
                        }
                    }
                }
            }
            $mdl_statement->complete_bank($statement_data); //引用传递
            //操作日志
            $obj_log = vmc::singleton('o2ocds_operatorlog');
            if(method_exists($obj_log,'statement_log')) {
                $obj_log->statement_log($statement_data,'add');
            }
            if (!$mdl_statement->save($statement_data)) {
                $this->end(false, '结算单保存失败!');
            } else {
                foreach (vmc::servicelist('o2ocds.statement.create') as $service) {
                    if (!$service->exec($statement_data, $msg)) {
                        logger::error($msg);
                        $this->end(false, '结算单保存失败!'.$msg);
                    }
                }
            }
        }
        $this->end(true);
    }
}
