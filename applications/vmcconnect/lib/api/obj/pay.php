<?php

/**
 * Created by PhpStorm.
 * User: Ganxiaohui
 * Date: 2017/6/22
 * Time: 19:23
 */
class vmcconnect_api_obj_pay extends vmcconnect_api_obj_base
{

    protected $_fields = '';

    /*
     * 查询支付方式
     */
    public function read_get()
    {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';

        $mdl_pconf = app::get('ectools')->model('payment_applications');
        $data = $mdl_pconf->getList('*');

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }
}