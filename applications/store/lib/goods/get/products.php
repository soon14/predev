<?php

/**
 * 获取店铺货品
 *
 * Class store_goods_get_products
 */
class store_goods_get_products
{
    private $model_products = null;
    /**
     * 商品和店铺关联信息
     * @var array
     */
    private $goods_relation_store = [];

    public function __construct() {
        $this->model_products = app::get('b2c')->model('products');
    }

    /**
     * 查询店铺商品信息
     *
     * @param string $columns 要查询的字段
     * @param array $filter 查询条件
     * @param int $now_page 当前页
     * @param int $page_size 每次查询多少条记录
     * @param string $oder_type 排序规则
     *
     * @return array
     */
    public function get_store_products($columns = '*', $filter = [], $now_page = 0, $page_size = -1, $oder_type = ''){
        $store_products = [];

        //增加店铺商品查询条件
        $filter = $this->extension_store_filter($filter);

        //强制查询商品id
        if($columns != '*' && strpos($columns, 'goods_id') === false){
            $columns .= ', goods_id';
        }

        //查询店铺货品数量
        $store_product_count = $this->model_products->count($filter);
        if($store_product_count > 0){
            $store_products = $this->model_products->getList($columns, $filter, $now_page, $page_size, $oder_type);
            foreach ($store_products as $index => $store_product) {
                $store_product['store_id'] = $this->goods_relation_store[$store_product['goods_id']];

                $store_products[$index] = $store_product;
            }
        }

        return $store_products;
    }

    /**
     * 查询店铺商品时扩展店铺商品的查询条件
     *
     * @param array $filter 基础查询条件数组
     *
     * @return array
     */
    private function extension_store_filter($filter = []){
        $store_goods_filter = [
            'goods_id' => 'none',//默认使店铺里查询出来的商品为空
        ];
        $store_relation_goods_condition = [];

        if(isset($filter['store_id'])){
            $store_relation_goods_condition['store_id'] = $filter['store_id'];
        }
        $model_store_relation_goods = app::get('store')->model('relation_goods');
        $store_relation_goods_infos = $model_store_relation_goods->getList('goods_id, store_id', $store_relation_goods_condition);

        if(is_array($store_relation_goods_infos) && count($store_relation_goods_infos) > 0){
            $temp = [];
            foreach($store_relation_goods_infos as $store_relation_goods_info){
                $temp[] = $store_relation_goods_info['goods_id'];

                $this->goods_relation_store[$store_relation_goods_info['goods_id']] = $store_relation_goods_info['store_id'];
            }
            $store_goods_filter['goods_id'] = $temp;

            if(is_array($filter['goods_id']) == true){
                $store_goods_filter['goods_id'] = array_merge($store_goods_filter['goods_id'], $filter['goods_id']);
            }else if(is_numeric($filter['goods_id']) == true && $filter['goods_id'] > 0){
                if(in_array($filter['goods_id'], $store_goods_filter['goods_id']) == false){
                    $store_goods_filter['goods_id'][] = $filter['goods_id'];
                }
            }
        }

        $filter = array_merge($filter, $store_goods_filter);

        return $filter;
    }
}