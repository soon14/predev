<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/3/28
 * Time: 16:11
 */
class store_goods_save{
    public function exec($goods){
        $relation_goods = app::get('store') ->model('relation_goods');
        if(!empty($goods['store_id'])){
            $goods['store_id'] = $goods['store_id'][0];
            $data = array(
                'goods_id' => $goods['goods_id'],
                'store_id' => $goods['store_id'],
                'store_enable' => $goods['store_enable']
            );
            $relation_goods ->save($data);
        }else{
            $relation_goods ->delete(array('goods_id' => $goods['goods_id']));
        }
    }
}