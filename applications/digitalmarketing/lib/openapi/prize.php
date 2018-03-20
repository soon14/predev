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


class digitalmarketing_openapi_prize extends base_openapi {

    private $req_params = array();

    public function __construct() {
        $this->_request = vmc::singleton('base_component_request');
        $this->req_params = $this->_request->get_params(true);
    }

    public function activity() {
        $params = $this->req_params;
        if (!$params['ids']) {
            $this->failure('缺少参数', '02');
        }
        $filter = array('activity_id' => explode(',', $params['ids']));
        $activity_list = app::get('prize')->model('activity')->getList('*', $filter);
        $this->success($activity_list);
    }

    public function win() {
        $params = $this->req_params;
        $member_id = $this->check_login();
        if (!$params['activity_id']) {
            $this->failure('缺少参数', '02');
        }
        $db = vmc::database();
        $db->beginTransaction();
        $prize_service = vmc::singleton('digitalmarketing_prize');
        if (!$activity = $prize_service->check_activity($member_id, $params['activity_id'], $msg)) {
            $db->rollback();
            $this->failure($msg, '03');
        }
        $prize = $prize_service->get_prize($activity);
        if (!$partin = $prize_service->prize_log($member_id, $prize, $activity)) {
            $db->rollback();
            $this->failure('系统记录错误', '04');
        }
        $prize['prize_name'] = $this->_prize_name($prize['prize_grade']);
        $db->commit();
        $this->success(array('prize' => $prize, 'partin' => $partin));
    }

    //发放奖品
    public function award() {
        $params = $this->req_params;
        $member_id = $this->check_login();
        if (!$params['partin_id']) {
            $this->failure('缺少参数', '02');
        }
        $db = vmc::database();
        $db->beginTransaction();
        $prize_service = vmc::singleton('digitalmarketing_prize');
        if (!$prize_service->award($member_id, $params['partin_id'], $params['addr_id'], $msg, $win)) {
            $db->rollback();
            $this->failure($msg, '03');
        }
        $db->commit();
        $this->success(array(
            'msg' => '兑换成功',
            'win' => $win,
        ));
    }

    private function check_login() {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->failure('请先登录', '01');
        }
        return $member_id;
    }

    /**
     * @param $msg
     * @param $code
     */
    protected function failure($msg, $code) {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result' => 'failure',
            'data' => array(),
            'msg' => $msg,
            'code' => $code
        ));
        exit;
    }

    protected function _prize_name($grade) {
        $prize_names = array(
            1 => '一等奖',
            2 => '二等奖',
            3 => '三等奖',
            4 => '普通奖',
        );
        return $prize_names[$grade];
    }

    //
    public function actives() {
        $activity = app::get('digitalmarketing')->model('activity');
        $currTime = time();
        $filter = array(
            'from_time|sthan' => $currTime,
            'to_time|bthan' => $currTime,
        );
        
        $_schema = app::get('digitalmarketing')->model('activity')->get_schema();
        $activity_type = $_schema['columns']['type']['type'];
        $res = array();
        foreach($activity_type as $k => $v){
            $res[$k] = array(
                'name' => $v,
                'items' => array(),
            );
        }

        $actives = $activity->getList('activity_id, type, title', $filter);
        if (!$actives) {
            $this->success($res);
        }
        foreach ($actives as $_v) {
            $res[$_v['type']]['items'][] = $_v;
        }
        $this->success($res);
    }

}
