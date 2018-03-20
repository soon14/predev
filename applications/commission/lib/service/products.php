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
class commission_service_products
{
    public function __construct()
    {
        $this->tb_prefix = vmc::database()->prefix;
    }

    /*
     * 同步products表到commission_products_extend
     */
    public function exec($goods ,&$msg='')
    {
        $SQL_copy = "INSERT INTO `{$this ->tb_prefix}commission_products_extend`(`product_id`, `sku_bn` ,`goods_id`,`title`)
                     SELECT `product_id`,`bn`,`goods_id`,CONCAT(`name`,' ',IFNULL(`spec_info`,''))
                     FROM `{$this ->tb_prefix}b2c_products` WHERE `{$this ->tb_prefix}b2c_products`.`product_id`
                     NOT IN (SELECT `product_id` FROM `{$this ->tb_prefix}commission_products_extend`)";
        $SQL_clean = "DELETE FROM `{$this ->tb_prefix}commission_products_extend`
                     WHERE `{$this ->tb_prefix}commission_products_extend`.`product_id` NOT IN
                     (SELECT `product_id` FROM `{$this ->tb_prefix}b2c_products`)";
        $re1 = vmc::database()->exec($SQL_copy);
        $re2 = vmc::database()->exec($SQL_clean);
        if (!$re1 || !$re2) {
            $msg = '异常';

            return false;
        }
        $msg = 'commission_products同步完成';

        return true;
    }
}