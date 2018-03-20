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
class solr_service_savegoods{

    public function exec($goods){
        system_queue::instance()->publish('solr_tasks_goodsindex', 'solr_tasks_goodsindex', array('goods_id' =>$goods['goods_id']));
    }

}