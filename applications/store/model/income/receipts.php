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


class store_mdl_income_receipts extends dbeav_model
{
    /**
     * 获取唯一的损益单编号
     *
     * @return string 唯一的损益单编号
     */
    public function get_unique_income_receipts_bn()
    {
        $unique_income_receipts_bn = md5(uniqid('income_receipts_bn', true));

        //查询在数据库是否已经存在,不存在就返回这个损益单编号
        $count = $this->count(['income_receipts_bn' => $unique_income_receipts_bn]);
        if ($count == 0) {

            return $unique_income_receipts_bn;
        }

        //已经存在,重新生成
        $this->get_unique_income_receipts_bn();
    }

    /**
     * 修改损益单损益人字段显示损益人账户名
     *
     * @param $col
     * @param $income_receipts_info
     *
     * @return string
     */
    public function modifier_incomer_id($col, $income_receipts_info)
    {
        $model_desktop_users = app::get('desktop')->model('users');
        $desktop_user_info = $model_desktop_users->getRow('name', ['user_id' => $income_receipts_info['incomer_id']]);

        return $desktop_user_info['name'];
    }
}//End Class
