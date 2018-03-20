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


 class dbeav_metadata
 {
    private $_meta_columns = array();

     public function __construct()
     {
         $sql = 'select * from vmc_dbeav_meta_register';
         $arr_rows = vmc::database()->select($sql);
         foreach ($arr_rows as $row) {
             $this->_meta_columns[$row['tbl_name']][$row['col_name']] = $row;
         }
     }

     public function get_all()
     {
         return $this->_meta_columns;
     }
 }
