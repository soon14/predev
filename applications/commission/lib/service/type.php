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
class commission_service_type
{
    public function __construct()
    {
        $this->tb_prefix = vmc::database()->prefix;
    }

    /*
     * 同步goods_type表到commission_type_extend
     */
    public function exec($goods ,&$msg='')
    {
        $SQL_copy = "INSERT INTO `{$this ->tb_prefix}commission_type_extend`(`type_id`,`name`) SELECT `type_id`,`name`
                     FROM `{$this ->tb_prefix}b2c_goods_type` WHERE `{$this ->tb_prefix}b2c_goods_type`.`type_id`
                     NOT IN (SELECT `type_id` FROM `{$this ->tb_prefix}commission_type_extend`)";
        $SQL_clean = "DELETE FROM `{$this ->tb_prefix}commission_type_extend`
                     WHERE `{$this ->tb_prefix}commission_type_extend`.`type_id` NOT IN
                     (SELECT `type_id` FROM `{$this ->tb_prefix}b2c_goods_type`)";
        $re1 = vmc::database()->exec($SQL_copy);
        $re2 = vmc::database()->exec($SQL_clean);
        if (!$re1 || !$re2) {
            $msg = '异常';

            return false;
        }
        $msg = 'commission_type同步完成';

        return true;
    }
}