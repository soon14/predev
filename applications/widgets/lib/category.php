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
class widgets_category{
    public function __construct(&$app){
        $this ->app =$app;
    }
    public function get_parent_path($parent_id)
    {
        $parent = $this ->app ->model('widgets_category') ->getRow('*' ,array('cid' => $parent_id));
        if (!$parent) {
            return '';
        }
        $path[] = array(
            'title' => $parent['title'],
            'ident' => $parent['cid'],
        );
        $parents = explode(',', $parent['parent_path']);
        $parents = $this ->app ->model('widgets_category') ->getList('*' ,array('cid' => $parents));
        foreach ($parents as $category) {
            $path[] = array(
                'title' => $category['title'],
                'ident' => $category['cid'],
            );
        }
        return array_reverse($path);
    }

    public function get_selectmaps(){

        $rows = $this ->app ->model('widgets_category')->getList('*');
        $tree = $this ->get_tree($rows);
        return $this->parse_selectmaps($tree);
    }


    public function get_tree($items){
        $items = utils::array_change_key($items ,'cid');
        $tree = array();
        foreach($items as $item){
            if(isset($items[$item['parent_id']])){
                $items[$item['parent_id']]['children'][] = &$items[$item['cid']];
            }else{
                $tree[] = &$items[$item['cid']];
            }
        }
        return $tree;
    }



    private function parse_selectmaps($rows ,$is_child = false)
    {
        $data = array();
        foreach ((array) $rows as $k => $v) {
            $step = $is_child ? count(explode(',' ,$v['parent_path']))  : 0 ;
            $data[] = array('parent_id' => $v['parent_id'], 'title' => $v['title'] ,'cid' =>$v['cid'] ,'step' =>$step+1);
            if ($v['children']) {
                $data = array_merge($data, $this->parse_selectmaps($v['children'] ,true));
            }
        }

        return $data;
    }
}