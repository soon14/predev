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
class groupbooking_finder_extend_activity
{

    public function get_extend_colums()
    {
        $db['activity'] = array(
            'columns' => array(
                'activity_status' => array(
                    'type' => array(
                        '0' => '未开始',
                        '1' => '进行中',
                        '2' => '已结束',
                    ),
                    'label' => ('状态'),
                    'filtertype' => 'has',
                    'filterdefault' => true,
                ),
            ),
        );
        return $db;
    }
}