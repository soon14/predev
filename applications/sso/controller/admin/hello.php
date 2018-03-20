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

class helloworld_ctl_admin_hello extends desktop_controller
{
    public function index()
    {
        /*
         * 构建列表UI
         */
        $this->finder('helloworld_mdl_hello', array(
            'title' => ('问候列表'),
            // 'use_buildin_recycle' => true, #是否启用删除
            // 'use_buildin_set_tag' => true,#是否启用标签
            // 'use_buildin_export' => true, #是否启用批量导出
            // 'use_buildin_import' => true, #是否启用批量导入
            // 'use_buildin_filter' => true, #是否启用筛选
            'actions' => array(//操作按钮
                array(
                    'label' => '添加问候',
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=helloworld&ctl=admin_hello&act=edit',
                ),
            ),
        ));
    }

    public function edit($hello_id)
    {
        if ($hello_id) {
            $mdl_hello = app::get('helloworld')->model('hello');
            $hello = $mdl_hello->dump($hello_id);
            $this->pagedata['hello'] = $hello;
        }
        $this->page('admin/edit.html');

        // $this->singlepage('admin/edit.html');
        // $this->display('admin/edit.html');
    }

    public function save()
    {
        //事务开始 ＃成功后跳转地址定义
        $this->begin('index.php?app=helloworld&ctl=admin_hello&act=index');
        $p_data = $_POST['hello'];
        $mdl_hello = app::get('helloworld')->model('hello');
        $is_save = $mdl_hello->save($p_data);
        if ($is_save) {
            $this->end(true, '保存成功!');//事务提交
        } else {
            $this->end(false, '保存失败!');//事务回滚
        }
    }
}
