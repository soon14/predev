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


class content_ctl_site_node extends site_controller
{
    public function index($node_id,$page = 1)
    {
        $id = $node_id;
        if ($id < 1) {
            vmc::singleton('site_router')->http_status(404); //exit;
        }
        $node = vmc::singleton('content_article_node');
        $info = $node->get_node($id, true);
        if ($info['ifpub'] != 'true') {
            $this->splash('error', null, $info['node_name'].'未发布');
        }
        $runtime_path = $node->get_node_path($id, true);
        $this->pagedata['path'] = $runtime_path;
        $this->pagedata['node_id'] = $node_id;
        $this->get_seo_info($info, $runtime_path);
        $this->set_tmpl('node');
        $this->set_tmpl_file($info['setting']['site_template']);
        if($info['homepage'] == 'false'){
            $type = 'alist';
        }else{
            $type = 'nodepage';
        }
        switch ($type) {
            case 'nodepage':
                $this->page('content_node:'.$info['node_id']);
                break;
            case 'alist'://文章列表
                $mdl_aindex = app::get('content')->model('article_indexs');
                $filter = array('node_id' => $node_id ,'ifpub'=>'true');
                $limit = 20;
                $list = $mdl_aindex->getList('*', $filter, ($page - 1) * $limit, $limit);
                $count = $mdl_aindex->count($filter);
                foreach ($list as $key => &$value) {
                    $value['body'] = vmc::singleton('content_article_detail')->get_body($value['article_id'], true);
                }
                $this->pagedata['alist'] = $list;
                $this->pagedata['alist_pager'] = array(
                    'total' => ceil($count / $limit) ,
                    'current' => $page,
                    'link' => array(
                        'app' => 'content',
                        'ctl' => 'site_node',
                        'act' => 'index',
                        'args' => array(
                            $node_id,
                            ($token = time()),
                        ) ,
                    ) ,
                    'token' => $token,
                );
                $this->page('site/article/list.html');
                break;

        }
    }

    private function get_seo_info($aInfo, $aPath)
    {
        is_array($info) or $info = array();
        is_array($aPath) or $aPath = array();
        //title keywords description
        $title = array();
        $title[] = $aInfo['seo_title'] ? $aInfo['seo_title'] : $aPath[count($aPath) - 1]['title'];
        if (!$aInfo['seo_title']) {
            $title[] = $this->site_name ? $this->site_name : app::get('site')->getConf('site_name');
        }
        $title = array_filter($title);
        $this->title = implode('-', $title);
        $this->description = $aInfo['seo_description'] ? $aInfo['seo_description'] : $this->pagedata['title'];
        if ($aInfo['seo_keywords']) {
            $this->keywords = $aInfo['seo_keywords'];
        } else {
            $keyword = array();
            foreach ($aPath as $row) {
                $keyword[] = $row['title'];
            }
            $this->keywords = implode(',', $keyword);
        }
    }
}
