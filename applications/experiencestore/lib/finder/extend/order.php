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
class experiencestore_finder_extend_order{

    public function get_extend_colums(){
        $db['activity_order']=array (
            'columns' =>
                array (
                    'store_name' =>
                        array (
                            'type' => 'varchar(50)',
                            'label' => '地点',
                            'filtertype' => 'yes',
                            'filterdefault' => true,
                        ),
                    'subject_title' =>
                        array (
                            'type' => 'varchar(50)',
                            'label' => '活动主题',
                            'filtertype' => 'yes',
                            'filterdefault' => true,
                        ),
                )
        );
        return $db;
    }
}