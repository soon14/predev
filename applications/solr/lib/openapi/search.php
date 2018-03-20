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
class solr_openapi_search extends base_openapi{

    private $req_params = array();
    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    /**
     * /openapi/search/goods_auto/keyword/ip
     * 搜索框自动提示
     * @param array $req_params
     */
    public function goods_auto($req_params = array()){
        $req_params = array_merge($req_params ,$this->req_params);
        $keyword = $req_params['keyword'];
        if(!$keyword){
            $this ->failure('keyword is required');
        }
        $solr_stage = vmc::singleton('solr_stage');
        $result = $solr_stage->facet_suggest($keyword);
        $this ->success($result);
    }
}