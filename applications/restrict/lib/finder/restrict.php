<?php

/**
 * Created by PhpStorm.
 * User: cp
 * Date: 2016/6/14
 * Time: 16:25
 */
class restrict_finder_restrict
{
    var $column_control = '操作';
    function column_control($row){
        $btn  = '<a   class="btn btn-xs btn-default"   href="index.php?app=restrict&ctl=admin_restrict&act=edit&p[0]='.$row['res_id'].'">'.('编辑').'</a>';

        return $btn;
    }

	/*public function detail_goods($res_id)
    {
    	$render = app::get('restrict')->render();
    	$row_restrict = app::get('restrict')->model('restrict')->getRow('goods_id',array('res_id'=>$res_id));
    	$render->pagedata['goods'] = app::get('b2c')->model('goods')->getList('name',array('filter_sql'=>'goods_id IN ('.$row_restrict['goods_id'].')'));
        return $render->fetch('admin/goods.html');
    }*/
    
}
