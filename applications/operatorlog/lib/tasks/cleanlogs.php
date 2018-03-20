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



class operatorlog_tasks_cleanlogs extends base_task_abstract implements base_interface_task{
    public function exec($params=null){
        $time = strtotime('-30 days');
        $sql = "DELETE FROM vmc_operatorlog_normallogs WHERE dateline<=$time";
        app::get('operatorlog')->model('normallogs')->db->exec( $sql );
    }
}
