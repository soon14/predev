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



class image_tasks_imagerebuild extends base_task_abstract implements base_interface_task{
    public function exec($params=null){
          //每次最多处理2个
        $limit = 2;
        $model = app::get('image')->model('image');
        $db = vmc::database();
        if($params['filter']['image_id']=='_ALL_'||$params['filter']['image_id']=='_ALL_'){
            unset($params['filter']['image_id']);
        }
        $where = $model->_filter($params['filter']);
        $where .= ' and last_modified<='.$params['queue_time'];
        $rows = $db->select('select image_id from vmc_image_image where '.$where.' order by last_modified desc limit '.$limit);
        foreach($rows as $r){
			if($params['watermark'] == 'false')
			{
				$params['watermark'] = false;
			}
            $model->rebuild($r['image_id'],$params['size'],$params['watermark']);

        }
        $r = $db->selectrow('select count(*) as c from vmc_image_image where '.$where);
        return $r['c'];

    }
}

