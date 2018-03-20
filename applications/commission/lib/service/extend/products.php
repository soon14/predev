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
class commission_service_extend_products
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
    }

    /*
     * 扩展products表commission_value字段
     */
    public function extend_column(&$goods, &$msg)
    {
        $service_chk_column = vmc::singleton('commission_service_check_column');
        foreach ($goods['product'] as $k => $v) {
            if (array_key_exists("commission_value", $v) && $v['commission_value']) {
                $data = array(
                    'product_id' => $v['product_id'],
                    'goods_id' => $v['goods_id'],
                    'title' => $v['name'] . ' ' . $v['spec_info'],
                    'commission_value' => $v['commission_value']
                );
                if (false == $service_chk_column->check_column_value($data, $msg)) {
                    return false;
                }
                $re = $this->app->model('products_extend')->save($data);
                if (!$re) {
                    $msg = "异常";

                    return false;
                }
            }
        }

        return true;

    }
}