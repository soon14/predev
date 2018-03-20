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
class commission_service_check_column
{
    public function check_column_value($params, &$mgs)
    {
        if(app::get('commission')->getConf('mode') == 1){
            if ($params['commission_value']['first'] && !is_numeric($params['commission_value']['first'])) {
                $mgs = '一级分佣[' . $params['commission_value']['first'] . ']填写有误';

                return false;
            }
            if ($params['commission_value']['second'] && !is_numeric($params['commission_value']['second'])) {
                $mgs = '二级分佣[' . $params['commission_value']['second'] . ']填写有误';

                return false;
            }
        }
        if(app::get('commission')->getConf('mode') == 2){
            foreach($params['lv_commission_value'] as $k => $v){
                if ($v && !is_numeric($v)) {
                    $mgs = "$v".'填写有误';

                    return false;
                }
            }

        }

        return true;
    }
}