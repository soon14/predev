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


class ectools_mdl_payment_applications {
    function __construct(&$app) {
        $this->app = $app;
    }
    public function getList($cols = '*', $filter = false) {
        foreach (vmc::servicelist('ectools_payment.applications') as $class_name => $app_ins) {
            $setting = $app_ins->setting();
            $class_arr = explode('_', $class_name);
            $conf = unserialize(app::get('ectools')->getConf($class_name));
            foreach ($setting as $key => $item) {
                if (!$conf || empty($conf[$key])) {
                    $conf[$key] = $item['default'];
                }
            }
            $row = array(
                'name' => $app_ins->name,
                'version' => $app_ins->version,
                'platform_allow' => $app_ins->platform_allow,
                'app_id' => array_pop($class_arr) , //app_id 即class 文件名
                'display_name' => $conf['display_name'],
                'order_num' => $conf['order_num'] ? $conf['order_num'] : 0,
                'app_class' => $class_name,
                'description' => $conf['description'],
                'pay_fee' => $conf['pay_fee'],
                'status' => $conf['status']
            );
            $flag = true;
            if ($filter && is_array($filter)) {
                foreach ($filter as $key => $value) {
                    if ($row[$key]) {
                        $flag = ($flag && ((is_array($row[$key]) && array_intersect($row[$key], $value)) || $row[$key] == $value));
                    }
                }
            }
            if ($flag) $list[] = $row;
        }
        $tmp_list = array();
        $index = 0;
        if($list){
            foreach ($list as $key => $value) {
                if ($filter && $filter['app_id'] != 'cod' && $value['app_id'] == 'cod') {
                    unset($list[$key]);
                    continue;
                }
                $index = $value['order_num'];
                while (true) {
                    if (!isset($tmp_list[$index])) break;
                    $index++;
                }
                $tmp_list[$index] = $value;
            }
        }
        ksort($tmp_list);
        return array_values($tmp_list);
    }
    //得到指定支付方式信息
    public function dump($pay_app_id) {
        if (!$pay_app_id) return false;
        $pay_app = $this->getList('*', array(
            'app_id' => $pay_app_id
        ));
        return current($pay_app);
    }
}
