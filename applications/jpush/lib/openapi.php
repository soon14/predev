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


class jpush_openapi extends base_openapi
{
    //绑定推送服务客户端ID到会员
    public function bind_registration($params)
    {
        $hybirdapp_agent = base_mobiledetect::is_hybirdapp();
        if (!$hybirdapp_agent) {
            $this->failure('error agent');
        }
        $registration_id = $params['id'];
        if (!$registration_id) {
            $this->failure('registration_id not found');
        }
        $agent_obj = new Agent(); //Agent class in base static
        $device = $agent_obj->device();
        $platform = $agent_obj->platform();
        $reg_data = array(
            'id'=>$registration_id,
            'platform' => strtolower($hybirdapp_agent),
            'platform_version' => $agent_obj->version($platform),
            'device' => $device,
            'device_version' => $agent_obj->version($device),
            'createtime' => time(),
        );
        $current_member = vmc::singleton('b2c_user_object')->get_current_member();
        if ($current_member && $current_member['member_id']) {
            $reg_data['member_id'] = $current_member['member_id'];
        }
        $mdl_registration = app::get('jpush')->model('registration');
        if ($mdl_registration->save($reg_data)) {
            $this->success('bind success');
        } else {
            $this->failure('save failure');
        }
    }
}
