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


class vshop_ctl_mobile_shop extends vshop_mfrontpage
{
    public $title = '我的店铺';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $action_key = implode('_',array($this->app->app_id,$this->_request->get_ctl_name(),$this->_request->get_act_name()));
        if($action_key != 'vshop_mobile_shop_info'){
            $this->verify_vshop_member();
            $this->set_tmpl('vshop');
            $this->pagedata['vshop'] = $this->vshop;
            //$this->vshop   has data
        }
    }

    public function index()
    {
        $vshop = $this->vshop;
        $mdl_relorder = $this->app->model('relorder');
        $this->pagedata['count_tody_order'] = $mdl_relorder->count(
            array(
                'createtime|between' => array(strtotime(date('Y-m-d')), strtotime('now')),
                'shop_id'=>$this->vshop['shop_id']
            )
        );
        $mdl_voucher = $this->app->model('voucher');
        $mdl_notice = $this->app->model('notice');
        $count_voucher_subprice = $mdl_voucher->count_subprice($vshop['shop_id'], strtotime(date('Y-m-01 00:00:00')));
        $this->pagedata['count_voucher_subprice'] = ectools_cur::format($count_voucher_subprice);
        $this->pagedata['count_week_notice'] = $mdl_notice->count(array(
            'pubtime|than' => strtotime('-1 week'),
        ));
        $this->pagedata['date_month_first'] = date('Y-m-01');
        $this->page('mobile/default.html');
    }

    public function notice_list($page = 1)
    {
        $limit = 10;
        $mdl_notice = $this->app->model('notice');
        $notice_list = $mdl_notice->getList('*', array('ispub' => 'true'), ($page - 1) * $limit, $limit);
        $count = $mdl_notice->count(array('ispub' => 'true'));
        $this->pagedata['notice_list'] = $notice_list;
        $this->pagedata['count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['totalpage'] = ceil($count / $limit);
        $this->pagedata['pager'] = array(
            'total' => $this->pagedata['totalpage'] ,
            'current' => $page,
            'link' => $this->gen_url(array(
                'app' => 'vshop',
                'ctl' => 'site_vshop',
                'act' => 'notice_list',
                'args' => array(
                    ($token = time()),
                ),
            )),
            'token' => $token,
        );
        $this->display('mobile/default.html');
    }

    public function notice($notice_id)
    {
        $mdl_notice = $this->app->model('notice');
        $this->set_tmpl('vshop_notice');
        $notice = $mdl_notice->dump($notice_id);
        if ($notice['ispub'] == 'false') {
            $this->splash('error', array(
                'app' => 'vshop',
                'ctl' => 'site_vshop',
                'act' => 'index',
            ), '未发布的公告');
        }
        $this->pagedata['notice'] = $notice;
        $this->page('mobile/default.html');
    }

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
        $filter = $status_filter[$status];
        if ($_POST) {
            $filter = array_merge($filter, $_POST);
        }
        $mdl_relorder = $this->app->model('relorder');
        $mdl_order_items = app::get('b2c')->model('order_items');
        $order_list = $mdl_relorder->getRelOrderList($this->vshop['shop_id'], 'o.*', $filter, ($page - 1) * $limit, $limit, 'o.createtime DESC', $order_count);
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
                'app' => 'vshop',
                'ctl' => 'mobile_shop',
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

    public function order_detail($order_id)
    {
        $relorder = $this->app->model('relorder')->getRow('*', array(
            'order_id' => $order_id,
            'shop_id' => $this->vshop['shop_id'],
        ));
        if (!$relorder) {
            $this->splash('error');
        }
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

    public function voucher($page = 1, $limit = 10)
    {
        if ($limit > 100) {
            $limit = 100;
        }
        $mdl_voucher = $this->app->model('voucher');
        $filter_post = utils::_filter_input($_POST);
        $filter_get = utils::_filter_input($_GET);
        if (!is_array($filter_post)) {
            $filter_post = array();
        }
        if (!is_array($filter_get)) {
            $filter_get = array();
        }
        $filter = array_merge($filter_post, $filter_get);
        $filter['shop_id'] = $this->vshop['shop_id'];
        $this->pagedata['count_subprice'] = $mdl_voucher->count_subprice($this->vshop['shop_id'], $filter['from'], $filter['to'], $filter['status']);

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
        $voucher_list = $mdl_voucher->getList('*', $filter, ($page - 1) * $limit, $limit);
        $start_row = $mdl_voucher->getRow('createtime', array('shop_id' => $this->vshop['shop_id']), 'createtime ASC');
        $end_row = $mdl_voucher->getRow('createtime', array('shop_id' => $this->vshop['shop_id']), 'createtime DESC');
        $this->pagedata['se_start'] = date('Y-m-d', $start_row['createtime']);
        $this->pagedata['se_end'] = date('Y-m-d', $end_row['createtime']);
        if ($voucher_list) {
            $voucher_id_arr = array_keys(utils::array_change_key($voucher_list, 'voucher_id'));
            $mdl_voucher_items = $this->app->model('voucher_items');
            $voucher_items = $mdl_voucher_items->getList('*', array('voucher_id' => $voucher_id_arr));
            $this->pagedata['voucher_items'] = utils::array_change_key($voucher_items, 'voucher_id', true);
            $count = $mdl_voucher->count($filter);
            $this->pagedata['voucher_list'] = $voucher_list;
            $this->pagedata['count'] = $count;
            $this->pagedata['page'] = $page;
            $this->pagedata['totalpage'] = ceil($count / $limit);
            $pager_url = $this->gen_url(array(
                'app' => 'vshop',
                'ctl' => 'mobile_shop',
                'act' => 'voucher',
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
        $filter['shop_id'] = $this->vshop['shop_id'];
        $filter['status'] = 'succ';
        $statement_list = $mdl_statement->getList('*', $filter, ($page - 1) * $limit, $limit);
        $start_row = $mdl_statement->getRow('createtime', array('shop_id' => $this->vshop['shop_id']), 'createtime ASC');
        $end_row = $mdl_statement->getRow('createtime', array('shop_id' => $this->vshop['shop_id']), 'createtime DESC');
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
                    ($token = time()),
                    $limit,
                ),
            ));
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

    public function statement_detail($statement_id)
    {
        $mdl_statement = $this->app->model('statement');
        $filter['shop_id'] = $this->vshop['shop_id'];
        $filter['status'] = 'succ';
        $filter['statement_id'] = $statement_id;
        $statement = $mdl_statement->getRow('*', $filter);
        if ($statement) {
            $mdl_statement_index = $this->app->model('statement_index');
            $mdl_voucher = $this->app->model('voucher');
            $mdl_voucher_items = $this->app->model('voucher_items');
            $voucher_id_arr = $mdl_statement_index->getColumn('voucher_id', array('statement_id' => $statement_id));
            $voucher_list = $mdl_voucher->getList('*', array('voucher_id' => $voucher_id_arr));
            $voucher_list_items = $mdl_voucher_items->getList('*', array('voucher_id' => $voucher_id_arr));
            $voucher_list_items = utils::array_change_key($voucher_list_items, 'voucher_id', true);
            $this->pagedata['statement'] = $statement;
            $this->pagedata['voucher_list'] = $voucher_list;
            $this->pagedata['voucher_list_items'] = $voucher_list_items;
        }
        $this->page('mobile/default.html');
    }
    public function pickout($action = 'list')
    {
        if($this->vshop['status']!='active'){
            $this->splash('error');
        }
        $omath = vmc::singleton('ectools_math');
        switch ($action) {
            case 'list':
                $pids = explode(',', $_GET['pids']);
                if (empty($pids)) {
                    $this->splash('error');
                }
                $default_profit = app::get('vshop')->getConf('default_profit');//默认分润比例
                $coefficient = $this->vshop['lv_info']['coefficient'];//分润系数
                $mdl_products = app::get('b2c')->model('products');
                $mdl_relprofit = $this->app->model('relprofit');
                $mdl_pickout = $this->app->model('pickout');
                $products = $mdl_products->getList('product_id,goods_id', array('product_id' => $pids));
                $gids = array_keys(utils::array_change_key($products, 'goods_id'));
                $product_arr = utils::array_change_key($products, 'product_id');
                $pickout = $mdl_pickout->getColumn('goods_id', array('shop_id' => $this->vshop['shop_id'], 'goods_id' => $gids));
                $relprofit = $mdl_relprofit->getList('product_id,goods_id,share', array('product_id' => $pids));
                $relprofit = utils::array_change_key($relprofit, 'product_id');
                $_return = array();
                foreach ($pids as $pid) {
                    $profit = ($relprofit[$pid] ? $relprofit[$pid]['share'] : $default_profit);
                    $_return[$pid] = array(
                        'pickout' => in_array($product_arr[$pid]['goods_id'],$pickout),
                        'profit' => $omath->number_multiple(array(
                             $profit,
                             $coefficient,
                        )),
                        'vshop_id'=>$this->vshop['shop_id']
                    );
                }
                $this->splash('success', null, $_return);
                break;
            case 'save':
                $gids = explode(',', $_GET['gids']);
                if (empty($gids)) {
                    $this->splash('error');
                }
                $mdl_pickout = $this->app->model('pickout');
                foreach ($gids as $gid) {
                    $exist_pickout = $mdl_pickout->getRow('id', array('shop_id' => $this->vshop['shop_id'], 'goods_id' => $gid));
                    if ($exist_pickout) {
                        $mdl_pickout->delete($exist_pickout);
                    } else {
                        $new_pickout = array(
                            'shop_id' => $this->vshop['shop_id'],
                            'goods_id' => $gid,
                        );
                        $mdl_pickout->save($new_pickout);
                    }
                }
                $this->splash('success', null);
                break;
            default:
                $this->splash('error');
                break;
        }
    }
    public function info($action = 'eidt')
    {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if(!$member_id){
            $this->splash('error',null,'未知用户状态');
        }else{
            $mdl_vshop = $this->app->model('shop');
            $vshop = $mdl_vshop->getRow('*',array('member_id'=>$member_id));
            if($vshop){
                $mdl_vshop_lv = $this->app->model('lv');
                $vshop['lv_info'] = $mdl_vshop_lv->dump($vshop['shop_lv_id']);
                $vshop['logo_url'] = base_storager::image_path($vshop['logo'], 'm');
                $vshop['banner_url'] = base_storager::image_path($vshop['gallery_image_id'], 'l');
            }
        }
        if ($action == 'save') {
            $mdl_vshop = $this->app->model('shop');
            $vshop_params = $_POST['vshop'];
            $vshop_save_data = array(
                'name'=>$vshop_params['name'],
                'shopkeeper'=>$vshop_params['shopkeeper'],
                'logo'=>$vshop_params['logo'],
                'contacts_phone'=>$vshop_params['contacts_phone']
            );
            foreach ($vshop_save_data as $key => $value) {
                if(empty($value)){
                    $this->splash('error',null,'请完整填写信息');
                    break;
                }
            }
            if($vshop){
                $vshop = array_merge($vshop, $vshop_save_data);
            }else{
                $vshop_save_data['shop_id'] = $mdl_vshop->apply_id();
                $vshop_save_data['member_id'] = $member_id;
                $vshop_save_data['shop_lv_id'] = $this->app->model('lv')->get_default_lv();
                $vshop_save_data['createtime'] = time();
                $vshop = $vshop_save_data;
            }
            if ($mdl_vshop->save($vshop)) {
                $this->splash('success', null, '保存成功');
            } else {
                $this->splash('error', null, '保存失败');
            }
        } else {
            $this->pagedata['vshop'] = $vshop;
            $this->page('mobile/default.html');
        }
    }
}
