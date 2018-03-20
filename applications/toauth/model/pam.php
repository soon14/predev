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


class toauth_mdl_pam
{
    public function __construct(&$app)
    {
        $this->app = $app;
    }
    public function getList($cols = '*', $filter = false)
    {
        foreach (vmc::servicelist('toauth.pam') as $class_name => $app_ins) {
            $setting = $app_ins->setting();
            if(!$app_ins->login_type){
                continue;
            }
            $auth_url = $app_ins->authorize_url();
            if(!$auth_url){
                continue;
            }
            $conf = unserialize($this->app->getConf($class_name));
            foreach ($setting as $key => $item) {
                if (!$conf || empty($conf[$key])) {
                    $conf[$key] = $item['default'];
                }
            }
            $row = array(
                'name' => $app_ins->name,
                'version' => $app_ins->version,
                'display_name' => $conf['display_name'],
                'order_num' => $conf['order_num'] ? $conf['order_num'] : 0,
                'pam_class' => $class_name,
                'login_type'=>$app_ins->login_type,
                'description' => $conf['description'],
                'authorize_url' => $app_ins->authorize_url(),
                'status' => $conf['status'],
            );
            $flag = true;
            if ($filter && is_array($filter)) {
                foreach ($filter as $key => $value) {
                    if ($row[$key]) {
                        $flag = ($flag && ((is_array($row[$key]) && array_intersect($row[$key], $value)) || $row[$key] == $value));
                    }
                }
            }
            if ($flag) {
                $list[] = $row;
            }
        }
        $tmp_list = array();
        $index = 0;
        foreach ($list as $key => $value) {
            $index = $value['order_num'];
            while (true) {
                if (!isset($tmp_list[$index])) {
                    break;
                }
                $index++;
            }
            $tmp_list[$index] = $value;
        }
        ksort($tmp_list);

        return array_values($tmp_list);
    }
    //得到指定第三方信任登录
    public function dump($pam_class)
    {
        if (!$pam_class) {
            return false;
        }
        $pam = $this->getList('*', array(
            'pam_class' => $pam_class,
        ));

        return current($pam);
    }
}
