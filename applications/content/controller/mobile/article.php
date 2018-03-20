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


class content_ctl_mobile_article extends mobile_controller
{
    public function index()
    {
        $article_id = $this->_request->get_param(0);
        if ($article_id < 1) {
            vmc::singleton('mobile_router')->http_status(404); //exit;
        }
        $detail = vmc::singleton('content_article_detail')->get_detail($article_id, true);

        if ($detail['indexs']['ifpub'] != 'true') {
            $this->splash('error', '', '未发布');
        }
        $node = vmc::singleton('content_article_node');
        $node_info = $node->get_node($detail['indexs']['node_id'], true);
        if ($node_info['ifpub'] != 'true') {
            $this->splash('error', '', $node_info['node_name'].'未发布');
        }
        $runtime_path = $node->get_node_path($detail['indexs']['node_id'], true);
        $runtime_path[] = array(
            'link' => $this->app->router()->gen_url(array(
                'app' => 'content',
                'ctl' => 'mobile_article',
                'act' => 'index',
                'arg0' => $article_id,
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
        if(base_component_request::is_wxapp()){
            $detail['bodys']['content'] = $this->html_filter($detail['bodys']['content']);
        }
        $this->pagedata['detail'] = $detail;
        //vmc::singleton('content_article_detail')->parse_hot_link($detail['bodys']);
        $this->set_tmpl('article');
        $this->set_tmpl_file($detail['bodys']['setting']['mobile_template']);
        $this->page('mobile/article/index.html');
    } //End Function
    private function _index2($detail)
    {
        $this->page('content:'.$detail['indexs']['article_id'], true);
    } //End Function


    private function get_seo_info($aInfo, $aPath)
    {
        is_array($info) or $info = array();
        is_array($aPath) or $aPath = array();
        //title keywords description
        $title = array();
        $title[] = $aInfo['seo_title'] ? $aInfo['seo_title'] : $aPath[count($aPath) - 1]['title'];
        if (!$aInfo['seo_title']) {
            $title[] = $this->site_name ? $this->site_name : app::get('mobile')->getConf('mobile_name');
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

    /**
     *  filter html for applet
     */
    private function html_filter($html){
        $html_filter_conf = new HTMLFilterConfiguration();
        $allow_tag = array('p','br','ul','li','ol','table','tr','td','th','tfoot','thead','img');
        foreach ($allow_tag as $tag_name) {
            $html_filter_conf->allowTag($tag_name);
        }
        $html_filter_conf->allowAttribute('img','src');
        $html_filter = new HTMLFilter();
        $return = $html_filter->filter($html_filter_conf, $html);
        $return = preg_replace("/&#([\d]+);/","", $return);
        return $return;
    }
} //End Class
