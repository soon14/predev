<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/3/28
 * Time: 14:43
 */
class store_goods_editor
{

    public function get_extends_label(&$sections)
    {
        $sections['store'] = array(
            'label' => ('门店'),
            'file' => 'admin/goods/detail/store.html',
            'app' => 'store'
        );
    }

    public function get_extends_data($obj ,$goods_id)
    {
        $store_relation = app::get('store')->model('relation_goods')->getRow('*', array('goods_id' => $goods_id));
        $store = app::get('store')->model('store') ->getRow('*' ,array('store_id' =>$store_relation['store_id'] ));
        $obj->pagedata['store_relation'] = array_merge($store_relation ,$store);
    }
}
