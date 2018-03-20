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

class adjustprices_ctl_admin_plan extends desktop_controller
{
    public function index()
    {
        if ($this->has_permission('adjustprices_edit')) {
            $actions = array(
                array(
                    'label' => ('新建降价活动'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=adjustprices&ctl=admin_plan&act=edit',
                ),
            );
        }

        if ($this->has_permission('adjustprices_del')) {
            $use_buildin_recycle = true;
        }

        $this->finder('adjustprices_mdl_plan', array(
            'title' => ('限时降价活动'),
            'use_buildin_recycle' => $use_buildin_recycle,
            'actions' => $actions,
        ));
    }
    public function edit($plan_id)
    {
        $mdl_plan = $this->app->model('plan');
        $this->pagedata['plan'] = $mdl_plan->dump($plan_id);
        $this->page('admin/plan/edit.html');
    }

    public function save()
    {
        $this->begin('index.php?app=adjustprices&ctl=admin_plan&act=index');
        $mdl_plan = $this->app->model('plan');
        $data = $_POST;

        if (empty($data['plan_id'])) {
            $plan_name = $mdl_plan->getRow('plan_id', array(
                'plan_name' => $data['plan_name'],
            ));
            if (is_array($plan_name)) {
                $this->end(false, ('名称重复'));
            }
            $data['createtime'] = time();
        }

        if ($data['carry_out_time'] == '') {
            $data['carry_out_time'] = null;
        } else {
            $data['carry_out_time'] = strtotime($data['carry_out_time']);
        }
        // if ($data['carry_out_time'] < time() + 300) {
        //     $this->end(false, '计划执行时间应大于当前时间');
        // }
        if ($data['rollback_time'] == '') {
            $data['rollback_time'] = null;
        } else {
            $data['rollback_time'] = strtotime($data['rollback_time']);
        }
        if ($data['rollback_time'] && $data['rollback_time'] < $data['carry_out_time']) {
            $this->end(false, '回滚时间不能小于计划调价时间');
        }
        if (isset($data['plan_status']) && !$this->has_permission('adjustprices_status_edit')) {
            $this->end(false, '无权修改状态');
        }
        if ($mdl_plan->save($data)) {
            $this->end(true, ('保存成功'));
        } else {
            $this->end(false, ('保存失败'));
        }
    }

    public function edit_products($plan_id, $page = 1, $keyword = false)
    {
        $pagelimit = 20;
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_products = app::get('b2c')->model('products');

        $product_filter = array('plan_id' => $plan_id);
        if ($keyword) {
            $goods_range = $mdl_goods->getList('goods_id', array('keyword' => $keyword));
            if (!$goods_range) {
                $goods_range = $mdl_goods->getList('goods_id', array('gid|has' => $keyword));
            }
            if (!$goods_range) {
                $goods_range = $mdl_goods->getList('goods_id', array('product_filter' => array('bn|has' => $keyword)));
            }
            $goods_range = $goods_range ? $goods_range : array();
            $goods_range_gids = array_keys(utils::array_change_key($goods_range, 'goods_id'));
            $product_filter['goods_id'] = $goods_range_gids;
        }

        $mdl_plan = $this->app->model('plan');
        $this->pagedata['plan'] = $mdl_plan->dump($plan_id);

        $mdl_plan_job = $this->app->model('job');
        $job_items = $mdl_plan_job->getList('*', $product_filter, $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_plan_job->count($product_filter);

        $product_ids = array_keys(utils::array_change_key($job_items, 'product_id'));
        $products = $mdl_products->getList('*', array('product_id' => $product_ids));

        $this->pagedata['job_items'] = utils::array_change_key($job_items, 'goods_id', true);
        $this->pagedata['products'] = utils::array_change_key($products, 'product_id');
        $this->pagedata['items_count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=adjustprices&ctl=admin_plan&act=edit_products&p[0]='.$plan_id.'&p[1]='.time().'&in_page=true' ,
            'token' => time(),
        );
        if ($_GET['in_page']) {
            $this->display('/admin/plan/job_items.html');
        } else {
            $this->page('/admin/plan/edit_job.html');
        }
    }
    public function update_plan_products($plan_id, $action = 'save')
    {
        $this->begin('index.php?app=adjustprices&ctl=admin_plan&act=edit_products&p[0]='.$plan_id);
        if (!$plan_id) {
            $this->end(false);
        }
        $product_ids = $_POST['product_id'];
        $products = app::get('b2c')->model('products')->getList('*', array('product_id' => $product_ids));

        $mdl_plan_job = $this->app->model('job');
        $mdl_plan = $this->app->model('plan');
        if ($action == 'delete' && !is_array($products)) {
            $mdl_plan_job->delete(array(
                'plan_id' => $plan_id,
            ));
        } else {
            if (!$products) {
                $this->end(false);
            }
            foreach ($products as $key => $product) {
                $data = array(
                    'plan_id' => $plan_id,
                    'goods_id' => $product['goods_id'],
                    'product_id' => $product['product_id'],
                    'begin_price' => $product['price'],
                    'end_price' => $product['price'],
                );
                if ($action == 'delete') {
                    unset($data['begin_price']);
                    unset($data['end_price']);
                }
                if (!$mdl_plan_job->{$action}($data)) {
                    $this->end(false);
                }
            }
        }
        $this->end($mdl_plan->update(array('product_count' => $mdl_plan_job->count(array('plan_id' => $plan_id))), array('plan_id' => $plan_id)));
    }

    public function update_plan_products_bycsv($plan_id)
    {
        $this->begin();
        $csv_file = $_FILES['files']['tmp_name'][0];
        $file = fopen($csv_file, 'r');
        while ($data = fgetcsv($file)) {
            $row = array(
                'bn' => $data[0],
                'price' => $data[1],
            );
            $import_data[$row['bn']] = $row;
            $bn_arr[] = $data[0];
        }
        $products = app::get('b2c')->model('products')->getList('*', array('bn' => $bn_arr));
        if (!$products) {
            $this->end(false);
        }
        $mdl_plan_job = $this->app->model('job');
        $mdl_plan = $this->app->model('plan');
        foreach ($products as $key => $product) {
            $data = array(
                'plan_id' => $plan_id,
                'goods_id' => $product['goods_id'],
                'product_id' => $product['product_id'],
                'begin_price' => $product['price'],
                'end_price' => $import_data[$product['bn']]['price'],
            );
            if (!$mdl_plan_job->save($data)) {
                $this->end(false);
            }
        }
        $this->end($mdl_plan->update(array('product_count' => $mdl_plan_job->count(array('plan_id' => $plan_id))), array('plan_id' => $plan_id)));
    }

    public function update_job_item()
    {
        $this->begin();
        $mdl_plan_job = $this->app->model('job');
        $this->end($mdl_plan_job->save($_POST));
    }
}
