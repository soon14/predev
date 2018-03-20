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
class solr_tasks_init extends base_task_abstract implements base_interface_task
{
    public function exec ($params = null)
    {
        $goods_mdl =app::get('b2c') ->model('goods');
        $total =$goods_mdl ->count();
        $limit = 100;
        $step = ceil($total/$limit);
        for($i =0 ; $i<$step ;$i++){
            $goods = $goods_mdl ->getList('goods_id',null, $i*$limit , $limit);
            foreach($goods as $v){
                vmc::singleton('solr_tasks_goodsindex') ->exec(array('goods_id' =>$v['goods_id']));
            }
        }
    }
}