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


class community_finder_blog
{
    public $column_edit = '编辑';
    public function column_edit($row)
    {
        $return = '<a class="btn btn-default btn-xs" href="index.php?app=community&ctl=admin_blog&act=edit&p[0]='.$row['blog_id'].'" ><i class="fa fa-edit"></i>'.('编辑').'</a>';
        return $return;

    }

}
