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

class integralmall_ctl_mobile_list extends b2c_mfrontpage
{
    public $title = '积分兑换商品列表';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->set_tmpl('integralmall');
    }
    public function index()
    {
        $params = utils::_filter_input($_GET);
        $query_str = $this->_query_str($params);
        $this->pagedata['query'] = $this->_query_str($params, 0);
        $params = $this->_params_decode($params);
        $gfilter = $params['gfilter'];
        $rfilter = $params['rfilter'];
        $goods_list = $this->_list($gfilter, $rfilter, $params['page'], $params['orderby']);
        $this->pagedata['data_screen']['cat'] = $goods_list['relcat'];
        $this->pagedata['data_screen']['brand'] = $goods_list['relbrand'];
        $this->pagedata['data_screen']['min_num'] = $goods_list['min_num'];
        $this->pagedata['data_screen']['max_num'] = $goods_list['max_num'];
        $this->pagedata['data_list'] = $goods_list['data'];
        $this->pagedata['count'] = $goods_list['count'];
        $this->pagedata['all_count'] = $goods_list['all_count'];
        $this->pagedata['pager'] = $goods_list['page_info'];
        $this->pagedata['pager']['token'] = time();
        $page_layout = 'mobile/list/index.html';
        $this->pagedata['pager']['link'] = $this->gen_url(array(
            'app' => 'integralmall',
            'ctl' => 'mobile_list',
            'act' => 'index',
            'full' => 1,
        )).'?page='.$this->pagedata['pager']['token'].($query_str ? '&'.$query_str : '');
        if ($this->_request->is_ajax()) {
            //ajax 请求不经过模板机制
            $this->display($page_layout);
        } else {
            $this->page($page_layout);
        }
    }
    //获取商品列表，包装商品列表
    private function _list($gfilter, $rfilter, $page, $orderby)
    {
        $cache_key = 'integralmalllist_'.utils::array_md5(func_get_args());
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();
        $goods_cols = '*';
        $mdl_relgoods = $this->app->model('relgoods');
        $goods_list = $mdl_relgoods->goodsList($gfilter, $rfilter, $page['size'] * ($page['index'] - 1), $page['size'], $orderby, $total);
        $obj_goods_stage = vmc::singleton('b2c_goods_stage');
        //set_member
        if ($this->app->member_id = vmc::singleton('b2c_user_object')->get_member_id()) {
            $obj_goods_stage->set_member($this->app->member_id);
        }
        $obj_goods_stage->gallery($goods_list); //引用传递
        $return = array(
            'data' => $goods_list,
            'count' => count($goods_list) ,
            'all_count' => $total,
            'page_info' => array(
                'total' => ($total ? ceil($total / $page['size']) : 1) ,
                'current' => intval($page['index']),
            ),
        );
        $return['relcat'] = $this->app->model('relgoods')->relcat();
        $return['relbrand'] = $this->app->model('relgoods')->relbrand($gfilter['cat_id']);
        unset($rfilter['deduction|bthan']);
        unset($rfilter['deduction|sthan']);
        unset($rfilter['deduction']);
        $min_arr = $mdl_relgoods->goodsList($gfilter, $rfilter, 0, 1, ' r.deduction asc');
        $max_arr = $mdl_relgoods->goodsList($gfilter, $rfilter, 0, 1, ' r.deduction desc');
        $return['min_num'] = $min_arr[0]['deduction'];
        $return['max_num'] = $max_arr[0]['deduction'];

        cachemgr::set($cache_key, $return, cachemgr::co_end());
        return $return;
    }
    private function _query_str($params, $nopage = true)
    {
        if ($nopage) {
            unset($params['page']);
        }

        return http_build_query($params);
    }
    //配置参数
    private function _params_decode($params)
    {
        //排序
        $orderby = str_replace('-', ' ', $params['orderby']);
        unset($params['orderby']);
        //分页,页码
        $page['index'] = $params['page'] ? $params['page'] : 1;
        $page['size'] = $params['page_size'] ? $params['page_size'] : 10;
        unset($params['page']);
        unset($params['page_size']);
        // //价格区间
        // if ($params['price_min'] || $params['price_max']) {
        //     $params['price'] = ($params['price_min'] ? $params['price_min'] : '0').'~'.($params['price_max'] ? $params['price_max'] : '99999999');
        // }
        // unset($params['price_min']);
        // unset($params['price_max']);
        $params['marketable'] = 'true';
        if ($params['cat_id']) {
            $params['cat_id'] = app::get('b2c')->model('goods_cat')->get_all_children_id($params['cat_id']);
        }
        $tmp_gfilter = $params;
        foreach ($tmp_gfilter as $key => $value) {
            switch ($key) {
                case 'deduction':
                case 'marketable':
                unset($tmp_gfilter[$key]);
                $tmp_rfilter[$key] = $value;
                break;
            }
        }
        // //价格区间筛选
        // if ($tmp_gfilter['price']) {
        //     $tmp_gfilter['price'] = explode('~', $tmp_gfilter['price']);
        // }

        //积分区间筛选
        if ($tmp_rfilter['deduction']) {
            $duration_arr = explode('~', $tmp_rfilter['deduction']);
            $tmp_rfilter['deduction|bthan'] = $duration_arr[0];
            $tmp_rfilter['deduction|sthan'] = $duration_arr[1];
            unset($tmp_rfilter['deduction']);
        }

        $params['gfilter'] = $tmp_gfilter;
        $params['rfilter'] = $tmp_rfilter;
        $params['orderby'] = $orderby;
        $params['page'] = $page;

        return $params;
    }
}
