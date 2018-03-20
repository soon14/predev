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
class digitalmarketing_ctl_mobile_activity extends b2c_mfrontpage {

    protected $activity, $prize, $partin, $addrs;
    protected $ver = 'vue'; // def vue
    protected $isWx = false, $noTheme = false;

    public function __construct($app) {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        $this->member = $this->get_current_member();
        $this->pagedata['app_id'] = $this->app->app_id;
        $this->pagedata['pagetime'] = time();
        
        !strlen($this->ver) && $this->ver = 'def';
        $this->pagedata['ver'] = $this->ver;
        $this->isWx = base_component_request::is_wxapp();
        $this->noTheme = $this->isWx ? false : true;
    }

    // CTRL 默认
    public function index() {
        
    }

    // CTRL 详情
    public function detail($id) {

        $this->_get_activity($id);

        $this->pagedata['data'] = $this->activity;
        if ($this->member) {
            $this->pagedata['chance'] = vmc::singleton('digitalmarketing_prize')->get_chance($this->member['member_id'], $id);
            $member_score = app::get('b2c')->model('member_integral')->amount($this->member['member_id']);
            $this->member['score'] = $member_score;
            $this->pagedata['member'] = $this->member;
        }

        $this->title = $this->activity['title'];
        $this->keywords = $this->activity['title'];
        $this->description = $this->activity['title'];

        $this->pagedata['title'] = $this->pagedata['keywords'] = $this->pagedata['description'] = $this->activity['title'];


        $view_tpl = null;
        switch ($this->activity['type']) {
            case 1:
                $view_tpl = 'dzp'; // 大转盘
                break;
            case 2:
                $view_tpl = 'sgj'; // 水果机
                break;
            case 3:
                $view_tpl = 'ggl'; // 刮刮卡
                break;
            case 4:
                $view_tpl = 'yyy'; // 摇一摇
                break;
        }
        $this->page('mobile/activity/' . ($this->ver != 'def' ? ('__' . $this->ver . '/') : '') . 'types/' . $view_tpl . '.html', $this->noTheme);
    }

    // CTRL ajax 交互
    public function ajax() {
        $_params = $this->_request->get_params(true);
        $activity_id = $_params && isset($_params['activity_id']) ? (int) $_params['activity_id'] : null;
        $type = $_params && isset($_params['type']) ? $_params['type'] : null;
        
        $this->_get_activity($activity_id);

        if (!$activity_id || !$this->activity || !$type) return false;

        $this->pagedata['data'] = $this->activity;

        $_method = '_ajax_' . $type;
        method_exists($this, $_method) && $this->$_method();
    }

    // ajax 获取当前信息
    protected function _ajax_detail() {

        $_json = $this->activity;

        if ($this->activity['prize_items']) {
            $items = array();
            foreach ($this->activity['prize_items'] as $_k => $_v) {
                $items[] = array(
                    'text' => $_v,
                );
            }
            $_json['items'] = $items;
        }

        $chance = vmc::singleton('digitalmarketing_prize')->get_chance($this->member['member_id'], $this->activity['activity_id']);

        $num = $this->activity['frequency_limit'] - $chance['today_nums'];
        $num < 0 && $num = 0;

        $_json['chance_num'] = $num;

        echo json_encode($_json);
        exit;
    }

    // ajax 获取规则
    protected function _ajax_rule() {
        $this->page('mobile/activity/rule.html', $this->noTheme);
    }

    // ajax 获取可抽奖次数
    protected function _ajax_num() {
        if (!$this->activity['frequency_limit']) {
            exit;
        }

        $chance = vmc::singleton('digitalmarketing_prize')->get_chance($this->member['member_id'], $this->activity['activity_id']);

        $num = $this->activity['frequency_limit'] - $chance['today_nums'];
        $num < 0 && $num = 0;
        echo $num;
        exit;
    }

    // ajax 获取奖品信息
    protected function _ajax_prize_items() {
        $items = array();
        if (!$this->activity['prize_items']) return false;
        foreach ($this->activity['prize_items'] as $_k => $_v) {
            $items[] = array(
                'text' => $_v,
            );
        }
        echo json_encode($items);
        exit;
    }

    // ajax 获取抽奖结果
    protected function _ajax_lottery() {
        $_params = $this->_request->get_params(true);
        $partin_id = $_params && isset($_params['partin_id']) ? (int) $_params['partin_id'] : null;

        $partin_id && $this->partin = $this->app->model('partin')->dump(array(
            'partin_id' => $partin_id,
            'activity_id' => $this->activity['activity_id'],
            'member_id' => $this->member['member_id'],
        ));
        $this->partin && $this->prize = $this->app->model('prize')->dump(array(
            'prize_id' => $this->partin['prize_id'],
            'activity_id' => $this->activity['activity_id'],
        ));

        $this->pagedata['partin'] = $this->partin;
        $this->pagedata['prize'] = $this->prize;

        if (!$partin_id || !$this->partin || !$this->prize) {
            return $this->_on_lose();
        } else {
            return $this->_on_lottery();
        }
    }

    // -------- 抽奖结果
    protected function _on_lose() {
        $this->page('mobile/activity/lottery/lose.html', $this->noTheme);
    }

    // 未中奖
    protected function _on_lottery() {
        $_type = trim($this->prize['prize_type']);
        $_method = '_on_' . $_type;
        method_exists($this, $_method) && $this->$_method();
    }

    // 积分
    protected function _on_score() {
        $this->page('mobile/activity/lottery/score.html', $this->noTheme);
    }

    // 优惠券
    protected function _on_coupon() {
        $this->pagedata['coupon'] = app::get('b2c')->model('coupons')->getRow('*', array('cpns_id' => $this->prize['addon']['coupon']));

        $this->page('mobile/activity/lottery/coupon.html', $this->noTheme);
    }

    // 赠品
    protected function _on_product() {
        $this->pagedata['product'] = app::get('b2c')->model('products')->getRow('*', array('product_id' => $this->prize['addon']['product']));
        $this->page('mobile/activity/lottery/product.html', $this->noTheme);
    }

    // CTRL 地址操作
    public function addrs() {
        $_params = $this->_request->get_params(true);
        $partin_id = $_params && isset($_params['partin_id']) ? (int) $_params['partin_id'] : null;

        $this->partin = $this->app->model('partin')->dump(array(
            'partin_id' => $partin_id,
            'member_id' => $this->member['member_id'],
        ));
        $this->partin && $this->_get_activity($this->partin['activity_id']);
        $this->partin && $this->activity && $this->prize = $this->app->model('prize')->dump(array(
            'prize_id' => $this->partin['prize_id'],
            'activity_id' => $this->activity['activity_id'],
        ));

        if (!$this->partin || !$this->activity || !$this->prize || $this->prize['prize_type'] != 'product')
                return false;

        $this->pagedata['data'] = $this->activity;
        $this->pagedata['partin'] = $this->partin;
        $this->pagedata['prize'] = $this->prize;

        $type = $_params && isset($_params['type']) ? $_params['type'] : 'sel';
        !$type && $type = 'sel';
        $_method = '_addr_' . $type;
        method_exists($this, $_method) && $this->$_method();
    }

    // 选择地址
    protected function _addr_sel() {
        $this->pagedata['title'] = '选择收货地址';
        $this->ver != 'vue' && $this->_get_addrs();
        $this->page('mobile/activity/' . ($this->ver != 'def' ? ('__' . $this->ver . '/') : '') . 'addr/sel.html', $this->noTheme);
    }

    // 选择地址
    protected function _addr_list() {
        $_vh = vmc::singleton('base_view_helper');
        $this->_get_addrs();
        if ($this->addrs) {
            foreach ($this->addrs as $_k => $_v) {
                $this->addrs[$_k]['region_area'] = $_vh->modifier_region($_v['area']);
            }
        }
        echo json_encode($this->addrs);
        exit;
    }

    // 新建地址
    protected function _addr_new() {
        $this->pagedata['title'] = '添加收货地址';
        $mdl_maddr = app::get('b2c')->model('member_addrs');

        $redirect = array(
            'app' => $this->app->app_id,
            'ctl' => 'mobile_activity',
            'act' => 'addrs',
        );

        $toUrl = $this->gen_url($redirect) . '?type=sel&partin_id=' . $this->partin['partin_id'];

        if ($_POST) {

            $addr = $_POST['maddr'];
            $addr['member_id'] = $this->member['member_id'];
            $addr_area_name = $_POST['addr_area_name'];
            $addr_area_val = $_POST['addr_area_val'];
            if (!$addr_area_name || !$addr_area_val)
                    $this->splash('error', '', '情选择地区');
            $addr['area'] = 'mainland:' . implode('/', explode(',', $addr_area_name)) . ':' . array_pop(explode(',', $addr_area_val));

            $save = $mdl_maddr->save($addr);
            $this->pagedata['addr'] = $addr;
            if (!$save) {
                $this->splash('error', '', '保存失败');
            }

            !$this->isWx && $this->redirect($toUrl);
        }

        $this->page('mobile/activity/' . ($this->ver != 'def' ? ('__' . $this->ver . '/') : '') . 'addr/new.html', $this->noTheme);
    }

    // 赠品获取成功
    protected function _addr_success() {
        $this->pagedata['title'] = '赠品';

        $_params = $this->_request->get_params(true);
        $win_id = $_params && isset($_params['win_id']) ? (int) $_params['win_id'] : null;
        $win = $this->app->model('win')->dump(array(
            'win_id' => $win_id,
            'partin_id' => $this->partin['partin_id'],
            'member_id' => $this->member['member_id'],
        ));
        if (!$win) return;

        $order = app::get('b2c')->model('orders')->dump(array(
            'order_id' => $win['order_id']
        ));

        $this->pagedata['product'] = app::get('b2c')->model('products')->getRow('*', array('product_id' => $this->prize['addon']['product']));
        $this->pagedata['win'] = $win;
        $this->pagedata['order'] = $order;
        $this->page('mobile/activity/' . ($this->ver != 'def' ? ('__' . $this->ver . '/') : '') . 'addr/success.html', $this->noTheme);
    }

    // 获取活动信息
    protected function _get_activity($id) {
        $this->activity = $this->app->model('activity')->dump($id);
        $this->activity['prize'] = $this->app->model('activity')->getList('*', array('activity_id' => $id, 'nums|than' => 0));
        $this->activity['member_lv'] = explode(',', $this->activity['member_lv']);
        $this->activity['prize'] = utils::array_change_key($this->activity['prize'], 'prize_grade');
        foreach ($this->activity['prize'] as &$v) {
            if ($v['prize_type'] == 'coupon') {
                $v['item'] = app::get('b2c')->model('coupons')->getRow('*', array('cpns_id' => $v['addon']['coupon']));
            } elseif ($v['prize_type'] == 'product') {
                $v['item'] = app::get('b2c')->model('products')->getRow('*', array('product_id' => $v['addon']['product']));
            }
        }

        // $this->activity['type'] = 4;
        $this->_re_data($this->activity);
    }

    // 获取地址列表
    protected function _get_addrs() {
        $mdl_maddr = app::get('b2c')->model('member_addrs');
        $member_id = $this->member['member_id'];
        $this->addrs = $mdl_maddr->getList('*', array('member_id' => $member_id));
        $this->pagedata['list'] = $this->addrs;
    }

    // 重组抽奖奖品数据
    protected function _re_data(&$activity) {
        if (!$activity) return false;
        switch ($activity['type']) {
            case 1:
                $this->__re_dzp($activity);
                break;
            case 2:
                $this->__re_sgj($activity);
                break;
            case 3:
                $this->__re_ggl($activity);
                break;
            case 4:
                $this->__re_yyy($activity);
                break;
        }
    }

    // 大转盘
    protected function __re_dzp(&$activity) {
        $prize_names = array(
            '1' => '一等奖',
            '2' => '二等奖',
            '3' => '三等奖',
            '4' => '普通奖',
        );

        $rows = $this->app->model('prize')->getList('*', null, 0, -1, 'prize_grade asc');
        $prizes = array();

        $prize_items = array(
            0 => '谢谢',
            1 => '谢谢',
            2 => '谢谢',
            3 => '谢谢',
            4 => '谢谢',
            5 => '谢谢',
            6 => '谢谢',
            7 => '谢谢',
            8 => '谢谢',
            9 => '谢谢',
        );

        if ($rows) {
            foreach ($rows as $_v) {
                $_v['name'] = $prize_names[$_v['prize_grade']];
                $prize_items[($_v['prize_grade'] * 2)] = $_v['name'];
                $prizes[] = $_v;
            }
        }
        $activity['prize_items'] = $prize_items;
    }

    // 水果机
    protected function __re_sgj(&$activity) {
        $prize_names = array(
            '1' => 'pingguo.png',
            '2' => 'putao.png',
            '3' => 'shiliu.png',
            '4' => 'youzi.png',
        );

        $rows = $this->app->model('prize')->getList('*', null, 0, -1, 'prize_grade asc');
        $prizes = array();

        // pingguo putao shiliu youzi
        $prize_items = array(
            0 => 'ningmeng.png',
            1 => 'xiangjiao.png',
            2 => 'ningmeng.png',
            3 => 'ningmeng.png',
            4 => 'ningmeng.png',
            5 => 'xiangjiao.png',
            6 => 'xiangjiao.png',
            7 => 'ningmeng.png',
            8 => 'xiangjiao.png',
            9 => 'xiangjiao.png',
            10 => 'ningmeng.png',
            11 => 'xiangjiao.png',
        );

        if ($rows) {
            foreach ($rows as $_v) {
                $_v['name'] = $prize_names[$_v['prize_grade']];
                $prize_items[($_v['prize_grade'] * 2)] = $_v['name'];
                $prizes[] = $_v;
            }
        }
        $activity['prize_items'] = $prize_items;
    }

    // 刮刮乐
    protected function __re_ggl(&$activity) {
        
    }

    // 摇一摇
    protected function __re_yyy(&$activity) {
        
    }

}
