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

class couponactivity_ctl_site_coupons extends b2c_frontpage
{
    public $title = '优惠券活动';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->obj_coupon = vmc::singleton('couponactivity_coupon_stage');
    }

    public function index($id = 0)
    {
        if ($id>0) {
            $this->pagedata['data_list'] = $this->obj_coupon->activity(array('activity_id'=>$id));
            $this->title .= '-'.$this->pagedata['data_list']['activity']['name'];
        }else{
            $params = vmc::singleton('base_component_request')->get_params(true);
            $params = utils::_filter_input($params);
            $page = array(
                'size'=> $params['size']>0?$params['size']:100,
                'index'=> $params['index']>0?$params['index']:0,
            );
            $filter = array(
                'status' =>  'true',
                'from_time|sthan'=>time(),
                'to_time|than'=>time()
                );
            $mdl_activity = $this->app->model('activity');
            $list = $mdl_activity->getList('*', $filter, $page['index']*$page['size'], $page['size'], 'op_time ASC');

            $this->pagedata['data_list'] = $list;
            $this->pagedata['count'] = count($list);
            $this->pagedata['all_count'] = $mdl_activity->count($filter);
            $this->pagedata['pager'] = array(
                'total' => ($this->pagedata['all_count'] ? ceil($this->pagedata['all_count'] / $page['size']) : 1) ,
                'current' => intval($page['index']),
            );
            $this->pagedata['pager']['token'] = time();
            $this->pagedata['pager']['link'] = $this->gen_url(array(
                'app' => 'couponactivity',
                'ctl' => 'site_coupons',
                'act' => 'index',
                'full' => 1,
            )).'?page='.$this->pagedata['pager']['token'];
        }

        $this->page('site/coupons/index.html');
        
    }


}
