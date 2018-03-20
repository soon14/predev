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

class integraldeduction_ctl_admin_setting extends desktop_controller
{
    public function index()
    {
        include $this->app->app_dir.'/setting.php';
        foreach ($setting as $key => $value) {
            if ($value['desc']) {
                $this->pagedata['setting'][$key] = $value;
                $this->pagedata['setting'][$key]['value'] = $this->app->getConf($key);
            }
        }
        if ($this->has_permission('integraldeduction_setting_base')) {
            $this->pagedata['integraldeduction_setting_base'] = true;
        }

        if ($this->has_permission('integraldeduction_setting_goods')) {
            $this->pagedata['integraldeduction_setting_goods'] = true;
        }

        $this->page('admin/setting.html');
    }

    public function save_setting()
    {
        $this->begin();
        foreach ($_POST as $key => $value) {
            $this->app->setConf($key, $value);
        }
        $this->end(true, '保存成功');
    }

    public function edit_goods($page = 1, $keyword = false)
    {
        $pagelimit = 20;
        $mdl_goods = app::get('b2c')->model('goods');

        if ($keyword && trim($keyword)!='') {
            $goods_range = $mdl_goods->getList('goods_id', array('keyword' => $keyword));
            if (!$goods_range) {
                $goods_range = $mdl_goods->getList('goods_id', array('gid|has' => $keyword));
            }
            $goods_range = $goods_range ? $goods_range : array();
            $goods_range_gids = array_keys(utils::array_change_key($goods_range, 'goods_id'));
            $filter['goods_id'] = $goods_range_gids;
        }

        $mdl_optgoods = $this->app->model('optgoods');
        $optgoods = $mdl_optgoods->getList('*', $filter,$pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_optgoods->count($filter);
        if ($optgoods) {
            $optgoods = utils::array_change_key($optgoods, 'goods_id');
            $goods_ids = array_keys($optgoods);
            $goods_list = $mdl_goods->getList('*', array(
                'goods_id' => $goods_ids,
            ));
        }

        //包装数据
        vmc::singleton('b2c_goods_stage')->gallery($goods_list);
        $this->pagedata['goods_list'] = $goods_list;
        $this->pagedata['optgoods'] = $optgoods;
        $this->pagedata['goods_list_count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=integraldeduction&ctl=admin_setting&act=edit_goods&p[0]='.time().'p[1]='.$keyword.'&in_page=true' ,
            'token' => time(),
        );
        if ($_GET['in_page']) {
            $this->display('/admin/goods_list.html');
        } else {
            $this->display('/admin/edit_goods.html');
        }
    }
    public function update_goods($action = 'save')
    {
        $this->begin('index.php?app=integraldeduction&ctl=admin_setting&act=edit_goods');
        $gids = $_POST['goods_id'];
        $mdl_optgoods = $this->app->model('optgoods');
        if ($action == 'delete' && !isset($gids)) {
            $mdl_optgoods->delete(array(
                'goods_id|than' => 0,
            ));
        } else {
            foreach ($gids as $key => $goods_id) {
                $data = array(
                    'goods_id' => $goods_id,
                );
                if (!$mdl_optgoods->{$action}($data)) {
                    $this->end(false);
                }
            }
        }
        $this->end(true);
    }

    public function update_optgoods(){
        $this->begin();
        $mdl_optgoods = $this->app->model('optgoods');
        $this->end($mdl_optgoods->save($_POST));
    }

    public function update_goods_bycsv($collection_id, $action = 'save')
    {
        $this->begin();
        $csv_file = $_FILES['files']['tmp_name'][0];
        $file = fopen($csv_file, 'r');
        while ($data = fgetcsv($file)) {
            $goods_gid[] = $data[0];
            $scale[$data[0]]=$data[1];
        }
        $goods_gids = app::get('b2c')->model('goods')->getColumn('goods_id', array('gid' => $goods_gid));
        if (!$goods_gids) {
            $this->end(false);
        }
        $mdl_optgoods = $this->app->model('optgoods');
        foreach ($goods_ids as $key => $goods_id) {
            $data = array(
                'goods_id' => $goods_id,
            );
            if($scale && is_array($scale)){
                $data['scale'] = $scale[$goods_id];
            }
            if (!$mdl_optgoods->{$action}($data)) {
                $this->end(false);
            }
        }
        $this->end(true);
    }
}
