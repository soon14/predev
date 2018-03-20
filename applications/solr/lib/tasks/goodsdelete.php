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
class solr_tasks_goodsdelete extends base_task_abstract implements base_interface_task
{
    public function exec ($params = null)
    {
        $solr_stage = vmc::singleton('solr_stage');
        if($params['product_id']){
            $product_id = is_array($params['product_id'] ) ?$params['product_id'] :array($params['product_id']);
            foreach($product_id as $id){
                if(!$solr_stage ->delete($id)){
                    logger::error('商品solr索引删除失败,product_id:'.$id);
                }
            }

        }else{
            if($params['goods_id']){
                $goods_id = is_array($params['goods_id']) ?$params['goods_id'] : array($params['goods_id']) ;
            }else{
                $goods = app::get('b2c')->model('goods') ->getList('goods_id' ,  $params);
                $goods_id = array_keys(utils::array_change_key($goods ,'goods_id'));
            }
            if($goods_id){
                foreach($goods_id as $gid){
                    $solr_filter = 'goods_id:'.$gid;
                    if(!$solr_stage ->delete(0 , $solr_filter)){
                        logger::error('商品solr索引删除失败,goods_id:'.$gid);
                    }
                }
            }
        }
    }
}