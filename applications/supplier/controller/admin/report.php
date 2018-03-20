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

class supplier_ctl_admin_report extends desktop_controller
{
    public function index()
    {
        $this->page('admin/report.html');
    }
    public function export()
    {

        $object = $_POST['export_object'];
        switch ($object) {
            case 'order':
            case 'statement':
            case 'voucher':
                break;
            default:
               die('ERROR EXPORT OBJECT');
                break;
        }
        $filter = $_POST['filter'];
        foreach ($filter as $key => $value) {
            if (!$value || $value == '') {
                unset($filter[$key]);
            }
            switch ($key) {
                case 'createtime|lthan':
                case 'createtime|bthan':
                    if (!is_numeric($value)) {
                        $filter[$key] = strtotime($value);
                    }
                    break;
                case 'status':
                case 'supplier_bn':
                $value = explode(',',$value);
                if($value[1]){
                    $filter[$key] = $value;
                }
                    break;
                default:
                    # code...
                    break;
            }
        }
        
        if (empty($filter['createtime|lthan']) || empty($filter['createtime|bthan'])) {
            die('无效的时间段1[必须限定时间段,并且时间跨度不能大于3个月]');
        }
        if ($filter['createtime|bthan'] > $filter['createtime|lthan']) {
            die('无效的时间段2[必须限定时间段,并且时间跨度不能大于3个月]');
        }
        if (($filter['createtime|lthan'] - $filter['createtime|bthan']) > (3600 * 24 * 100)) {
            die('无效的时间段3[必须限定时间段,并且时间跨度不能大于3个月]');
        }

        vmc::singleton('supplier_export_'.$object)->doexport($filter);
    }
}
