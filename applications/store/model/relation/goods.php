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


class store_mdl_relation_goods extends dbeav_model
{
    private $msg = '';
    private $store_id;

    /**
     * 根据商品id获取商品已关联的店铺id数组
     *
     * @param $goods_id
     *
     * @return array
     */
    public function get_goods_relation_store_infos_by_goods_id($goods_id){
        $goods_relation_store_infos = [];
        $condition = [
            'goods_id' => $goods_id
        ];
        $relation_info = $this->getList('store_id, relation_id', $condition);
        if(is_array($relation_info) == true && count($relation_info) > 0){
            $temp_store_id = $relation_info['0']['store_id'];

            //查询店铺信息
            $model_store = app::get('store')->model('store');
            $store_columns = 'store_id, store_name, store_bn, store_area, store_address, store_contact';
            $goods_relation_store_infos = $model_store->getList($store_columns, ['store_id' => $temp_store_id]);
            if(is_array($goods_relation_store_infos) == true && count($goods_relation_store_infos) > 0){
                $goods_relation_store_infos['0']['relation_id'] = $relation_info['0']['relation_id'];
                $goods_relation_store_infos = utils::array_change_key($goods_relation_store_infos, 'store_id');
            }
        }

        return $goods_relation_store_infos;
    }

    /**
     * 根据店铺id删除店铺和商品关联
     *
     * @param int $store_id 店铺id
     *
     * @return bool
     */
    public function del_goods_relation_store_by_store_id($store_id){
        $this->store_id = $store_id;

        try{

            //删除店铺和商品关联
            $this->del_goods_relation_store();

            //删除店铺商品库存
            $this->del_store_goods_stock();
        }catch (Exception $e){
            $this->msg = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function get_msg()
    {
        return $this->msg;
    }

    /**
     * 删除店铺和商品关联
     *
     * @throws Exception
     */
    private function del_goods_relation_store(){
        $condition = [
            'store_id' => $this->store_id
        ];

        $del_result = $this->delete($condition);

        if(!$del_result){
            throw new Exception('删除店铺和商品关联失败');
        }
    }

    /**
     * 删除店铺商品库存
     */
    private function del_store_goods_stock(){
        $condition = [
            'store_id' => $this->store_id
        ];

        $model_stock = app::get('b2c')->model('stock');

        $del_result = $model_stock->delete($condition);

        if($del_result){
            throw new Exception('删除店铺商品库存失败');
        }
    }
}//End Class
