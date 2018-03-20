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


class fastgroup_ctl_site_fastgroup extends site_controller
{
    public $title = '快团';
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function subject($subject_id, $product_id)
    {
        if (!$subject_id) {
            vmc::singleton('mobile_router')->http_status(404);
        }
        $mdl_subject = $this->app->model('subject');
        $subject = $mdl_subject->dump($subject_id);
        if (!$subject || $subject['is_pub'] != 'true') {
            vmc::singleton('mobile_router')->http_status(404);
        }
        $this->title = $subject['fg_title'];
        $this->description = $subject['fg_intro'];
        if (!$product_id) {
            $pid = 'g'.$subject['goods_id'];
        } else {
            $pid = $product_id;
        }
        $goods_detail = vmc::singleton('b2c_goods_stage')->detail($pid, $msg); //引用传递
        $this->pagedata['subject'] = $subject;
        $this->pagedata['goods_detail'] = $goods_detail;

        $this->pagedata['m_url'] = app::get('mobile')->router()->gen_url(array(
            'app'=>'fastgroup',
            'ctl'=>'mobile_fastgroup',
            'act'=>'subject',
            'args'=>array($subject_id),
            'full'=>'1'
        ));
        
        $this->page('site/subject/detail.html');
    }


}
