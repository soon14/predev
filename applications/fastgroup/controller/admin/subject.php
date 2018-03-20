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

class fastgroup_ctl_admin_subject extends desktop_controller
{
    public function index()
    {
        $this->finder('fastgroup_mdl_subject', array(
            'title' => ('团购活动列表'),
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'actions' => array(
                array(
                    'label' => ('新建团购活动'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=fastgroup&ctl=admin_subject&act=edit',
                ),
            ),
        ));
    }

    public function edit($id)
    {
        if ($id) {
            $mdl_subject = $this->app->model('subject');
            $subject = $mdl_subject->dump($id);
            $this->pagedata['subject'] = $subject;
        }
        $this->page('admin/subject/edit.html');
    }
    public function load_goods($id)
    {
        $goods = vmc::singleton('b2c_goods_stage')->detail('g'.$id);
        $goods['default_image'] = base_storager::image_path($goods['image_default_id']);
        $goods['price'] = $goods['product']['price'];
        $this->splash('success', null, 'success', 'echo', array('goods_info' => $goods));
    }
    public function save()
    {
        $this->begin('index.php?app=fastgroup&ctl=admin_subject&act=index');
        $data = $_POST;

        $data['subject']['begin_time'] = strtotime($data['subject']['begin_time']);
        $data['subject']['end_time'] = strtotime($data['subject']['end_time']);
        if ($data['subject']['end_time'] < time() || $data['subject']['end_time'] < $data['subject']['begin_time']) {
            $this->end(false, '活动结束时间异常');
        }
        $mdl_subject = $this->app->model('subject');
        $subject = $data['subject'];
        if (!$subject['id']) {
            $subject['createtime'] = time();
        }
        if ($mdl_subject->save($subject)) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }
}
