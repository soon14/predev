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



class wechat_finder_xcxpage{

    var $column_edit = '编辑';


    function column_edit($row){
        return '<a class="btn btn-default btn-xs" href="index.php?app=wechat&ctl=admin_xcxpage&act=edit&p[0]='.$row['id'].'" ><i class="fa fa-edit"></i> '.'编辑'.'</a>';
    }



}
