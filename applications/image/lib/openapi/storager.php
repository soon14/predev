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


class image_openapi_storager extends base_openapi
{
    private $req_params = array();
    public function __construct()
    {
            $this->req_params = vmc::singleton('base_component_request')->get_params(true);

    }
    public function o(){
        $images_arr = $this->req_params['images'];
        $images = $this->images_storage($images_arr);
        $this->success($images);
    }
    public function l(){
        $images_arr = $this->req_params['images'];
        $images = $this->images_storage($images_arr,'l');
        $this->success($images);
    }
    public function m(){
        $images_arr = $this->req_params['images'];
        $images = $this->images_storage($images_arr,'m');
        $this->success($images);
    }
    public function s(){
        $images_arr = $this->req_params['images'];
        $images = $this->images_storage($images_arr,'s');
        $this->success($images);
    }
    public function xs(){
        $images_arr = $this->req_params['images'];
        $images = $this->images_storage($images_arr,'xs');
        $this->success($images);
    }
    private function images_storage($images,$size){
        if(!is_array($images)){
            $images = explode(',',$images);
        }
        foreach ($images as $key => $value) {
            $_images[] = base_storager::modifier($value,$size);
        }
        $images_str = base_storager::image_storage(implode('_SPLIT_',$_images));
        return explode('_SPLIT_',$images_str);
    }

}
