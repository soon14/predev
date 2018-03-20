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

require_once APP_DIR.'/solr/solarium.phar';

class solr_stage
{
    public function __construct()
    {
        $config = array(
            'endpoint' => array(
                'localhost' => array(
                    'host' => app::get('solr')->getConf('solr_host'),
                    'port' => app::get('solr')->getConf('solr_port'),
                    'path' => app::get('solr')->getConf('solr_path'),
                ),
            ),
        );
        $this->client = new Solarium\Client($config);
        if (!$this->client) {
            trigger_error('Solr Connect ERROR', E_USER_ERROR);
        }

    }

    public function ping(&$data)
    {
        $ping = $this->client->createPing();
        try {
            $result = $this->client->ping($ping);
            $data = $result->getData();

            return true;
        } catch (Solarium\Exception $e) {
            $data = $e->getMessage();

            return false;
        }
    }

    public function delete($id = 0, $filter=''){
        $client = $this->client;
        try{
            $update = $client->createUpdate();
            if($id){
                $update->addDeleteById($id);
            }else{
                $update->addDeleteQuery($filter);
            }
            $update->addCommit();
            $result = $client->update($update);
            if($result->getStatus()== 0){
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    //批量处理
    public function save($sdf_arr =array()){
        $client = $this->client;
        try{
            $update = $client->createUpdate();
            $docs = array();
            foreach($sdf_arr as $sdf){
                $doc = $update->createDocument();
                foreach ($sdf as $key => $value) {
                    $doc->$key = $value;
                }
                $docs[] = $doc;
            }
            $update->addDocuments($docs);
            $update->addCommit();
            $result = $client->update($update);
            if($result->getStatus() == 0){
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderby =null ,$groupby=null)
    {
        $client = $this->client;
        try{
            $query = $client->createSelect ();
            if ($cols != '*') {
                $query->setFields (explode(',', $cols));
            }
            if ($limit > 0) {
                $query->setStart ($offset)->setRows ($limit);
            }
            //TODO filter
            $query->setQuery($filter['query']);
            if(!empty($filter['filter_query'])){
                foreach($filter['filter_query'] as $key=>$filter_query){
                    $query ->createFilterQuery($key)->setQuery($filter_query);
                }
            }
            //TODO orderby
            if($orderby){
                foreach($orderby as $sort){
                    $sort  =explode(' ',$sort);
                    $query ->addSort($sort[0] ,$sort[1]);
                }
            }

            
            if($filter['highlight']&&$filter['keyword']){
                //TODO highlight
                $hl = $query->getHighlighting ();
                $hl->setFields ('name');
                $hl->setSimplePrefix ('<span class="highlight" style="color:red">');
                $hl->setSimplePostfix ('</span>');
            }


            //TODO facet
            $facetSet = $query->getFacetSet();
            $facetSet->createFacetField('type_id')->setField('goods_type_id');
            $result =array();
            if($groupby){
                //TODO groupby
                $group = $query->getGrouping();
                $group->addField($groupby);
                $group->setLimit(1);
                $group->setNumberOfGroups(true);
                $resultset = $client->select ($query);
                $groups = $resultset->getGrouping();
                $result['total'] = 0;
                foreach ($groups as $groupKey => $fieldGroup) {
                    $result['total'] +=$fieldGroup->getNumberOfGroups();
                    foreach ($fieldGroup as $valueGroup) {
                        foreach ($valueGroup as $document) {
                            $row =array();
                            foreach ($document as $field => $value) {
                                $row[$field] = $value;
                            }
                            $result['rows'][] = $row;
                        }
                    }
                }
            }else{
                $resultset = $client->select ($query);
                if ($resultset->getStatus () == 0) {
                    $result['total'] = $resultset->getNumFound ();
                    $documents = $resultset->getDocuments ();
                    foreach ($documents as $document) {
                        $row =array();
                        foreach ($document as $field => $value) {
                            $row[$field] = $value;
                        }
                        $result['rows'][] = $row;
                    }
                }
            }
            //高亮，暂不开启
            
            if($filter['highlight']&&$filter['keyword']){
                $highlighting = $resultset->getHighlighting();
                $highlight = array();
                foreach($highlighting as $k =>$v){
                    $fields = $v->getFields();
                    if(!empty($fields)){
                        $highlight[$k] =$fields;
                    }
                }
                foreach($result['rows'] as $k=>$v){
                    if($highlight[$v['id']]['name'][0]){
                        $result['rows'][$k]['highlight_name'] = $highlight[$v['id']]['name'][0];
                    }
                }
            }
            

            $facet = $resultset->getFacetSet()->getFacet('type_id');
            foreach ($facet as $k => $v) {
                if($v>0){
                    $result['facet']['type_id'][] = array(
                        'type_id' =>$k,
                        'total' =>$v
                    );
                }
            }
            return $result;
        }catch (Exception $e) {
            return false;
        }
    }

    public function count($filter = array() ,$groupby='goods_id'){
        $client = $this->client;
        try{
            $query = $client->createSelect ();
            //TODO filter
            $query->setQuery($filter['query']);
            if(!empty($filter['filter_query'])){
                foreach($filter['filter_query'] as $key=>$filter_query){
                    $query ->createFilterQuery($key)->setQuery($filter_query);
                }
            }
            $query->setStart (0)->setRows (0);
            $total = 0;
            if($groupby){
                //TODO groupby
                $group = $query->getGrouping();
                $group->addField($groupby);
                $group->setLimit(1);
                $group->setNumberOfGroups(true);
                $resultset = $client->select ($query);
                $groups = $resultset->getGrouping();

                foreach ($groups as $groupKey => $fieldGroup) {
                    $total +=$fieldGroup->getNumberOfGroups();
                }
            }else{
                $resultset = $client->select ($query);
                if ($resultset->getStatus () == 0) {
                    $total += $resultset->getNumFound ();
                }
            }
            return $total;
        }catch (Exception $e){
            return false;
        }

    }


    public function facet_count($key ,$filter , $groupby='goods_id'){
        $client = $this ->client;
        try{
            $query = $client->createSelect();
            $facetSet = $query->getFacetSet();
            $facet = $facetSet->createFacetMultiQuery('facet');
            $solr_filter['marketable'] = 'marketable:true';
            $solr_filter['goods_marketable'] = 'goods_marketable:true';
            $solr_filter['disabled'] = 'disabled:false';
            $solr_filter = implode(' AND ' ,$solr_filter);
            foreach($filter as $k =>$value){
                $facet->createQuery('facet_'.$k, $solr_filter .' AND '. $key.':'.$value.'*');
            }

            $resultset = $client->select($query);
            $facet = $resultset->getFacetSet()->getFacet('facet');
            $result =array();
            foreach ($facet as $key => $count) {
                $result[$key] = $count;
            }
            return $result;
        }catch(Exception $e){
            return false;
        }

    }

    /**
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function suggest($keyword='', $limit = 10 ){
        $client = $this ->client;
        try{
            $query = $client->createSuggester();
            $query->setQuery($keyword); //multiple terms
            $query->setDictionary('default');
            $query->setOnlyMorePopular(true);
            $query->setCount($limit);
            $query->setCollate(true);
            $resultset = $client->suggester($query);
            $result = $resultset->getData();
            $result = $result['suggest']['default'];
            $total = 0;
            $rows =array();
            foreach($result as $v){
                $total +=$v['numFound'];
                foreach($v['suggestions']  as $vv){
                    $rows[] = array(
                        'term' =>$vv['term'],
                        'weight' =>$vv['weight'],
                    );
                }
            }
            return array(
                'total' =>$result[$keyword]['numFound'],
                'rows' =>$rows
            );
        }catch(Exception $e){
            return false;
        }

    }

    /**
     * 使用facet
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function facet_suggest($keyword='', $limit = 10){
        $result  =$this ->pre_facet($keyword ,$limit);
        if($result['total'] ==0){
            $result = $this ->pinyin_suggest($keyword ,$limit);
        }
        return $result;
    }

    public function pre_facet($pre='', $limit = 10){
        $client = $this ->client;
        try{
            $query = $client->createSelect();
            $query->setStart (0)->setRows (0);
            $facetSet = $query ->getFacetSet();
            $facet = $facetSet->createFacetField('suggest');
            //默认使用前缀方式查询
            $facet ->setPrefix($pre);
            $facet ->setField('suggest'); //注意该字段
            $facet ->setLimit($limit);
            $facet ->setMinCount(1);
            $resultset = $client->select($query);
            $result = $resultset ->getFacetSet()->getFacet('suggest')->getValues();
            $rows =array();
            foreach($result as $k =>$v){
                $rows[] =array(
                    'term' =>$k,
                    'weight' =>0,
                    'total' =>$v,
                );
            }
            return array(
                'total' =>count($rows),
                'rows' =>$rows
            );
        }catch (Exception $e){
            return false;
        }

    }

    public function pinyin_suggest($keyword='', $limit = 10 ,$groupby='goods_id'){
        $client = $this->client;
        try{
            $query = $client->createSelect ();
            $query->setQuery('keyword_pinyin:'.$keyword.'*');
            $query->setFields (array('keyword_pinyin'));
            $query ->addSort('w_order' ,$query::SORT_DESC);
            $query->setStart (0)->setRows ($limit*2);
            $group = $query->getGrouping();
            $group->addField($groupby);
            $group->setLimit(1);
            $group->setNumberOfGroups(true);
            $resultset = $client->select ($query);
            $groups = $resultset->getGrouping();
            $rows= array();
            $keywords = array();
            foreach ($groups as $groupKey => $fieldGroup) {
                //$fieldGroup->getNumberOfGroups();
                foreach ($fieldGroup as $valueGroup) {
                    foreach ($valueGroup as $document) {
                        foreach ($document as $field => $value) {
                            if($field =='keyword_pinyin'){
                                $value = explode(',' ,$value);
                                foreach($value as $vv){
                                    $vv = explode(':' ,$vv);
                                    if($vv[1]{0} == $keyword{0}){
                                        $keywords[]= $vv[0];
                                    }

                                }
                            }
                        }
                    }
                }
            }
            $keywords = array_slice(array_flip(array_flip($keywords)) ,0 ,10);
            $facet_count = $this ->facet_count('text' ,$keywords);
            foreach($keywords as $k =>$v){
                $rows[] =array(
                    'term' =>$v,
                    'weight' =>0,
                    'total' =>$facet_count['facet_'.$k],
                );
            }
            return array(
                'total' =>count($rows),
                'rows' =>$rows
            );
        }catch (Exception $e){
            return false;
        }
    }
}
