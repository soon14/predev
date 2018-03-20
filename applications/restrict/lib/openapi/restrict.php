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


class restrict_openapi_restrict extends base_openapi
{
    private $req_params = array();

    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    /*
     * 获取限购信息
     * */
    public function  get_restrict () {
        if(!$this->req_params['sku']) {
            $this->failure('缺少必填参数');
        }
        if(!$products = app::get('b2c')->model('products')->getList('*',array('bn'=>$this->req_params['sku']))) {
            $this->failure('未知商品');
        };
        if(count($products) > 1) {
            //扩展购物车页面购物车显示限购数量
            foreach(vmc::servicelist('restrict.check') as $obj) {
                if(method_exists($obj,'get_list')) {
                    if($restrict = $obj->get_list($products,$msg)) {
                        $this->success($restrict);
                    }else{
                        $this->success('未限购');
                    }
                }
            }
        }else{
            foreach(vmc::servicelist('restrict.check') as $obj) {
                if(method_exists($obj,'get_row')) {
                    if($restrict = $obj->get_row($products[0],$msg)) {
                        $this->success($restrict);
                    }else{
                        $this->success('未限购');
                    }
                }
            }
        }

        $this->success('未限购');
    }


}