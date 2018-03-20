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


class wechat_openapi_xcxpage extends wechat_openapi {

    public function __construct() {
        parent::__construct();
    }

    // /openapi/xcxpage/activity?filter[id]=1...  &cols=
    public function activity($params = null) {
        $params = array_merge(($params ? $params : array()), ($this->params ? $this->params : array()));
        $cols = strtolower(trim(urldecode($params['cols'])));
        !strlen($cols) && $cols = '*';

        if ($cols != '*') {
            $col_arr = explode(',', $cols);
            $col_arr = array_map("trim", $col_arr);
            !in_array('id', $col_arr) && $col_arr[] = 'id';
            !in_array('subject_id', $col_arr) && $col_arr[] = 'subject_id';
            !in_array('store_id', $col_arr) && $col_arr[] = 'store_id';
            $cols = implode(',', $col_arr);
        }

        $offset = (int) $params['offset'];
        $limit = (int) $params['limit'];
        !$limit && $limit = -1;

        $filter = $params['filter'];
        !$filter && $filter = array();
        $filter['id'] && $filter['id'] = !is_array($filter['id']) ? explode(',', $filter['id']) : $filter['id'];

        $activities = app::get('experiencestore')->model('activity_schedule')->getList($cols, $filter, $offset, $limit);
        !$activities && $this->failure('暂无数据');
        
        $subject_ids = $activities ? array_keys(utils::array_change_key($activities, 'subject_id')) : false;
        $subjects = $subject_ids ? app::get('experiencestore')->model('activity_subject')->getList('*', array('id' => $subject_ids)) : false;
        $subjects && $subjects = utils::array_change_key($subjects, 'id');
        
        $store_ids = $activities ? array_keys(utils::array_change_key($activities, 'store_id')) : false;
        $stores = $store_ids ? app::get('experiencestore')->model('store')->getList('*', array('id' => $store_ids)) : false;
        $stores && $stores = utils::array_change_key($stores, 'id');
        
        foreach ($activities as $k => $v){
            $activities[$k]['subject'] = ($subjects && $subjects[$v['subject_id']]) ? $subjects[$v['subject_id']] : array();
            $activities[$k]['store'] = ($stores && $stores[$v['store_id']]) ? $stores[$v['store_id']] : array();
        }
        $this->success($activities);
    }

    public function groupbooking($params = null) {
        $params = array_merge(($params ? $params : array()), ($this->params ? $this->params : array()));
        $cols = strtolower(trim(urldecode($params['cols'])));
        !strlen($cols) && $cols = '*';

        if ($cols != '*') {
            $col_arr = explode(',', $cols);
            $col_arr = array_map("trim", $col_arr);
            !in_array('activity_id', $col_arr) && $col_arr[] = 'activity_id';
            !in_array('goods_id', $col_arr) && $col_arr[] = 'goods_id';
            !in_array('product_id', $col_arr) && $col_arr[] = 'product_id';
            $cols = implode(',', $col_arr);
        }

        $offset = (int) $params['offset'];
        $limit = (int) $params['limit'];
        !$limit && $limit = -1;

        $filter = $params['filter'];
        !$filter && $filter = array();
        $filter['activity_id'] && $filter['activity_id'] = !is_array($filter['activity_id']) ? explode(',', $filter['activity_id']) : $filter['activity_id'];

        $activities = app::get('groupbooking')->model('activity')->getList($cols, $filter, $offset, $limit);
        !$activities && $this->failure('暂无数据');
        
        $goods_ids = $activities ? array_keys(utils::array_change_key($activities, 'goods_id')) : false;
        $goods = $goods_ids ? app::get('b2c')->model('goods')->getList('*', array('goods_id' => $goods_ids)) : false;
        $goods && $goods = utils::array_change_key($goods, 'goods_id');
        
        $product_ids = $activities ? array_keys(utils::array_change_key($activities, 'product_id')) : false;
        $products = $product_ids ? app::get('b2c')->model('products')->getList('*', array('product_id' => $product_ids)) : false;
        $products && $products = utils::array_change_key($products, 'product_id');
        
        foreach ($activities as $k => $v){
            $activities[$k]['goods'] = ($goods && $goods[$v['goods_id']]) ? $goods[$v['goods_id']] : array();
            $activities[$k]['products'] = ($products && $products[$v['product_id']]) ? $products[$v['product_id']] : array();
        }
        $this->success($activities);
    }
    
    public function preselling($params = null) {
        $params = array_merge(($params ? $params : array()), ($this->params ? $this->params : array()));
        $cols = strtolower(trim(urldecode($params['cols'])));
        !strlen($cols) && $cols = '*';

        if ($cols != '*') {
            $col_arr = explode(',', $cols);
            $col_arr = array_map("trim", $col_arr);
            !in_array('activity_id', $col_arr) && $col_arr[] = 'activity_id';
            !in_array('goods_id', $col_arr) && $col_arr[] = 'goods_id';
            !in_array('product_id', $col_arr) && $col_arr[] = 'product_id';
            $cols = implode(',', $col_arr);
        }

        $offset = (int) $params['offset'];
        $limit = (int) $params['limit'];
        !$limit && $limit = -1;

        $filter = $params['filter'];
        !$filter && $filter = array();
        $filter['activity_id'] && $filter['activity_id'] = !is_array($filter['activity_id']) ? explode(',', $filter['activity_id']) : $filter['activity_id'];

        $activities = app::get('preselling')->model('activity')->getList($cols, $filter, $offset, $limit);
        !$activities && $this->failure('暂无数据');
        
        $goods_ids = $activities ? array_keys(utils::array_change_key($activities, 'goods_id')) : false;
        $goods = $goods_ids ? app::get('b2c')->model('goods')->getList('*', array('goods_id' => $goods_ids)) : false;
        $goods && $goods = utils::array_change_key($goods, 'goods_id');
        
        $product_ids = $activities ? array_keys(utils::array_change_key($activities, 'product_id')) : false;
        $products = $product_ids ? app::get('b2c')->model('products')->getList('*', array('product_id' => $product_ids)) : false;
        $products && $products = utils::array_change_key($products, 'product_id');
        
        foreach ($activities as $k => $v){
            $activities[$k]['goods'] = ($goods && $goods[$v['goods_id']]) ? $goods[$v['goods_id']] : array();
            $activities[$k]['products'] = ($products && $products[$v['product_id']]) ? $products[$v['product_id']] : array();
        }
        $this->success($activities);
    }
}
