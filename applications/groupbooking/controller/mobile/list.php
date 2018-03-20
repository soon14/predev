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

class groupbooking_ctl_mobile_list extends b2c_mfrontpage
{
    public $title = '拼团列表';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->_response->set_header('Cache-Control', 'no-store');
    }

    public function index()
    {
        $params = utils::_filter_input($_GET);
        $query_str = $this->_query_str($params);
        $this->pagedata['query'] = $this->_query_str($params, 0);
        $params = $this->_params_decode($params);
        $filter = $params['filter'];

        $activity_list = $this->_list($filter, $params['page'], $params['orderby']);

        $this->pagedata['data_list'] = $activity_list['data'];
        $this->pagedata['count'] = $activity_list['count'];
        $this->pagedata['all_count'] = $activity_list['all_count'];
        $this->pagedata['pager'] = $activity_list['page_info'];
        $this->pagedata['pager']['token'] = time();

        $page_layout = 'mobile/list/index.html';
        $this->pagedata['pager']['link'] = $this->gen_url(array(
                'app' => 'groupbooking',
                'ctl' => 'mobile_list',
                'act' => 'index',
                'full' => 1,
            )).'?page='.$this->pagedata['pager']['token'].($query_str ? '&'.$query_str : '');

        if($this->_request->is_ajax()){
            //ajax 请求不经过模板机制
            $this->display($page_layout);
        }else{
            $this->page($page_layout);
        }

    }
    //获取拼团活动列表
    private function _list($filter, $page, $orderby)
    {

        $activity_cols = '*';
        $mdl_activity = $this->app->model('activity');
        $activity_list = $mdl_activity->getList($activity_cols, $filter, $page['size'] * ($page['index'] - 1), $page['size'], $orderby);
        $total = $mdl_activity->count($filter);

        $obj_activity_stage = vmc::singleton('groupbooking_activity_stage');
        $obj_activity_stage->gallery($activity_list); //引用传递

        $return =  array(
            'data' => $activity_list,
            'count' => count($activity_list) ,
            'all_count'=>$total,
            'page_info' => array(
                'total' => ($total ? ceil($total / $page['size']) : 1) ,
                'current' => intval($page['index']),
            ),
        );
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

        $time = time();
        $tmp_filter['start_time|sthan'] = $time;
        $tmp_filter['end_time|than'] = $time;
        $tmp_filter['status'] = 'process';

        $params['filter'] = $tmp_filter;
        $params['orderby'] = $orderby;
        $params['page'] = $page;

        return $params;
    }
}
