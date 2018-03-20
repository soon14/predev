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


class content_openapi_node extends base_openapi
{
    private $req_params = array();

    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }
    /**
     *  @param array $params;
     */
    public function tree($params, $return = false)
    {
        $default_params = array(
            'node_id' => 0,
            'show_article' => false,
            'article_limit' => 10,
            'show_self' => true,
        );
        $params =$params ?$params :array();
        $params = array_merge($default_params, $params ,$this ->req_params);
        $list_map = vmc::singleton('content_article_node')->get_listmaps($params['node_id'], $params['step']);
        $tree = array();
        foreach ($list_map as $key => $value) {
            if ($value['ifpub'] == 'false' || $value['disabled'] == 'true') {
                continue;
            }
            $url_params = array($value['node_id']);
            if($value['homepage']!='true'){
                $url_params[] = 'alist';
            }
            $item = array(
                'node_id' => $value['node_id'],
                'node_path' => $value['node_path'],
                'node_name' => $value['node_name'],
                'link' => app::get('site')
                    ->router()
                    ->gen_url(array('app' => 'content', 'ctl' => 'site_node', 'act' => 'index', 'args' => $url_params)),
            );
            if ($params['show_article']=='true') {
                $articles_list = $this->articles(array('node_id' => $value['node_id'], 'article_limit' => $params['article_limit']), true);
                $item['articles'] = array_values($articles_list);
            }
            $tree[] = $item;
        }
        // foreach ($tree as $node_id => $node) {
        //     $node_path = $node['node_path'];
        //     $node_path_arr = explode(',', $node_path, -1);
        //     if (!empty($node_path_arr)) {
        //         unset($tree[$node_id]);
        //         $eval_str = '$tree['.implode('][', $node_path_arr).'][$node_id]= $node;';
        //         eval($eval_str);
        //     }
        // }

        if ($params['node_id'] > 0 && $params['show_self'] == 'true') {
            $node_self = vmc::singleton('content_article_node')->get_node($params['node_id'], true);
            $url_params = array($params['node_id']);
            if($node_self['homepage']!='true'){
                $url_params[] = 'alist';
            }
            $tree_data = array(
                'node_id' => $params['node_id'],
                'node_path'=> $params['node_path'],
                'node_name' => $node_self['node_name'],
                'link' => app::get('site')
                    ->router()
                    ->gen_url(array('app' => 'content', 'ctl' => 'site_node', 'act' => 'index', 'args' => $url_params)),
            );

            if ($params['show_article']=='true') {
                $articles_list = $this->articles(array('node_id' => $params['node_id'], 'article_limit' => $params['article_limit']), true);
                $tree_data['articles'] = array_values($articles_list);
            }
            array_unshift($tree,$tree_data);
        }
        $tree_data = array_values($tree);
        if ($return) {
            return $tree_data;
        }
        $this->success($tree_data);
    }

    public function articles($params , $_return = false)
    {
        $default_params = array(
            'node_id' => 0,
            'article_limit' => 10,
        );
        $params = $params ? $params :array();
        $params = array_merge($default_params, $params ,$this->req_params );
        $mdl_aindexs = app::get('content')->model('article_indexs');
        $list = $mdl_aindexs->getList('article_id,title', array('node_id|in' => explode("," ,$params['node_id']), 'ifpub|notin' => array('false')), 0, $params['article_limit']);
        if ($list) {
            foreach ($list as $key => $value) {
                $list[$key]['link'] = app::get('site')
                    ->router()
                    ->gen_url(array('app' => 'content', 'ctl' => 'site_article', 'act' => 'index', 'args' => array($value['article_id'])));
            }
        }
        if ($_return) {
            return $list;
        }
        $this->success($list);
    }

    
    public function articles_body($params , $_return = false)
    {
        $default_params = array(
            'node_id' => 0,
            'article_limit' => 10,
        );
        $params = $params ? $params :array();
        if($params['article_id']) {
            unset($default_params['node_id']);
            $filter['article_id|in'] = explode("," ,$params['article_id']);

        }
        $params = array_merge($default_params, $params ,$this->req_params );
        $filter['ifpub|notin'] = array('false');
        if(isset($params['node_id'])) {
            $filter['node_id|in'] = explode("," ,$params['node_id']);
        }
        $mdl_aindexs = app::get('content')->model('article_indexs');
        $mdl_abodys = app::get('content')->model('article_bodys');
        $where_sql = $mdl_aindexs->_filter($filter,'ai');
        $SQL = "SELECT * FROM {$mdl_aindexs->table_name(true)} as ai
                LEFT JOIN {$mdl_abodys->table_name(true)} as ab
                ON ai.article_id = ab.article_id
                WHERE {$where_sql} LIMIT 0,{$params['article_limit']}";
        $list = $mdl_aindexs->db->select($SQL);
        if ($list) {
            foreach ($list as $key => $value) {
                $list[$key]['link'] = app::get('site')
                    ->router()
                    ->gen_url(array('app' => 'content', 'ctl' => 'site_article', 'act' => 'index', 'args' => array($value['article_id'])));
            }
        }
        if ($_return) {
            return $list;
        }
        $this->success($list);
    }

}
