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
class commission_openapi_products
{
    private $req_params = array();

    public function __construct($http = true)
    {
        if ($http) {
            header('Content-Type:application/json; charset=utf-8');
            $this->req_params = vmc::singleton('base_component_request')->get_params(true);
        }
    }
    /*
     * 每件商品可产生的佣金
     */
    public function commission(){
        $member = vmc::singleton('b2c_user_object')->get_current_member();
        if(!$member){
            return false;
        }
        $req = $this->req_params;
        $commission = app::get('commission') ->model('products_extend') ->get_expect_commission($req['product_id'] ,$member);
        $commission = $commission * (app::get('commission')->getConf('exchange'));
        $this ->_success($commission);
    }
    /*
     * 批量查询
     */
    public function commission_more(){
        $member = vmc::singleton('b2c_user_object')->get_current_member();
        if(!$member){
            return false;
        }
        $req = $this->req_params;
        $products = explode(',' , $req['s']);
        $result = array();
        foreach($products as $v){
            $result[$v] = app::get('commission') ->model('products_extend') ->get_expect_commission($v ,$member);
            $result[$v] = $result[$v] * (app::get('commission')->getConf('exchange'));
        }
        $this ->_success($result);
    }

    private function _success($data)
    {
        echo json_encode(array(
            'result' => 'success',
            'data' => $data,
        ));
        exit;
    }

    private function _failure($msg)
    {
        echo json_encode(array(
            'result' => 'failure',
            'data' => [],
            'msg' => $msg,
        ));
        exit;
    }
}