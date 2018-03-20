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

class integralmall_ctl_admin_setting extends desktop_controller
{

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

        $mdl_relgoods = $this->app->model('relgoods');
        $relgoods = $mdl_relgoods->getList('*', $filter,$pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_relgoods->count($filter);
        if ($relgoods) {
            $relgoods = utils::array_change_key($relgoods, 'goods_id');
            $goods_ids = array_keys($relgoods);
            $goods_list = $mdl_goods->getList('*', array(
                'goods_id' => $goods_ids,
            ));
        }

        //包装数据
        vmc::singleton('b2c_goods_stage')->gallery($goods_list);
        $this->pagedata['goods_list'] = $goods_list;
        $this->pagedata['relgoods'] = $relgoods;
        $this->pagedata['goods_list_count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=integralmall&ctl=admin_setting&act=edit_goods&p[0]='.time().'p[1]='.$keyword.'&in_page=true' ,
            'token' => time(),
        );
        if ($_GET['in_page']) {
            $this->display('/admin/goods_list.html');
        } else {
            $this->page('/admin/edit_goods.html');
        }
    }
    public function update_goods($action = 'save')
    {
        $this->begin('index.php?app=integralmall&ctl=admin_setting&act=edit_goods');
        $gids = $_POST['goods_id'];
        $mdl_relgoods = $this->app->model('relgoods');
        if ($action == 'delete' && !isset($gids)) {
            $mdl_relgoods->delete(array(
                'goods_id|than' => 0,
            ));
        } else {
            foreach ($gids as $key => $goods_id) {
                $data = array(
                    'goods_id' => $goods_id
                );
                if (!$mdl_relgoods->{$action}($data)) {
                    $this->end(false);
                }
            }
        }
        $this->end(true);
    }

    public function update_relgoods(){
        $this->begin();
        $mdl_relgoods = $this->app->model('relgoods');
        $this->end($mdl_relgoods->save($_POST));
    }

    public function update_goods_bycsv($collection_id, $action = 'save')
    {
        $this->begin();
        $csv_file = $_FILES['files']['tmp_name'][0];
        $file = fopen($csv_file, 'r');
        while ($data = fgetcsv($file)) {
            $goods_gid[] = $data[0];
            $deduction[$data[0]]=$data[1];
        }
        $goods_gids = app::get('b2c')->model('goods')->getColumn('goods_id', array('gid' => $goods_gid));
        if (!$goods_gids) {
            $this->end(false);
        }
        $mdl_relgoods = $this->app->model('relgoods');
        foreach ($goods_ids as $key => $goods_id) {
            $data = array(
                'goods_id' => $goods_id,
                'deduction'=>999999
            );
            if($deduction && is_array($deduction)){
                $data['deduction'] = $deduction[$goods_id];
            }
            if (!$mdl_relgoods->{$action}($data)) {
                $this->end(false);
            }
        }
        $this->end(true);
    }
}
