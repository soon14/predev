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


class content_ctl_admin_article extends content_admin_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function index()
    {
        $actions = array();
        if ($this->has_permission('cms_article_edit')) {
            $actions[] = array(
                'label' => '添加文章页',
                'icon' => 'fa fa-plus',
                'href' => 'index.php?app=content&ctl=admin_article&act=add',
            );
        }

        if ($this->has_permission('cms_article_custom_edit')) {
            $actions[] = array(
                'label' => '添加自定义页',
                'icon' => 'fa fa-plus',
                'href' => 'index.php?app=content&ctl=admin_article&act=add&type=2',
            );
        }

        if ($this->has_permission('cms_article_batch')) {
            $actions[] = array(
                'label'=>'批量编辑',
                'group'=>array(
                    array(
                        'label' => 'PC模板页',
                        'data-submit' => 'index.php?app=content&ctl=admin_article&act=batchedit&p[0]=edit&p[1]=template_pc',
                        'data-target' => '_ACTION_MODAL_',
                    ),
                    array(
                        'label' => 'HTML5模板页',
                        'data-submit' => 'index.php?app=content&ctl=admin_article&act=batchedit&p[0]=edit&p[1]=template_h5',
                        'data-target' => '_ACTION_MODAL_',
                    ),
                    array(
                        'label' => '发布状态',
                        'data-submit' => 'index.php?app=content&ctl=admin_article&act=batchedit&p[0]=edit&p[1]=ispub',
                        'data-target' => '_ACTION_MODAL_',
                    ),
                    array(
                        'label' => '所属栏目',
                        'data-submit' => 'index.php?app=content&ctl=admin_article&act=batchedit&p[0]=edit&p[1]=node',
                        'data-target' => '_ACTION_MODAL_',
                    ),
                    array(
                        'label' => '排序',
                        'data-submit' => 'index.php?app=content&ctl=admin_article&act=batchedit&p[0]=edit&p[1]=ordernum',
                        'data-target' => '_ACTION_MODAL_',
                    ),
                ),
            );
        }

        //
        if ($this->has_permission('cms_article_del')) {
            $use_buildin_recycle = true;
        }

        if ($this->has_permission('cms_article_tag')) {
            $use_buildin_set_tag = true;
        }

        $this->finder('content_mdl_article_indexs', array(
            'title' => '内容列表',
            'use_buildin_set_tag' => $use_buildin_set_tag,
            'use_buildin_filter' => true,
            'use_buildin_recycle' => $use_buildin_recycle,
            'actions' => $actions,
        ));
    } //End Function
    public function batchedit($action){
        if($action == 'save'){
            $article_id = json_decode($_POST['filter'],1);
            $param = $_POST['article'];

            $this->begin();
            if($param['indexs']){
                $mdl = app::get('content')->model('article_indexs');
                $this->end($mdl->update($param['indexs'],array('article_id'=>$article_id)));
            }elseif($param['bodys']){
                $mdl = app::get('content')->model('article_bodys');
                $this->end($mdl->update($param['bodys'],array('article_id'=>$article_id)));
            }

        }else{
            $selectmaps = vmc::singleton('content_article_node')->get_selectmaps();
            $this->pagedata['selectmaps'] = $selectmaps;
            $this->pagedata['article_id'] = $_POST['article_id'];
            $this->display('admin/article/batchedit.html');
        }

    }
    public function add()
    {
        $node_id = $this->_request->get_get('node_id');
        $type = $this->_request->get_get('type');
        $article['indexs']['node_id'] = ($node_id > 0) ? $node_id : 0;
        $article['indexs']['type'] = $type;
        if ($type == '2') {
            $article['bodys']['content'] = <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>标题</title>
        <meta name="description" content="内容简介">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    </head>
    <body>
        <h1>标题</h1>
        <div>
            内容
            <!--完全自定义页需要一定的HTML CSS  前端编码基础-->
        </div>
    </body>
</html>
EOF;
        }

        $this->pagedata['article'] = $article;
        $selectmaps = vmc::singleton('content_article_node')->get_selectmaps();
        $this->pagedata['selectmaps'] = $selectmaps;

        if (!$type || $type == '1') {
            $this->page('admin/article/detail/normal.html');
        } elseif ($type == '2') {
            $this->page('admin/article/detail/custom.html');
        }
    } //End Function
    public function edit()
    {
        $this->begin('index.php?app=content&ctl=admin_article&act=index');
        $article_id = $this->_request->get_get('article_id');
        $article['indexs'] = app::get('content')->model('article_indexs')->dump($article_id, '*');
        if (empty($article['indexs'])) {
            $this->end(false, '错误请求');
        }
        $article['bodys'] = vmc::singleton('content_article_detail')->get_body($article_id);
        $this->pagedata['article'] = $article;
        $selectmaps = vmc::singleton('content_article_node')->get_selectmaps();
        $this->pagedata['selectmaps'] = $selectmaps;
        if ($article['indexs']['type'] == '1') {
            $this->page('admin/article/detail/normal.html');
        } elseif ($article['indexs']['type'] == '2') {
            $this->page('admin/article/detail/custom.html');
        }
    } //End Function
    public function save()
    {
        $this->begin('index.php?app=content&ctl=admin_article&act=index');

        $post = $this->_request->get_post('article');
        $article_id = $this->_request->get_post('article_id');
        $post['indexs']['pubtime'] = time();
        if($hot_link = $post['bodys']['hot_link']){
            //关键词处理
            $hot_link = explode("\n",$hot_link);
            foreach ($hot_link as $key => $row) {
                $row = explode("|",$row);
                $hot_link_arr[] = array(
                    'keyword'=>$row[0],
                    'url'=>$row[1],
                );
            }
            $post['bodys']['hot_link'] = $hot_link_arr;
        }
        if ($article_id > 0) {
            $res = app::get('content')->model('article_indexs')->update($post['indexs'], array(
                'article_id' => $article_id,
            ));
            if ($res) {
                $res = app::get('content')->model('article_bodys')->update($post['bodys'], array(
                    'article_id' => $article_id,
                ));
                if ($res) {
                    $services = vmc::serviceList('content_article_index');
                    foreach ($services as $service) {
                        if ($service instanceof content_interface_index) {
                            $service->update($article_id, $post);
                        }
                    }
                    $this->end(true, '保存成功');
                } else {
                    $this->end(false, '保存失败');
                }
            } else {
                $this->end(false, '保存失败');
            }
        } else {
            $res = app::get('content')->model('article_indexs')->insert($post['indexs']);
            if ($res) {
                $post['bodys']['article_id'] = $res;
                $res = app::get('content')->model('article_bodys')->insert($post['bodys']);
                if ($res) {
                    $services = vmc::serviceList('content_article_index');
                    foreach ($services as $service) {
                        if ($service instanceof content_interface_index) {
                            $service->insert($post);
                        }
                    }
                    $this->end(true, '添加成功', null, array(
                        'id' => $post['bodys']['article_id'],
                    ));
                } else {
                    $this->end(false, '添加失败');
                }
            } else {
                $this->end(false, '添加失败');
            }
        }
    } //End Function
    public function updatetime()
    {
        $article_id = $this->_request->get_get('article_id');
        if ($article_id > 0) {
            app::get('content')->model('article_indexs')->update_time(array(
                'article_id' => $article_id,
            ));
        }
    } //End Function
} //End Class
