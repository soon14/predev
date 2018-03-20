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

class fpurchase_ctl_site_list extends b2c_frontpage
{
    public $title = '快速采购';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->verify_member();
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->set_tmpl('fpurchase');
    }
    public function index()
    {
        $params = utils::_filter_input($_GET);
        $query_str = $this->_query_str($params);
        $this->pagedata['query'] = $this->_query_str($params, 0);
        $params = $this->_params_decode($params);
        $filter = $params['filter'];
        $mdl_cat = app::get('b2c')->model('goods_cat');
        //$cat_info = $mdl_cat->dump($filter['cat_id']);
        $this->pagedata['cat_path'] = $mdl_cat->getPath($filter['cat_id']);
        $goods_list = $this->_list($filter, $params['page'], $params['orderby']);
        $this->pagedata['data_list'] = $goods_list['data'];
        $this->pagedata['count'] = $goods_list['count'];
        $this->pagedata['all_count'] = $goods_list['all_count'];
        $this->pagedata['pager'] = $goods_list['page_info'];
        $this->pagedata['pager']['token'] = time();
        $this->pagedata['data_screen'] = vmc::singleton('b2c_goods_stage')->screening_data_by_cat($filter['cat_id']);
        $page_layout = 'site/list/index.html';
        $this->pagedata['pager']['link'] = $this->gen_url(array(
            'app' => 'fpurchase',
            'ctl' => 'site_list',
            'act' => 'index',
            'full' => 1,
        )).'?page='.$this->pagedata['pager']['token'].($query_str ? '&'.$query_str : '');

        $this->page('site/list.html');
    }
    //获取商品列表，包装商品列表
    private function _list($filter, $page, $orderby)
    {
        $cache_key = utils::array_md5(func_get_args());
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();
        $mdl_goods = app::get('b2c')->model('goods');
        $goods_cols = '*';
        //根据价格排序
        if(explode(" ",$orderby)[0] == 'price'){
            $db = vmc::database();
            $goods_id = $db ->select('SELECT goods_id FROM `'.$db->prefix.'b2c_products`  GROUP BY goods_id ORDER BY '.$orderby);
            $str = '';
            foreach($goods_id as $k =>$v){
                $str .= ($k==0 ? '':',').$v['goods_id'];
            };
            $orderby ="FIND_IN_SET(goods_id , '".$str."')";
        }
        if($search = vmc::service('b2c.goods.list.search')){
            $res = $search->getList($goods_cols, $filter, $page['size'] * ($page['index'] - 1), $page['size'], $orderby);
            $goods_list = $res['rows'];
            $total = $res['total'];
        }else{
            $goods_list = $mdl_goods->getList($goods_cols, $filter, $page['size'] * ($page['index'] - 1), $page['size'], $orderby);
            $total = $mdl_goods->count($filter);
        }

        $goods_list = is_array($goods_list) ? $goods_list : array();
        $obj_goods_stage = vmc::singleton('b2c_goods_stage');
        //set_member
        if ($this->app->member_id = vmc::singleton('b2c_user_object')->get_member_id()) {
            $obj_goods_stage->set_member($this->app->member_id);
        }
        $obj_goods_stage->purchase_list($goods_list); //引用传递
        $return = array(
            'data' => $goods_list,
            'count' => count($goods_list) ,
            'all_count' => $total,
            'page_info' => array(
                'total' => ($total ? ceil($total / $page['size']) : 1) ,
                'current' => intval($page['index']),
            ),
        );
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
        $page['size'] = $params['page_size'] ? $params['page_size'] : 20;
        unset($params['page']);
        unset($params['page_size']);
        //价格区间
        if ($params['price_min'] || $params['price_max']) {
            $params['price'] = ($params['price_min'] ? $params['price_min'] : '0').'~'.($params['price_max'] ? $params['price_max'] : '99999999');
        }
        unset($params['price_min']);
        unset($params['price_max']);
        $params['marketable'] = 'true';
        $tmp_filter = $params;
        //价格区间筛选
        if ($tmp_filter['price']) {
            $tmp_filter['price'] = explode('~', $tmp_filter['price']);
        }
        $params['filter'] = $tmp_filter;
        $params['orderby'] = $orderby;
        $params['page'] = $page;

        return $params;
    }

}
