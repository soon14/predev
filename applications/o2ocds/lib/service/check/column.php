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
class o2ocds_service_check_column
{
    public function check_column_value($params, &$mgs)
    {
        if(app::get('o2ocds')->getConf('mode') == 1){
            if ($params['o2ocds_value']['first'] && !is_numeric($params['o2ocds_value']['first'])) {
                $mgs = '一级分佣[' . $params['o2ocds_value']['first'] . ']填写有误';

                return false;
            }
            if ($params['o2ocds_value']['second'] && !is_numeric($params['o2ocds_value']['second'])) {
                $mgs = '二级分佣[' . $params['o2ocds_value']['second'] . ']填写有误';

                return false;
            }
        }
        if(app::get('o2ocds')->getConf('mode') == 2){
            foreach($params['lv_o2ocds_value'] as $k => $v){
                if ($v && !is_numeric($v)) {
                    $mgs = "$v".'填写有误';

                    return false;
                }
            }

        }

        return true;
    }
}