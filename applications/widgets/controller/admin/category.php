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
class widgets_ctl_admin_category extends desktop_controller
{
    public function __construct (&$app)
    {
        $this->app = $app;
        $this->category = vmc::singleton ('widgets_category');
        parent::__construct ($app);
    }

    public function index ($parent_id = 0)
    {
        $this->pagedata['list'] = $this->app->model ('widgets_category')->getList ('*',array('parent_id' => $parent_id));
        $this->pagedata['path'] = $this->category->get_parent_path ($parent_id);
        $this->page ('admin/category/index.html');
    }

    public function edit ()
    {
        if ($_GET['cid']) {
            $this->pagedata['cat'] = $this->app->model ('widgets_category')->dump ($_GET['cid']);
        }
        if ($_GET['parent_id']) {
            $this->pagedata['cat']['parent_id'] = $_GET['parent_id'];
        }
        $selectmaps = $this->category->get_selectmaps ();

        array_unshift ($selectmaps, array(
            'cid' => 0,
            'parent_id' => 0,
            'title' => '---无---',
        ));
        $this->pagedata['selectmaps'] = $selectmaps;
        $this->page ('admin/category/edit.html');
    }

    public function save ()
    {
        $_POST['cat']['parent_id'] = $_POST['cat']['parent_id'] ? $_POST['cat']['parent_id'] : 0;
        if (!$_POST['cat']['cid'] && $this->app->model ('widgets_category')->count (array(
                'category_key' => trim($_POST['cat']['category_key']),
                'parent_id' => $_POST['cat']['parent_id']
            ))
        ){
            $this->splash ('error', '', '该英文标识已被占用');
        }
        $this->begin ('index.php?app=widgets&ctl=admin_category&act=index&p[0]=' . $_POST['cat']['parent_id']);
        $_POST['cat']['category_key'] = trim($_POST['cat']['category_key']);
        if ($this->app->model ('widgets_category')->save ($_POST['cat'])) {
            $this->end (true, '保存成功');
        }
        $this->end (false, '保存失败');
    }

    public function remove ()
    {
        $cid = $_GET['cid'];
        $mdl_catgory = $this->app->model ('widgets_category');
        $catgory = $mdl_catgory->getRow ('*', array('cid' => $cid));
        if (!$catgory) {
            $this->splash ('error', '', '参数错误');
        }
        $this->begin ('index.php?app=widgets&ctl=admin_category&act=index&p[0]=' . $catgory['parent_id']);
        if ($mdl_catgory->count (array('parent_id' => $cid))) {
            $this->end (false, '该分类下还有子分类，无法删除');
        }
        if ($this->app->model ('widgets')->count (array('cid' => $cid))) {
            $this->end (false, '该分类下还有板块，无法删除');
        }
        if ($mdl_catgory->delete (array('cid' => $cid))) {
            $this->end (true, '删除成功');
        }
        $this->end (false, '删除失败');
    }
}
