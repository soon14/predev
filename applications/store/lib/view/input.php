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


class store_view_input
{
    /**
     * 模板渲染对象
     *
     * @var base_render
     */
    private $render;

    public function __construct()
    {
        $app = app::get('store');
        $this->render = new base_render($app);
    }

    /**
     * 店铺finder列表增加店铺筛选
     *
     * @param array $params input标签里的所有参数
     *
     * @return string 返回html字符串
     */
    public function input_goodsstore($params)
    {
        $mdl_store = app::get('store')->model('store');
        $store_condition = array();

        if (is_array($params['store_ids']) && count($params['store_ids']) > 0) {
            $store_condition['store_id'] = $params['store_ids'];
        }

        //查询所有的店铺信息
        $store_info = $mdl_store->getList('store_id, store_name', $store_condition);

        if (is_array($store_info) === false || count($store_info) === 0) {

            return '';
        }

        $params['storeInfos'] = $store_info;
        $params['store_domid'] = substr(md5(uniqid()), 0, 6);

        $this->render->pagedata['params'] = $params;

        return $this->render->fetch('admin/finder/goodsstore_filter.html');
    }

    /**
     * 店铺finder列表增加店铺id筛选
     *
     * @param array $params input标签里的所有参数
     *
     * @return string 返回html字符串
     */
    public function input_goodsstoreid($params)
    {
        $mdl_store = app::get('store')->model('store');
        $store_condition = array();

        if (is_array($params['store_ids']) && count($params['store_ids']) > 0) {
            $store_condition['store_id'] = $params['store_ids'];
        }

        //查询所有的店铺信息
        $store_info = $mdl_store->getList('store_id, store_name', $store_condition);

        if (is_array($store_info) === false || count($store_info) === 0) {

            return '';
        }

        $params['storeInfos'] = $store_info;
        $params['store_domid'] = substr(md5(uniqid()), 0, 6);

        $this->render->pagedata['params'] = $params;

        return $this->render->fetch('admin/finder/goodstoreid_filter.html');
    }

}
