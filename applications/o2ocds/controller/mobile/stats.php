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


class o2ocds_ctl_mobile_stats extends o2ocds_mfrontpage
{
    public $title = '我的';

    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_o2ocds_member();
    }

    /*
     * 店铺管理
     * */
    public function store_list($page = 1) {
        if($this->app->type != 'enterprise') {
            $this->splash('error',null,'不是企业身份');
        }
        $limit = 10;
        $order_filter_post = utils::_filter_input($_POST);
        $order_filter_get = utils::_filter_input($_GET);
        $order_filter = array_merge((array)$order_filter_post, (array)$order_filter_get);
        foreach ($order_filter as $key => $value) {
            if ($value == '') {
                unset($order_filter[$key]);
                continue;
            }
            if ($key == 'from') {
                $order_filter['createtime']['from'] = strtotime($value);
                unset($order_filter[$key]);
            }
            if ($key == 'to') {
                $order_filter['createtime']['to'] = strtotime($value);
                unset($order_filter[$key]);
            }
        }
        $mdl_enterprise = $this->app->model('enterprise');
        $filter = array(
            'order' => $order_filter,
            'enterprise' => $this->app->enterprise
        );
        if($this->app->relation == 'salesman') {
            //业务员身份
            $filter['invitation']['member_id'] =  $this->app->member_id;
        }
        if($store_list = $mdl_enterprise->relevance_store($filter,($page - 1) * $limit, $limit)) {
            $this->pagedata['store_list'] = $store_list;
            $this->pagedata['arg_sum'] = vmc::singleton('ectools_math')->number_div(array(
                array_sum(array_keys(utils::array_change_key($store_list,'order_sum'))),
                count($store_list)
            ));
        };
        $count = $mdl_enterprise->count_store($filter);
        if(!$filter['from'] && !$filter['to']) {
            $start_row = $mdl_enterprise->db->select(
                "SELECT bo.createtime as createtime FROM   `vmc_o2ocds_store` os
                LEFT JOIN `vmc_o2ocds_service_code` sc ON sc.store_id = os.store_id
                LEFT JOIN `vmc_b2c_orders` bo ON sc.order_id = bo.order_id
                WHERE os.enterprise_id = ".$this->app->enterprise['enterprise_id'].' AND  bo.createtime IS NOT NULL ORDER BY bo.createtime ASC  LIMIT 0,1'
            );
            $end_row = $mdl_enterprise->db->select(
                "SELECT bo.createtime as createtime FROM   `vmc_o2ocds_store` os
                LEFT JOIN `vmc_o2ocds_service_code` sc ON sc.store_id = os.store_id
                LEFT JOIN `vmc_b2c_orders` bo ON sc.order_id = bo.order_id
                WHERE os.enterprise_id = ".$this->app->enterprise['enterprise_id'].' AND  bo.createtime IS NOT NULL ORDER BY bo.createtime DESC  LIMIT 0,1'
            );
            $this->pagedata['se_start'] = date('Y-m-d', $start_row[0]['createtime']);
            $this->pagedata['se_end'] = date('Y-m-d', $end_row[0]['createtime']);
        }else{
            $this->pagedata['se_start'] = $filter['from'];
            $this->pagedata['se_end'] = $filter['to'];
        }
        $this->pagedata['page'] = $page;
        $pager_url = $this->gen_url(array(
            'app' => 'o2ocds',
            'ctl' => 'mobile_stats',
            'act' => 'store_list',
            'args' => array(
                $limit,
                ($token = time()),
            ),
        ));
        $pager_url .= '?'.http_build_query($_GET);
        $this->pagedata['count'] = $count;
        $this->pagedata['limit'] = $limit;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit),
            'current' => $page,
            'link' => $pager_url,
            'token' => $token,
        );
        $this->page('mobile/default.html');
    }

    /*
     * 业务员管理
     * */
    public function salesman_list($page = 1) {
        $limit = 10;
        if($this->app->relation != 'admin') {
            $this->splash('error',null,'不是企业身份');
        }
        $filter_post = utils::_filter_input($_POST);
        $filter_get = utils::_filter_input($_GET);
        $filter = array_merge((array)$filter_post, (array)$filter_get);
        foreach ($filter as $key => $value) {
            if ($value == '') {
                unset($filter[$key]);
                continue;
            }
            if ($key == 'from') {
                $filter['usetime|bthan'] = strtotime($value);
                unset($filter[$key]);
            }
            if ($key == 'to') {
                $filter['usetime|lthan'] = strtotime($value);
                unset($filter[$key]);
            }
        }

        $mdl_enterprise = $this->app->model('enterprise');
        if($salesman_list = $mdl_enterprise->relation_sales($this->app->enterprise['enterprise_id'],$filter,($page - 1) * $limit, $limit)) {
            $mdl_members = app::get('b2c')->model('members');
            $member_ids = array_keys(utils::array_change_key($salesman_list,'member_id'));
            $member_info = $mdl_members->getList('member_id,avatar,mobile',array('member_id'=>$member_ids));
            $member_info = utils::array_change_key($member_info,'member_id');
            foreach ($salesman_list as &$item) {
                $item = array_merge($item,$member_info[$item['member_id']]);
            }
            $this->pagedata['data_list'] = $salesman_list;
            $this->pagedata['invitation_count'] = array_sum(array_keys(utils::array_change_key($salesman_list,'invitation_count')));
        };

        $count = $this->app->model('relation')->count(array('relation_id'=>$this->app->enterprise['enterprise_id'],'type'=>'enterprise'));
        if(!$filter['from'] && !$filter['to']) {
            $start_row = $mdl_enterprise->db->select(
                "SELECT oi.usetime as usetime  FROM `vmc_o2ocds_relation` vor
                LEFT JOIN  `vmc_o2ocds_invitation` oi ON oi.member_id = vor.member_id
                WHERE vor.relation_id = ".$this->app->enterprise['enterprise_id'].' AND  oi.usetime IS NOT NULL ORDER BY oi.usetime ASC  LIMIT 0,1'
            );
            $end_row = $mdl_enterprise->db->select(
                "SELECT oi.usetime as usetime  FROM `vmc_o2ocds_relation` vor
                LEFT JOIN  `vmc_o2ocds_invitation` oi ON oi.member_id = vor.member_id
                WHERE vor.relation_id = ".$this->app->enterprise['enterprise_id'].' AND  oi.usetime IS NOT NULL ORDER BY oi.usetime DESC  LIMIT 0,1'
            );
            $this->pagedata['se_start'] = date('Y-m-d', $start_row[0]['usetime']);
            $this->pagedata['se_end'] = date('Y-m-d', $end_row[0]['usetime']);
        }else{
            $this->pagedata['se_start'] = $filter['from'];
            $this->pagedata['se_end'] = $filter['to'];
        }
        $this->pagedata['page'] = $page;
        $pager_url = $this->gen_url(array(
            'app' => 'o2ocds',
            'ctl' => 'mobile_stats',
            'act' => 'store_list',
            'args' => array(
                $limit,
                ($token = time()),
            ),
        ));
        $pager_url .= '?'.http_build_query($_GET);
        $this->pagedata['count'] = $count;
        $this->pagedata['limit'] = $limit;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit),
            'current' => $page,
            'link' => $pager_url,
            'token' => $token,
        );
        $this->page('mobile/default.html');
    }

    /*
     * 店员管理
     * */
    public function salesclerk_list($page = 1) {
        if($this->app->relation != 'manager') {
            $this->splash('error',null,'不是店长身份');
        }
        $limit = 10;
        $filter_post = utils::_filter_input($_POST);
        $filter_get = utils::_filter_input($_GET);
        $filter = array_merge((array)$filter_post, (array)$filter_get);
        $order_array = array('order_count','order_sum');
        if(in_array($filter['order'],$order_array)) {
            $order_type = $filter['order'];
        }
        foreach ($filter as $key => $value) {
            if ($value == '') {
                unset($filter[$key]);
                continue;
            }
            if ($key == 'from') {
                $filter['cancel_time|bthan'] = strtotime($value);
                unset($filter[$key]);
            }
            if ($key == 'to') {
                $filter['cancel_time|lthan'] = strtotime($value);
                unset($filter[$key]);
            }
        }

        $mdl_store = $this->app->model('store');
        if($salesclerk_list = $mdl_store->relation_clerk($this->app->store_manager['relation_id'],$filter,($page - 1) * $limit, $limit,$order_type)) {
            $mdl_members = app::get('b2c')->model('members');
            $member_ids = array_keys(utils::array_change_key($salesclerk_list,'member_id'));
            $member_info = $mdl_members->getList('*',array('member_id'=>$member_ids));
            $member_info = utils::array_change_key($member_info,'member_id');
            foreach ($salesclerk_list as &$item) {
                $item['member_info'] = $member_info[$item['member_id']];
            }
            $this->pagedata['data_list'] = $salesclerk_list;
        };
        //$this->pagedata['order_count'] = $mdl_store->count_order($this->app->store_manager['relation_id']);
        $count = $this->app->model('relation')->count(array('relation_id'=>$this->app->store_manager['relation_id'],'type'=>'store'));
        if(!$filter['from'] && !$filter['to']) {
            $start_row = $mdl_store->db->select(
                "SELECT sc.cancel_time as cancel_time FROM `vmc_o2ocds_relation` vor
                LEFT JOIN `vmc_o2ocds_service_code` sc ON vor.member_id = sc.member_id
                WHERE vor.relation_id = ".$this->app->store_manager['relation_id'].' AND  sc.cancel_time IS NOT NULL ORDER BY sc.cancel_time ASC  LIMIT 0,1'
            );
            $end_row = $mdl_store->db->select(
                "SELECT sc.cancel_time as cancel_time FROM `vmc_o2ocds_relation` vor
                LEFT JOIN `vmc_o2ocds_service_code` sc ON vor.member_id = sc.member_id
                WHERE vor.relation_id = ".$this->app->store_manager['relation_id'].' AND  sc.cancel_time IS NOT NULL ORDER BY sc.cancel_time DESC  LIMIT 0,1'
            );
            $this->pagedata['se_start'] = date('Y-m-d', $start_row[0]['cancel_time']);
            $this->pagedata['se_end'] = date('Y-m-d', $end_row[0]['cancel_time']);
        }else{
            $this->pagedata['se_start'] = $filter['from'];
            $this->pagedata['se_end'] = $filter['to'];
        }
        $this->pagedata['page'] = $page;
        $pager_url = $this->gen_url(array(
            'app' => 'o2ocds',
            'ctl' => 'mobile_stats',
            'act' => 'salesclerk_list',
            'args' => array(
                $limit,
                ($token = time()),
            ),
        ));
        $pager_url .= '?'.http_build_query($_GET);
        $this->pagedata['count'] = $count;
        $this->pagedata['limit'] = $limit;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit),
            'current' => $page,
            'link' => $pager_url,
            'token' => $token,
        );
        $this->page('mobile/default.html');
    }

    /*
     * 热销排名
     * */
    public function hot_goods($page = 1) {
        $limit = 10;
        $mdl_service_code = $this->app->model('service_code');

        $filter_post = utils::_filter_input($_POST);
        $filter_get = utils::_filter_input($_GET);
        $filter = array_merge((array)$filter_post, (array)$filter_get);
        $order_array = array('goods_count','goods_sum');
        if(in_array($filter['order'],$order_array)) {
            $order_type = $filter['order'];
        }
        unset($filter['order']);
        foreach ($filter as $key => $value) {
            if ($value == '') {
                unset($filter[$key]);
                continue;
            }
            if ($key == 'from') {
                $filter['createtime|bthan'] = strtotime($value);
                unset($filter[$key]);
            }
            if ($key == 'to') {
                $filter['createtime|lthan'] = strtotime($value);
                unset($filter[$key]);
            }
        }
        if($this->app->relation == 'manager') {
            $store_ids = array_keys(utils::array_change_key($this->app->store_list,'store_id'));
            $filter['store_id'] = $store_ids;
        }elseif($this->app->relation == 'admin') {
            $filter['enterprise_id'] = $this->app->enterprise['enterprise_id'];
        }elseif($this->app->relation == 'salesclerk') {
            $filter['member_id'] = $this->app->member_id;
        }elseif($this->app->relation == 'salesman') {
            if($store_ids = $this->app->model('invitation')->get_store_ids($this->app->member_id)) {
                $filter['store_id'] = $store_ids;
            }
        }
        if($goods_list = $mdl_service_code ->get_hots($filter,($page - 1) * $limit, $limit, $order_type, $goods_count,$amount_avg)) {
            $this->pagedata['goods_list'] = $goods_list;
        };
        $count = $this->app->model('relation')->count(array('relation_id'=>$this->app->store_manager['relation_id'],'type'=>'store'));
        $this->pagedata['goods_count'] = $goods_count;
        $this->pagedata['amount_avg'] = $amount_avg;
        if(!$filter['from'] && !$filter['to']) {
            $start_row = $mdl_service_code->getRow('createtime',$filter , 'createtime ASC');
            $end_row = $mdl_service_code->getRow('createtime', $filter, 'createtime DESC');
            $this->pagedata['se_start'] = date('Y-m-d', $start_row['createtime']);
            $this->pagedata['se_end'] = date('Y-m-d', $end_row['createtime']);
        }else{
            $this->pagedata['se_start'] = $filter['from'];
            $this->pagedata['se_end'] = $filter['to'];
        }
        $this->pagedata['page'] = $page;
        $pager_url = $this->gen_url(array(
            'app' => 'o2ocds',
            'ctl' => 'mobile_stats',
            'act' => 'hot_goods',
            'args' => array(
                $limit,
                ($token = time()),
            ),
        ));
        $pager_url .= '?'.http_build_query($_GET);
        $this->pagedata['count'] = $count;
        $this->pagedata['limit'] = $limit;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit),
            'current' => $page,
            'link' => $pager_url,
            'token' => $token,
        );
        $this->page('mobile/default.html');
    }


}
