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

class vmcconnect_tasks_queue_warning extends vmcconnect_tasks_base implements base_interface_task{

    public function exec($params = null) {
        //队列执行后开始执行逻辑代码
        //①向该hook_key的设置号码发送短信
        //如果不存在预警电话，则不做下列操作
        if (!$params['alert_phone'] || !preg_match("/^1[34578]{1}\d{9}$/", $params['alert_phone'])) return true;

        //发送短信
        $mobile['mobile'] = $params['alert_phone'];      //18261018945
        $title = app::get('site')->getConf('site_name');    //YOUR SHOP NAME
        $content = $params['type']."任务'".$params['worker']."'执行失败";   //短信主要内容
//        $type = 'signup';

        $sms_obj = vmc::singleton('vmcconnect_messenger_sms');
        $res = $sms_obj->send($mobile, $title, $content);

        if (!$res) {
            $this->splash('error', null, '短信发送失败');
        }

        return $this->_exec_task($params);
    }

}