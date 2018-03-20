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

class supplier_ctl_admin_statement extends desktop_controller
{
    public function index()
    {
        $this->finder('supplier_mdl_statement', array(
            'title' => ('财务结算单'),
            'use_buildin_recycle' => $this ->has_permission('supplier_statement_delete'),
            //'use_buildin_export'=>true,
            'use_buildin_set_tag' => $this ->has_permission('supplier_statement_tag'),
            'use_buildin_filter' => true,
        ));
    }

    public function edit($statement_id)
    {
        $mdl_supplier = $this->app->model('supplier');
        $mdl_voucher = $this->app->model('voucher');
        $mdl_voucher_items = $this->app->model('voucher_items');
        $mdl_statement = $this->app->model('statement');
        $statement = $mdl_statement->dump($statement_id, '*', 'statement_index');
        $voucher_id_arr = array_keys(utils::array_change_key($statement['statement_index'], 'voucher_id'));
        $voucher_list = array();
        foreach ($voucher_id_arr as $voucher_id) {
            $voucher_list[$voucher_id] = $mdl_voucher->dump($voucher_id, '*', 'items');
        }
        $this->pagedata['voucher_list'] = $voucher_list;
        $this->pagedata['statement'] = $statement;
        $this->pagedata['supplier'] = $mdl_supplier->dump($statement['supplier_id']);
        $this->page('admin/statement/edit.html');
    }
    public function update($statement_id)
    {
        $this->begin('index.php?app=supplier&ctl=admin_statement&act=index');
        $statement_data = $_POST['statement'];
        $mdl_statement = $this->app->model('statement');
        $mdl_voucher = $this->app->model('voucher');
        $statement = $mdl_statement->dump($statement_id, '*', 'statement_index');
        if ($statement['status'] == 'succ') {
            unset($statement_data['status']);
        }
        if ($statement_data['status'] == 'succ') {
            foreach ($statement['statement_index'] as $index) {
                $update_voucher = array(
                    'voucher_id' => $index['voucher_id'],
                    'status' => 'succ',
                );
                if (!$mdl_voucher->save($update_voucher)) {
                    $this->end(false, '更新结算凭证状态失败!');
                } else {
                    $update_voucher['statement_id'] = $index['statement_id'];
                    foreach (vmc::servicelist('supplier.voucher.update') as $service) {
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
        if ($mdl_statement->save($statement_data)) {
            foreach (vmc::servicelist('supplier.statement.update') as $service) {
                if (!$service->exec($statement_data, $msg)) {
                    logger::error($msg);
                    $this->end(false, '结算单更新失败!'.$msg);
                }
            }
            $this->end(true);
        }
        $this->end(false);
    }

    public function create()
    {
        $voucher_id_arr = $_POST['voucher_id'];
        $mdl_voucher = $this->app->model('voucher');
        $voucher_arr = $mdl_voucher->getList('*', array('voucher_id' => $voucher_id_arr, 'status' => 'ready'));
        $voucher_id_arr = array_keys(utils::array_change_key($voucher_arr, 'voucher_id'));
        if (!$voucher_id_arr) {
            die('没有选择结算凭证或没有待结算状态的凭证!');
        }
        $this->pagedata['voucher_id_serialize'] = serialize($voucher_id_arr);
        $this->pagedata['voucher_id_arr'] = $voucher_id_arr;
        $supplier_id_arr = array_keys(utils::array_change_key($voucher_arr, 'supplier_id'));
        if (!empty($supplier_id_arr[1])) {
            $this->pagedata['multiple_supplier'] = 'true';
        }
        $this->display('admin/statement/create.html');
    }

    public function docreate()
    {
        $this->begin();
        $voucher_id_serialize = $_POST['voucher_id_serialize'];
        $voucher_id_arr = unserialize($voucher_id_serialize);
        $mdl_voucher = $this->app->model('voucher');
        $mdl_voucher_items = $this->app->model('voucher_items');
        $voucher_arr = $mdl_voucher->getList('*', array('voucher_id' => $voucher_id_arr, 'status' => 'ready'));
        if (!$voucher_arr) {
            $this->end();
        }
        $mdl_statement = $this->app->model('statement');
        $statement_data = $_POST['statement'];
        $statement_data['statement_id'] = $mdl_statement->apply_id();
        $statement_data['supplier_id'] = $voucher_arr[0]['supplier_id'];
        $statement_data['supplier_bn'] = $voucher_arr[0]['supplier_bn'];
        $statement_data['op_id'] = $this->user->user_id;
        $statement_data['createtime'] = time();
        $ready_voucher_id_arr = array_keys(utils::array_change_key($voucher_arr, 'voucher_id'));
        $s_subprice_arr = $mdl_voucher_items->getColumn('s_subprice', array('voucher_id' => $ready_voucher_id_arr));
        $statement_data['money'] = vmc::singleton('ectools_math')->number_plus($s_subprice_arr);
        if ($statement_data['status'] == 'process') {
            $new_voucher_status = 'process';
        }
        if ($statement_data['status'] == 'succ') {
            $new_voucher_status = 'succ';
        }
        foreach ($voucher_arr as $voucher) {
            $statement_data['statement_index'][] = array(
                'statement_id' => $statement_data['statement_id'],
                'voucher_id' => $voucher['voucher_id'],
            );
            $update_voucher = $voucher;
            $update_voucher['status'] = $new_voucher_status;
            if (!$mdl_voucher->save($update_voucher)) {
                $this->end(false, '更新结算凭证状态失败!'.$update_voucher['voucher_id']);
            } else {
                foreach (vmc::servicelist('supplier.voucher.update') as $service) {
                    if (!$service->exec($update_voucher, $msg)) {
                        logger::error($msg);
                        $this->end(false, '更新结算凭证状态失败!'.$msg);
                    }
                }
            }
        }

        if (!$mdl_statement->save($statement_data)) {
            $this->end(false, '结算单保存失败!');
        } else {
            foreach (vmc::servicelist('supplier.statement.create') as $service) {
                if (!$service->exec($statement_data, $msg)) {
                    logger::error($msg);
                    $this->end(false, '结算单保存失败!'.$msg);
                }
            }
        }
        $this->end(true);
    }

    public function docreate_multiple()
    {
        $this->begin();
        $voucher_id_serialize = $_POST['voucher_id_serialize'];
        $voucher_id_arr = unserialize($voucher_id_serialize);
        $mdl_voucher = $this->app->model('voucher');
        $mdl_voucher_items = $this->app->model('voucher_items');
        $voucher_arr = $mdl_voucher->getList('*', array('voucher_id' => $voucher_id_arr, 'status' => 'ready'));
        if (!$voucher_arr) {
            $this->end();
        }
        $voucher_arr_supplier_group = utils::array_change_key($voucher_arr, 'supplier_id', true);
        $mdl_statement = $this->app->model('statement');
        foreach ($voucher_arr_supplier_group as $supplier_id => $voucher_arr) {
            $statement_data = $_POST['statement'] ? $_POST['statement'] : array();
            $statement_data['statement_id'] = $mdl_statement->apply_id();
            $statement_data['supplier_id'] = $voucher_arr[0]['supplier_id'];
            $statement_data['supplier_bn'] = $voucher_arr[0]['supplier_bn'];
            $statement_data['op_id'] = $this->user->user_id;
            $statement_data['createtime'] = time();
            $ready_voucher_id_arr = array_keys(utils::array_change_key($voucher_arr, 'voucher_id'));
            $s_subprice_arr = $mdl_voucher_items->getColumn('s_subprice', array('voucher_id' => $ready_voucher_id_arr));
            $statement_data['money'] = vmc::singleton('ectools_math')->number_plus($s_subprice_arr);
            if ($statement_data['status'] == 'process') {
                $new_voucher_status = 'process';
            } elseif ($statement_data['status'] == 'succ') {
                $new_voucher_status = 'succ';
            } else {
                $new_voucher_status = 'process';
            }
            foreach ($voucher_arr as $voucher) {
                $statement_data['statement_index'][] = array(
                            'statement_id' => $statement_data['statement_id'],
                            'voucher_id' => $voucher['voucher_id'],
                        );
                $update_voucher = $voucher;
                $update_voucher['status'] = $new_voucher_status;
                if (!$mdl_voucher->save($update_voucher)) {
                    $this->end(false, '更新结算凭证状态失败!'.$update_voucher['voucher_id']);
                } else {
                    foreach (vmc::servicelist('supplier.voucher.update') as $service) {
                        if (!$service->exec($update_voucher, $msg)) {
                            logger::error($msg);
                            $this->end(false, '更新结算凭证状态失败!'.$msg);
                        }
                    }
                }
            }

            if (!$mdl_statement->save($statement_data)) {
                $this->end(false, '结算单保存失败!');
            } else {
                foreach (vmc::servicelist('supplier.statement.create') as $service) {
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
