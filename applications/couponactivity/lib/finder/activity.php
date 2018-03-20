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


 
class couponactivity_finder_activity{
	
    public function __construct($app)
    {
        $this->app = $app;
        $this->user_obj = vmc::singleton('desktop_user');
        if($this->user_obj->has_permission('sales_activity_edit')){
            $this->column_edit = '操作';
        }
    }
    
    function column_edit($row){
        $html = '<a class="btn btn-default btn-xs" href="index.php?app=couponactivity&ctl=admin_activity&act=edit&p[0]='.$row['activity_id'].'" ><i class="fa fa-edit"></i> '.('编辑').'</a>';
        //'<a href="index.php?app=couponactivity&ctl=admin_sales_activity&act=edit&p[0]'.$row['activity_id'].'" target="dialog::{title:\'优惠券活动编辑\',width:460,height:160}">编辑</a>';
        return $html;
    }
    
    
}
