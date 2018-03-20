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


class solr_service_searchgoods extends dbeav_filter
{

    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null ,$groupby='goods_id'){

        $b2c_goods = app::get('b2c') ->model('goods') ;
        $filter = utils::addslashes_array($filter);
        //关键词
        $keyword = trim($filter['keyword']);
        $search_keyword = $keyword;
        $keyword = $highlight_keyword= str_replace(' ','',$keyword);
        if ($keyword){
            $pinyin = vmc::singleton('solr_pinyin');
            $str_type = $pinyin ->get_str_type($keyword);
            switch($str_type){
                case '1':
                    $query = 'pinyin:'.$keyword;break;
                case '2':
                    $query = 'text:'.$search_keyword;break;
                case '3':
                    $pinyin_arr = $pinyin ->zh_convert($keyword);
                    $keyword = $pinyin_arr['l'];
                    $query = 'text:'.$search_keyword .' OR pinyin:'.$keyword;break;
                default:
                    $query = 'text:'.$search_keyword;
            }
        }
        //子类、扩展分类
        if($filter['cat_id']){
            $filter_query['goods_cat_id'] = 'goods_cat_id:'.$filter['cat_id'];
        }
        if($filter['brand_id']){
            $filter_query['goods_brand_id'] = 'goods_brand_id:'.$filter['brand_id'];
        }

        //价格范围
        if(!empty($filter['price'])){
            $from = $filter['price'][0] ? $filter['price'][0] :0;
            $to = $filter['price'][1] ? $filter['price'][1] :'*';
            $filter_query['price'] = 'price:['.$from.' TO '.$to.']';
        }

        foreach($filter as $k =>$v){
            //规格
            if(strpos($k,'spec_')===0 && $v){
                $or_filter =array();
                $p_arr = explode('-' ,$v);
                foreach($p_arr as $vv){
                    if($vv){
                        $or_filter[] = 'spec_info:'.$vv;
                    }
                    
                }
                if(!empty($or_filter)){
                    $filter_query['spec_info_'.$k] = implode(' OR ',$or_filter);
                }
            }

        }
        
        foreach($filter as $k =>$v){
            //属性
            if(strpos($k,'p_')===0 && $v){
                $or_filter =array();
                $p_arr = explode('-' ,$v);
                foreach($p_arr as $vv){
                    if($vv){
                        $or_filter[] = 'props:'.$k.'_'.$vv;
                    } 
                }
                if(!empty($or_filter)){
                    $filter_query['props_'.$k] = implode(' OR ',$or_filter);
                }
            }

        }

        if(empty($query) && empty($filter_query) && !$orderType){
            foreach ($filter as $k => $v) {
                if (!isset($v) || empty($v)) {
                    unset($filter[$k]);
                }
            }
            return array(
                'total' =>$b2c_goods->count($filter),
                'rows' =>$b2c_goods ->lw_getList($cols, $filter, $offset, $limit, $orderType)
            );
        }
        $solr_filter = array(
            'keyword' =>$keyword,
            'query' => $query   ?$query :'*:*',
            'filter_query' =>$filter_query ?$filter_query :array()
        );

        //固定参数
        $solr_filter['filter_query']['marketable'] = 'marketable:true';
        $solr_filter['filter_query']['goods_marketable'] = 'goods_marketable:true';
        $solr_filter['filter_query']['disabled'] = 'disabled:false';
        //    $solr_filter['filter_query']['is_default'] = 'is_default:true';

        $solr_orderBy= array('w_order desc','goods_id desc');
        if($orderType){
            $solr_orderBy = array($orderType);
        }
        $solr_stage = vmc::singleton('solr_stage');
        $solr_filter['highlight'] = false;

        $data = $solr_stage->getList('goods_id,id,name',$solr_filter,$offset, $limit, $solr_orderBy ,$groupby );
        if($data ===false){
            $solr_filter['highlight'] = false;
            $data = $solr_stage->getList('goods_id,id,name',$solr_filter,$offset, $limit, $solr_orderBy ,$groupby );
        }
        $total = $data['total'];
        if(!$total){
            return false;
        }

        $str_length = mb_strlen($highlight_keyword ,'UTF-8');
        foreach($data['rows'] as &$row){
            $keyword_arr =array();
            $row['highlight_name'] =$row['name'];
            for ($i=0;$i<$str_length ;$i++)
            {
                $key =strtolower(mb_substr($highlight_keyword, $i, 1, "UTF-8"));
                if(in_array($key ,$keyword_arr)){
                    continue;
                }
                $keyword_arr[] = $key;
                if(stripos($row['highlight_name'] ,$key)){
                    $row['highlight_name'] = str_ireplace($key, '@@'.$key.'&@@&',$row['highlight_name'] );
                }
            }

            $row['highlight_name'] = str_ireplace('&@@&' ,'</span>',$row['highlight_name'] );
            $row['highlight_name'] = str_ireplace('@@' ,'<span class="highlight" style="color:red">',$row['highlight_name'] );
        }
        return $data;
    }
}
