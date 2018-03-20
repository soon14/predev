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


class content_ctl_site_article extends site_controller
{
    public function index()
    {
        $article_id = $this->_request->get_param(0);
        if ($article_id < 1) {
            vmc::singleton('site_router')->http_status(404); //exit;
        }
        $detail = vmc::singleton('content_article_detail')->get_detail($article_id, true);
        if ($detail['indexs']['ifpub'] != 'true') {
            vmc::singleton('site_router')->http_status(404); //exit;
        }
        $node = vmc::singleton('content_article_node');
        $node_info = $node->get_node($detail['indexs']['node_id'], true);
        if ($node_info['ifpub'] != 'true') {
            vmc::singleton('site_router')->http_status(404); //exit;
        }
        $runtime_path = $node->get_node_path($detail['indexs']['node_id'], true);
        $runtime_path[] = array(
            'link' => app::get('site')->router()->gen_url(array(
                'app' => 'content',
                'ctl' => 'site_article',
                'act' => 'index',
                'args' => array($article_id),
            )) ,
            'title' => $detail['indexs']['title'],
        );
        $this->pagedata['path'] = $runtime_path;
        $this->get_seo_info($detail['bodys'], $runtime_path);

        switch ($detail['indexs']['type']) {
            case 1:
                $this->_index1($detail);
            break;
            case 2:
            default:
                $this->_index2($detail);
        } //End Switch
    } //End Function
    private function _index1($detail)
    {
        $detail['bodys']['content'] = vmc::singleton('content_article_detail')->parse_hot_link($detail['bodys']);
        $this->pagedata['detail'] = $detail;
        $this->set_tmpl('article');
        $this->set_tmpl_file($detail['bodys']['setting']['site_template']);
        $this->page('site/article/index.html');
    } //End Function
    private function _index2($detail)
    {
        $this->page('content:'.$detail['indexs']['article_id'], true);
    } //End Function

    // private function _index3($detail) {
    //     $this->set_tmpl_file($detail['bodys']['tmpl_path']);
    //     $this->page('content:' . $detail['indexs']['article_id']);
    // } //End Function


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
        $this->description = $aInfo['seo_description'];
        if ($aInfo['seo_keywords']) {
            $this->keywords = $aInfo['seo_keywords'];
        } else {
            $keyword = array();
            foreach ($aPath as $row) {
                $keyword[] = $row['title'];
            }
            $this->keywords = implode('-', $keyword);
        }
    }
} //End Class
