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
class ectools_barcode_api extends base_openapi{

    private $req_params = array();

    public function __construct($http = true)
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);

    }
    public function encode($params){
        $this->req_params = array_merge( $this->req_params ,$params);
        $option =array(
            'x' => $this->req_params['x']?$this->req_params['x']:150,
        );
        vmc::singleton('ectools_barcode_show') ->get($this->req_params['text'] ,'code128' ,$option);
    }
}