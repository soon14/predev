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


class solr_tasks_goodsindex extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $goods = app::get('b2c') ->model('goods') ->dump($params['goods_id'], '*', 'default');
        $products = $goods['product'];
        if(empty($products)){
            return false;
        }
        $data =array();
        $keywords =$keywords_pinyin= $goods_type =$goods_cat_name=$cat_name =$goods_brand =$cat_extends_name =$goods_props = '';
        if(!empty($goods['keywords'])){
            $keywords = array_keys(utils::array_change_key($goods['keywords'] ,'keyword'));
            $pinyin = vmc::singleton('solr_pinyin');
            foreach($keywords as $v){
                $pinyin_arr = $pinyin ->zh_convert($v);
                $keywords_pinyin[] = $v.':'.$pinyin_arr['l'];
                $keywords_pinyin[] = $v.':'.$pinyin_arr['s'];
            }
            $keywords_pinyin = implode(',',$keywords_pinyin);
            $keywords = implode(',',$keywords);
        }
        if(!empty($goods['type']['type_id'])){
            $goods_type = $goods['type']['name'];
        }
        if(!empty($goods['category']['cat_id'])){
            $goods_cat = $this ->get_cat($goods['category']['cat_id']);
            $goods_cat_name = implode(',' ,$goods_cat['cat_name']);
            $cat_name = $goods_cat['name'];
        }

        if(!empty($goods['brand']['brand_id'])){
            $goods_brand =  $goods['brand']['brand_name'];
        }
        if(!empty($goods['extended_cat'])){
            $cat_extends = $this ->get_cat_extends($goods['extended_cat']);
            $cat_extends_name = implode(',' ,$cat_extends['cat_name']);
        }
        if(!empty($goods['props'])){
            $goods_props = $this ->get_props( $goods['props']);
            $goods_props = implode(',' ,$goods_props);
        }
        foreach($products as $product){
            $data[] = array(
                'id' =>$product['product_id'],
                'goods_id' =>$product['goods_id'],
                'goods_cat_id' =>$goods_cat['cat_id'] .($cat_extends['cat_id'] ? ','.$cat_extends['cat_id'] :''),
                'goods_brand_id' =>$goods['brand']['brand_id'],
                'goods_type_id' =>$goods['type']['type_id'],
                'unit' =>$product['unit'],
                'image_id' =>$product['image_id'],
                'disabled' =>$product['disabled']=='true' ?true :false,
                'is_default' =>$product['is_default']=='true' ?true :false,
                'marketable' =>$product['marketable']=='true' ?true :false,
                'goods_marketable' =>$goods['marketable']=='true' ?true :false,
                'price' =>$product['price'],
                'name' =>$goods['name'],
                'spec_info' =>str_replace('/' ,',' ,$product['spec_info']),
                'goods_keyword' =>$keywords,
                'keyword_pinyin' =>$keywords_pinyin,
                'goods_brief' =>$goods['brief'],
                'goods_type' =>$goods_type,
                'goods_cat' => $goods_cat_name.($cat_extends_name ?','.$cat_extends_name:''),
                'cat_name' => $cat_name,
                'goods_brand' =>$goods_brand,
                'last_modify' =>$product['last_modify'],
                'goods_intro' =>$goods['description'],
                'w_order' =>$goods['w_order'],
                'props' =>$goods_props,
                'buy_count' =>$goods['buy_count'],
                'comment_count' =>$goods['comment_count'],
                'uptime' =>$goods['uptime'],
            );
        }
        $solr_stage = vmc::singleton('solr_stage');
        if(!$solr_stage ->save($data)){
            logger::error('商品索引更新失败');
        }
        return true;
    }

    private function get_cat($cat_id){
        $cat_mdl = app::get('b2c') ->model('goods_cat');
        $cat_path = $cat_mdl ->getRow('cat_path,cat_name' ,array('cat_id' =>$cat_id));
        $cat_list = $cat_mdl ->getlist('cat_name' ,array('cat_id' =>explode(',',$cat_path['cat_path'])));
        return array(
            'cat_id' =>$cat_path['cat_path'],
            'cat_name' =>array_keys(utils::array_change_key($cat_list ,'cat_name')),
            'name' =>$cat_path['cat_name']
        );
    }

    private function get_cat_extends($extended_cat){
        $cat_mdl = app::get('b2c') ->model('goods_cat');
        $cat_path = $cat_mdl ->getlist('cat_path' ,array('cat_id' =>$extended_cat));
        $cat_extends = array();
        foreach($cat_path as $v){
            $cat_list = $cat_mdl ->getlist('cat_name' ,array('cat_id' =>explode(',',$v['cat_path'])));
            $cat_name =  array_keys(utils::array_change_key($cat_list ,'cat_name'));
            $cat_extends['cat_name'] = array_merge($cat_extends['cat_name'] ,$cat_name);
            $cat_extends['cat_id'] = $cat_extends['cat_id'] .($cat_extends['cat_id']?',':'') .$v['cat_path'] ;
        }
        return $cat_extends;
    }

    private function get_props( $props){
        $type_props = array();
        foreach($props as $k=>$v){
            if($v['value']){
                //兼容商品属性多选
                $value =explode(',' ,$v['value']);
                foreach($value as $vv){
                    $type_props[] = $k.'_'.$vv;
                }
            }
        }
        return $type_props;
    }
}
