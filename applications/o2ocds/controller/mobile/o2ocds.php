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


class o2ocds_ctl_mobile_o2ocds extends o2ocds_mfrontpage
{
    public $title = '我的';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_o2ocds_member();
        $this->set_tmpl('cdsstore');
    }

    /*
     * 首页
     * */
    public function index()
    {
        $day_filter = array(
            'createtime|bthan' => strtotime(date('Y-m-d'.' 00:00:00',time())),
            'createtime|lthan' => time(),
        );

        $month_filter = array(
            'createtime|bthan' => strtotime(date('Y-m'.'-01 00:00:00',time())),
            'createtime|lthan' => time(),
        );
        $mdl_service_code = $this->app->model('service_code');
        $mdl_achieve = $this->app->model('orderlog_achieve');

        if($this->app->type == 'enterprise') {
            $this->pagedata['enterprise'] = $this->app->enterprise;
            if($this->app->relation == 'admin') {
                $achieve_filter = array(
                    'relation_id' => $this->app->enterprise['enterprise_id'],
                    'type' => 'enterprise',
                );
                $service_filter = array(
                    'enterprise_id' => $this->app->enterprise['enterprise_id'],
                );
            }else{
                //业务员查看邀请的店铺订单
                if($store_ids = $this->app->model('invitation')->get_store_ids($this->app->member_id)) {
                    $achieve_filter = array(
                        'relation_id' => $store_ids,
                        'type' => 'store',
                    );
                    $service_filter = array(
                        'store_id' => $store_ids,
                    );
                }
            }
        }else{
            $this->pagedata['store_list'] = $this->app->store_list;
            $store_ids = array_keys(utils::array_change_key($this->app->store_list,'store_id'));
            if($this->app->relation == 'manager') {
                $achieve_filter = array(
                    'relation_id' => $store_ids,
                    'type' => 'store',
                );
                $service_filter = array(
                    'store_id' => $store_ids,
                );
            }else{
                $service_filter = array(
                    'member_id' => $this->app->member_id,
                );
            }
        }
        //统计一周公告数量
        $notice_filter['role_'.$this->app->relation] = 'true';
        $notice_filter['createtime|bthan'] = strtotime('-1 week');
        $notice_filter['createtime|lthan'] = time();
        $this->pagedata['count_week_notice'] = $this->app->model('notice')->count($notice_filter);

        if($service_filter) {
            $this->pagedata['count_today_order'] = $mdl_service_code->count(array_merge($day_filter,$service_filter));
            $this->pagedata['count_month_order'] = $mdl_service_code->count(array_merge($month_filter,$service_filter));
            $this->pagedata['count_today_order_subprice'] = ectools_cur::format($mdl_service_code->subprice(array_merge($day_filter,$service_filter)));
            $this->pagedata['count_month_order_subprice'] = ectools_cur::format($mdl_service_code->subprice(array_merge($month_filter,$service_filter)));
        }
        if($achieve_filter) {
            $this->pagedata['count_voucher_subprice_day'] = ectools_cur::format($mdl_achieve->count_subprice(array_merge($day_filter,$achieve_filter)));
            $this->pagedata['count_voucher_subprice'] = ectools_cur::format($mdl_achieve->count_subprice(array_merge($month_filter,$achieve_filter)));
        }
        $this->pagedata['type'] = $this->app->type;
        $this->pagedata['relation'] = $this->app->relation;
        $this->page('mobile/default.html');
    }

    /*
     * 订单列表
     * */
    public function orders($status = 'all', $page = 1)
    {
        $limit = 10;
        $status_filter = array(
            'all' => array(//全部
            ) ,
            's1' => array(//待付款
                'status' => 'active',
                'pay_status' => array(
                    '0',
                    '3',
                    '5',
                ),
            ) ,
            's2' => array(//待发货
                'status' => 'active',
                'pay_status' => array(
                    '1',
                    '2',
                ) ,
                'ship_status|notin' => array(
                    '1',
                ),
            ) ,
            's3' => array(//已发货
                'status' => 'active',
                'ship_status' => array(
                    '1',
                    '2',
                ),
            ) ,
        );
        $order_filter = $status_filter[$status];
        //订单筛选条件
        if ($_POST['order']) {
            $order_filter = array_merge($order_filter, $_POST['order']);
        }
        $mdl_order_items = app::get('b2c')->model('order_items');
        $mdl_service_code = $this->app->model('service_code');
        if($this->app->relation == 'manager') {
            //一个会员有多个店铺
            $store_ids = array_keys(utils::array_change_key($this->app->store_list,'store_id'));
            $filter = array('store_id'=>$store_ids);
        }elseif($this->app->relation == 'admin') {
            $filter = array('enterprise_id'=>$this->app->enterprise['enterprise_id']);
        }elseif($this->app->relation == 'salesclerk') {
            //店员查看自己核销过的订单
            $filter['member_id'] = $this->app->member_id;
        }elseif($this->app->relation == 'salesman') {
            //业务员查看邀请的店铺订单
            if($store_ids = $this->app->model('invitation')->get_store_ids($this->app->member_id)) {
                $filter = array('store_id'=>$store_ids);
            }
        }
        $order_list = array();
        //查询订单   店员查询核销订单
        if($filter) {
            $order_list = $mdl_service_code->order_list($filter,$order_filter, ($page - 1) * $limit, $limit, 'o.createtime DESC', $order_count);
        }
        //核销店员信息
        $mdl_pam_member = app::get('pam')->model('members');
        $mdl_members = app::get('b2c')->model('members');
        if($sc_member_ids = array_keys(utils::array_change_key($order_list, 'sc_member_id'))) {
            $member_list = $mdl_members->getList('member_id,avatar,mobile,name',array('member_id'=>$sc_member_ids));
            if($member_list) {
                $this->pagedata['sc_member_list'] = utils::array_change_key($member_list,'member_id');
            }
        };
        //店铺信息
        $mdl_store = $this->app->model('store');
        if($store_ids = array_keys(utils::array_change_key($order_list, 'store_id'))) {
            $store_list = $mdl_store->getList('store_id,sno,name,mobile,director_name',array('store_id'=>$store_ids));
            if($store_list) {
                $this->pagedata['rel_store_list'] = utils::array_change_key($store_list,'store_id');
            }
        };
        //订单商品信息
        $oids = array_keys(utils::array_change_key($order_list, 'order_id'));
        $order_items = $mdl_order_items->getList('*', array(
            'order_id' => $oids,
        ));
        $order_items_group = utils::array_change_key($order_items, 'order_id', true);
        $this->pagedata['current_status'] = $status;
        $this->pagedata['status_map'] = $status_filter;
        $this->pagedata['order_list'] = $order_list;
        $this->pagedata['order_count'] = $order_count;
        $this->pagedata['order_items_group'] = $order_items_group;
        $this->pagedata['pager'] = array(
            'total' => ceil($order_count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'o2ocds',
                'ctl' => 'mobile_store',
                'act' => 'orders',
                'args' => array(
                    $status,
                    ($token = time()),
                ) ,
            ) ,
            'token' => $token,
        );
        $this->page('mobile/default.html');
    }

    /*
     * 订单明细
     * */
    public function order_detail($order_id)
    {

        $mdl_order = app::get('b2c')->model('orders');
        $bills = app::get('ectools')->model('bills');
        $mdl_order_log = app::get('b2c')->model('order_log');
        $mdl_delivery = app::get('b2c')->model('delivery');
        $order = $mdl_order->dump($order_id, '*', array(
            'items' => array(
                '*',
            ),
            'promotions' => array(
                '*',
            ),
            ':dlytype' => array(
                '*',
            ),
        ));

        foreach ($mdl_order_log->getList('behavior,log_time', array(
            'order_id' => $order['order_id'],
            'result' => 'success',
            //会员端只显示成功日志

        )) as $log) {
            $order['process'][$log['behavior']] = $log['log_time'];
        }
        // $order['service_code'] = $this->app->model('service_code')->getRow('*',array('order_id'=>$order['order_id']));
        // //核销店员信息
        // if($order['service_code']['member_id']) {
        //     $order['service_code']['login_account'] =  app::get('pam')->model('members')->getRow('member_id,login_account',array('member_id'=>$order['service_code']['member_id']))['login_account'];
        // }
        $mdl_service_code = $this->app->model('service_code');
        if($service_code = $mdl_service_code->getRow('*', array('order_id' => $order['order_id']))) {
                $this->pagedata['store'] = $this->app->model('store')->dump($service_code['store_id']);
                $this->pagedata['sc_member'] = app::get('b2c')->model('members')->dump($service_code['member_id']);
                $this->pagedata['scode'] = $service_code;
        }
        $this->pagedata['order'] = $order;
        $this->pagedata['payapp'] = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);
        if ($order['ship_status'] != '0' && $order['need_shipping'] == 'Y') {
            $delivery_list = $mdl_delivery->getList('delivery_id', array(
                'order_id' => $order['order_id'],
                'logistics_no|notin' => array(
                    '',
                    null,
                    'null',
                ),
            ));
            foreach ($delivery_list as $key => &$value) {
                $data = vmc::singleton('logisticstrack_puller')->pull($value['delivery_id'], $errmsg);
                foreach ($data['logi_log'] as $k => &$v) {
                    $v['context'] = strip_tags($v['context']);
                }
                $value['tracker_log'] = $data;
            }
            $this->pagedata['delivery_list'] = $delivery_list;
        }
        foreach ($order['promotions'] as $pitem) {
            switch ($pitem['pmt_type']) {
                case 'goods':
                    $this->pagedata['goods_pmt'][$pitem['product_id']][] = $pitem;
                    break;
                case 'order':
                    $this->pagedata['order_pmt'][] = $pitem;
                    break;
            }
        }
        $this->page('mobile/default.html');
    }

    /*
     * 结算凭证
     * */
    public function orderlog_achieve($page = 1, $limit = 10)
    {
        if ($limit > 100) {
            $limit = 100;
        }
        $mdl_achieve = $this->app->model('orderlog_achieve');
        $filter_post = utils::_filter_input($_POST);
        $filter_get = utils::_filter_input($_GET);
        if (!is_array($filter_post)) {
            $filter_post = array();
        }
        if (!is_array($filter_get)) {
            $filter_get = array();
        }
        $filter = array_merge($filter_post, $filter_get);
        if($this->app->type == 'store') {
            //店铺有多个
            $relation_id = array_keys(utils::array_change_key($this->app->store_list,'store_id'));
            $filter['relation_id'] = $relation_id;
        }elseif($this->app->type == 'enterprise') {
            $relation_id = $this->app->enterprise['enterprise_id'];
            $filter['relation_id'] = $relation_id;
        }
        $filter['type'] = $this->app->type;
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
        $this->pagedata['count_subprice'] = $mdl_achieve->count_subprice($filter);

        $achieve_list = $mdl_achieve->getList('*', $filter, ($page - 1) * $limit, $limit);
        if(!$filter['from'] && !$filter['to']) {
            $start_row = $mdl_achieve->getRow('createtime', array('relation_id' => $relation_id,'type'=>$this->app->type), 'createtime ASC');
            $end_row = $mdl_achieve->getRow('createtime', array('relation_id' => $relation_id,'type'=>$this->app->type), 'createtime DESC');
            $this->pagedata['se_start'] = date('Y-m-d', $start_row['createtime']);
            $this->pagedata['se_end'] = date('Y-m-d', $end_row['createtime']);
        }else{
            $this->pagedata['se_start'] = $filter['from'];
            $this->pagedata['se_end'] = $filter['to'];
        }

        if ($achieve_list) {
            //查询凭证详情
            $orderlog_id_arr = array_keys(utils::array_change_key($achieve_list, 'orderlog_id'));
            $mdl_orderlog_items = $this->app->model('orderlog_items');
            $achieve_items = $mdl_orderlog_items->getList('*', array('orderlog_id' => $orderlog_id_arr));
            $this->pagedata['achieve_items'] = utils::array_change_key($achieve_items, 'orderlog_id', true);
            //查询订单核销信息
            // $order_id_arr = array_keys(utils::array_change_key($achieve_list, 'order_id'));
            // $mdl_service_code = $this->app->model('service_code');
            // if($service_code = $mdl_service_code->getList('*', array('order_id' => $order_id_arr))) {
            //     $this->pagedata['service_code'] = utils::array_change_key($service_code,'order_id');
            //     //核销店员信息
            //     $mdl_pam_member = app::get('pam')->model('members');
            //     $mdl_members = app::get('b2c')->model('members');
            //     if($sc_member_ids = array_keys(utils::array_change_key($service_code, 'member_id'))) {
            //         $member_list = $mdl_members->getList('member_id,avatar,mobile,name',array('member_id'=>$sc_member_ids));
            //         if($mdl_members) {
            //             $this->pagedata['sc_member_list'] = utils::array_change_key($member_list,'member_id');
            //         }
            //     };
            // };
            $count = $mdl_achieve->count($filter);
            $this->pagedata['achieve_list'] = $achieve_list;
            $this->pagedata['count'] = $count;
            $this->pagedata['page'] = $page;
            $this->pagedata['totalpage'] = ceil($count / $limit);
            $pager_url = $this->gen_url(array(
                'app' => 'o2ocds',
                'ctl' => 'mobile_store',
                'act' => 'orderlog_achieve',
                'args' => array(
                    ($token = time()),
                    $limit,
                ),
            ));
            $pager_url .= '?'.http_build_query($_GET);
            $this->pagedata['count'] = $count;
            $this->pagedata['limit'] = $limit;
            $this->pagedata['pager'] = array(
                'total' => $this->pagedata['totalpage'] ,
                'current' => $page,
                'link' => $pager_url,
                'token' => $token,
            );
        }
        $this->page('mobile/default.html');
    }

    /*
     * 结算单列表
     * */
    public function statement($page = 1, $limit = 20)
    {

        if ($limit > 100) {
            $limit = 100;
        }
        $mdl_statement = $this->app->model('statement');
        $filter_post = utils::_filter_input($_POST);
        $filter_get = utils::_filter_input($_GET);
        if (!is_array($filter_post)) {
            $filter_post = array();
        }
        if (!is_array($filter_get)) {
            $filter_get = array();
        }
        $filter = array_merge($filter_post, $filter_get);
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
        //结算id，结算单类型类型
        if($this->app->type == 'store') {
            //店铺有多个
            $store_ids = array_keys(utils::array_change_key($this->app->store_list,'store_id'));
            $filter['relation_id'] = $store_ids;
            $relation_id = $store_ids;
        }elseif($this->app->type == 'enterprise') {
            $filter['relation_id'] = $this->app->enterprise['enterprise_id'];
            $relation_id = $this->app->enterprise['enterprise_id'];
        }
        $filter['relation_type']= $this->app->type;
        $statement_list = $mdl_statement->getList('*', $filter, ($page - 1) * $limit, $limit);
        $start_row = $mdl_statement->getRow('createtime', array('relation_id' => $relation_id,'type'=>$this->app->type), 'createtime ASC');
        $end_row = $mdl_statement->getRow('createtime', array('relation_id' => $relation_id,'type'=>$this->app->type), 'createtime DESC');
        $this->pagedata['se_start'] = date('Y-m-d', $start_row['createtime']);
        $this->pagedata['se_end'] = date('Y-m-d', $end_row['createtime']);
        if ($statement_list) {
            $count = $mdl_statement->count($filter);
            $this->pagedata['statement_list'] = $statement_list;
            $this->pagedata['page'] = $page;
            $this->pagedata['totalpage'] = ceil($count / $limit);
            $pager_url = $this->gen_url(array(
                'app' => 'vshop',
                'ctl' => 'mobile_shop',
                'act' => 'statement',
                'args' => array(
                    $status,
                    ($token = time()),
                    $limit,
                ),
            ));
            $this->pagedata['status'] = $status;
            $this->pagedata['count'] = $count;
            $this->pagedata['limit'] = $limit;
            $this->pagedata['pager'] = array(
                'total' => $this->pagedata['totalpage'] ,
                'current' => $page,
                'link' => $pager_url,
                'token' => $token,
            );
        }
        $this->page('mobile/default.html');
    }

    /*
     * 结算单明细
     * */
    public function statement_detail($statement_id)
    {
        if(!$statement_id) {
            $this->splash('error',null,'未知结算单');
        }
        $mdl_statement = $this->app->model('statement');
        $filter['statement_id'] = $statement_id;
        $statement = $mdl_statement->getRow('*',$filter);
        if ($statement) {
            $mdl_statement_index = $this->app->model('statement_index');
            $mdl_orderlog_achieve = $this->app->model('orderlog_achieve');
            $mdl_orderlog_items = $this->app->model('orderlog_items');
            //凭证列表
            $achieve_id_arr = $mdl_statement_index->getColumn('achieve_id', array('statement_id' => $statement_id));
            $achieve_list = $mdl_orderlog_achieve->getList('*', array('achieve_id' => $achieve_id_arr));
            //凭证明细
            $orderlog_ids = array_keys(utils::array_change_key($achieve_list,'orderlog_id'));
            $achieve_list_items = $mdl_orderlog_items->getList('*', array('orderlog_id' => $orderlog_ids));
            $achieve_list_items = utils::array_change_key($achieve_list_items, 'orderlog_id', true);
            $this->pagedata['statement'] = $statement;
            $this->pagedata['achieve_list'] = $achieve_list;
            $this->pagedata['achieve_items'] = $achieve_list_items;
        }else{
            $this->splash('error',null,'未知结算单');
        }
        $this->page('mobile/default.html');
    }



    /*
     * 保存申请结算
     * */
    public function save_statement() {
        $mdl_statement = $this->app->model('statement');
        $statement_id = $_POST['statement_id'];
        $statement = $mdl_statement->dump($statement_id);
        if(!$statement){
            $this->splash('error',null,'未知结算单据');
        }
        if($statement['status']!='noconfirm'){
            $this->splash('error',null,'结算单不在待确认状态');
        }
        $relation_id = $statement['relation_id'];
        $mdl_relation = $this->app->model('relation');
        $relation_data = $mdl_relation->getRow('*',array(
            'relation_id'=>$relation_id,
            'member_id'=>$this->app->member_id,
            'relation|in'=>array(
                'admin',
                'manager'
            )
        ));
        if(!$relation_data){
            $this->splash('error',null,'没有结算权限');
        }
        $data = $_POST['statement_payee_info'];
        foreach ($data as $key => $value) {
            if(!in_array($key,array('payee','payee_account','payee_bank'))){
                $this->splash('error',null,'非法操作');
            }else{
                if(trim($value) == ''){
                    $this->splash('error',null,'缺少必要信息');
                }
            }
        }
        $data['status'] = 'ready';
        if(!$mdl_statement->update($data,array('statement_id'=>$statement_id))) {
            $this->splash('error',null,'申请失败');
        };
        $this->splash('success',null,'申请成功');
    }

    /*
     * 邀请码页面
     * */
    public function invitation() {
        if(!$this->app->enterprise['enterprise_id']) {
            $this->splash('error',null,'未知企业');
        }
        if($this->app->enterprise['status'] != '1') {
            $this->splash('error',null,'企业未通过审核,无法邀请店铺');
        }
        $mem_lv = app::get('b2c')->model('member_lv');
        $mem_lv_list = $mem_lv->getList('*');

        $this->pagedata['member_lv_list'] = $mem_lv_list;
        $this->display('mobile/default.html');
    }

    /*
     * 创建邀请码
     * */
    public function create_invitation() {
        if(!$this->app->enterprise['enterprise_id']) {
            $this->splash('error',null,'未知企业');
        }
        if($this->app->enterprise['status'] != '1') {
            $this->splash('error',null,'企业未通过审核,无法生成店铺邀请码');
        }
        $mdl_invitation = app::get('o2ocds')->model('invitation');
        $data = $_POST;
        $data['op_id'] = $this->app->enterprise['enterprise_id'];
        $data['invitation_code'] = $mdl_invitation->apply_code();
        $data['member_id'] = $this->app->member_id;
        $data['enterprise_id'] = $this->app->enterprise['enterprise_id'];
        $data['createtime'] = time();
        if(!$mdl_invitation->save($data)) {
            $this->splash('error',null,'操作失败');
        };
        $this->splash('success',null,'邀请码生成成功');
    }

    /*
     * 邀请码列表
     * */
    public function invitation_list() {
        if(!$this->app->enterprise['enterprise_id']) {
            $this->splash('error',null,'未知企业');
        }
        if($this->app->enterprise['status'] != '1') {
            $this->splash('error',null,'企业未通过审核,无法邀请店铺');
        }
        $filter['enterprise_id'] = $this->app->enterprise['enterprise_id'];
        //企业管理员和业务员只看自己的邀请码
        $filter['member_id'] = $this->app->member_id;
        if(!$filter) {
            $this->splash('error',null,'未知账号');
        }
        $mdl_invitation = $this->app->model('invitation');
        if($invitation_list = $mdl_invitation->getList('*',$filter)) {
            //查询出发行会员和使用会员信息
            $member_ids = array_keys(utils::array_change_key($invitation_list,'member_id'));
            $use_member_ids = array_keys(utils::array_change_key($invitation_list,'use_member_id'));
            $mdl_pam_member = app::get('pam')->model('members');
            if($member_ids = array_filter(array_merge($member_ids,(array)$use_member_ids))) {
                if($member_list = $mdl_pam_member->getList('member_id,login_account',array('member_id'=>$member_ids))) {
                    $this->pagedata['member_list'] = utils::array_change_key($member_list,'member_id');
                }
            };
            //查询出邀请店铺信息
            $store_ids = array_keys(utils::array_change_key($invitation_list,'store_id'));
            if($store_list = $this->app->model('store')->getList('*',array('store_id'=>$store_ids))) {
                $this->pagedata['store_list'] = utils::array_change_key($store_list,'store_id');
            };
            $this->pagedata['invitation_list'] = $invitation_list;
        };
        $this->pagedata['enterprise'] = $this->app->enterprise;
        $this->display('mobile/default.html');
    }

    /*
     * 公告列表
     * */
    public function notice_list($page = 1)
    {
        $limit = 10;
        $mdl_notice = $this->app->model('notice');
        $filter = array('ispub' => 'true');
        $filter['role_'.$this->app->relation] = 'true';
        $notice_list = $mdl_notice->getList('*',$filter , ($page - 1) * $limit, $limit);
        $count = $mdl_notice->count($filter);
        $this->pagedata['notice_list'] = $notice_list;
        $this->pagedata['count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['totalpage'] = ceil($count / $limit);
        $this->pagedata['pager'] = array(
            'total' => $this->pagedata['totalpage'] ,
            'current' => $page,
            'link' => $this->gen_url(array(
                'app' => 'o2ocds',
                'ctl' => 'site_o2ocds',
                'act' => 'notice_list',
                'args' => array(
                    ($token = time()),
                ),
            )),
            'token' => $token,
        );
        $this->display('mobile/default.html');
    }

    /*
     * 公告详细
     * */
    public function notice($notice_id)
    {
        $mdl_notice = $this->app->model('notice');
        $this->set_tmpl('o2ocds_notice');
        $notice = $mdl_notice->dump($notice_id);
        if ($notice['ispub'] == 'false') {
            $this->splash('error', array(
                'app' => 'o2ocds',
                'ctl' => 'site_o2ocds',
                'act' => 'index',
            ), '未发布的公告');
        }
        if($notice['integral'] > 0 && $notice['role_'.$this->app->relation] == 'true') {
            $mdl_notice_integral = $this->app->model('notice_integral');
            if(!$mdl_notice_integral->getRow('id',array('member_id'=>$this->app->member_id,'notice_id'=>$notice['notice_id']))) {
                $db = vmc::database();
                $this->transaction_status = $db->beginTransaction();
                $integral_change_data = array(
                    'member_id'=>$this->app->member_id,
                    'change'=>$notice['integral'],
                    'change_reason'=>'else',
                    'op_model'=>'member',
                    'op_id'=>$this->app->member_id,
                    'remark'=>'阅读分销平台公告获得积分奖励:'.$notice['integral'],
                );
                if(!vmc::singleton('b2c_member_integral')->change($integral_change_data,$msg)){
                    $db->rollback();
                    $this->splash('error',null,'积分赠送失败'.$msg);
                }
                $notice_data = array(
                    'notice_id' => $notice['notice_id'],
                    'integral' => $notice['integral'],
                    'member_id' => $this->app->member_id,
                    'time' => time(),
                );
                if(!$mdl_notice_integral->save($notice_data)) {
                    $db->rollback();
                    $this->splash('error',null,'积分赠送失败 '.$msg);
                };
                $db->commit($this->transaction_status);
                $notice['is_integral'] = true;
            };
        }
        $this->pagedata['notice'] = $notice;
        $this->page('mobile/default.html');
    }

    /*
     * 我的二维码
     * */
    public function myqrcode($return_url = false) {
        //企业参数直接是当前企业，店铺需要选择或传入
        if($this->app->relation == 'admin') {
            $relation_id = $this->app->enterprise['enterprise_id'];
            $relation_data = $this->app->model('enterprise')->dump($relation_id);
            $relation_data['relation_type'] = 'enterprise';
        }elseif($this->app->relation == 'manager') {
            $relation_id = $this->app->store_manager['relation_id'];
            $relation_data = $this->app->model('store')->dump($relation_id);
            $relation_data['relation_type'] = 'store';
        }else{
            $this->splash('error',null,'无二维码');
        }
        $mobile_url = vmc::singleton('mobile_router')->gen_url(array(
            'app'=>'o2ocds',
            'ctl'=>'mobile_qrrouter',
            'act'=>'bind_relation',
            'full'=>1
        ));
        $encrypt_data = array(
            'relation' => $this->app->relation,
            'relation_id' => $relation_id
        );
        $bind_relation = utils::encrypt($encrypt_data);
        $mobile_url .= '?relation='.$bind_relation;
        if($return_url){
            $this->splash('success',null,array('qrcode_url'=>$mobile_url,'relation_data'=>$relation_data));
        }
        ectools_qrcode_QRcode::png($mobile_url,false,0,7,10);
    }

    /*
     * 获取当前店铺的绑定的二维码
     * */
    public function get_qrocde() {
        if($this->app->type != 'store') {
            $this->splash('error',null,'不是店铺身份');
        }
        if($this->app->relation == 'manager') {
            $store_id  = $this->app->store_manager['relation_id'];
        }else{
            $store_id  = $this->app->store['relation_id'];
        }
        $mdl_qrcode = $this->app->model('qrcode');
        if(!$qrcode_data = $mdl_qrcode->getRow('*',array('store_id'=>$store_id))) {
            $this->splash('error',null,'未知二维码');
        };
        $qrcode  = $qrcode_data['prefix'].$qrcode_data['qrcode'];
        
        $this->splash('success',null,array('qrcode'=>$qrcode));
    }




}
