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


/**
* 该类主要是用来记录后台管理员的操作日志
*/
class operatorlog_logs{

    public function logSetTagInfo(){
        $data['app'] = $_GET['app'];
        $data['ctl'] = $_GET['ctl'];
        $data['act'] = $_REQUEST['action'] ? $_REQUEST['action'] : $_GET['act'];
        $log_key = $data['app'].'-'.$data['ctl'].'-'.$data['act'];
        $register = array(
            'b2c-admin_goods-settag'  => array('app'=>'b2c', 'ctl'=>'admin_goods', 'act'=>'setTag', 'module'=>'goods','operate_type'=>'设置商品标签', 'template'=>'商品ID %s 批量设置商品标签 %s', 'param'=>'goods_id'),
            'b2c-admin_member-settag' => array('app'=>'b2c', 'ctl'=>'admin_member', 'act'=>'setTag', 'module'=>'member','operate_type'=>'设置会员标签', 'template'=>'会员ID %s 批量设置会员标签 %s', 'param'=>'member_id'),
            'b2c-admin_order-settag'  => array('app'=>'b2c', 'ctl'=>'admin_order', 'act'=>'setTag', 'module'=>'order','operate_type'=>'设置订单标签', 'template'=>'订单ID %s 批量设置订单标签 %s', 'param'=>'order_id'),
            'content-admin_article-settag'  => array('app'=>'content', 'ctl'=>'admin_article', 'act'=>'setTag', 'module'=>'content','operate_type'=>'设置文章标签', 'template'=>'文章ID %s 批量设置文章标签 %s', 'param'=>'article_id'),
            'image-admin_manage-settag'  => array('app'=>'image', 'ctl'=>'admin_manage', 'act'=>'setTag', 'module'=>'image','operate_type'=>'设置图片标签', 'template'=>'图片ID %s 批量设置图片标签 %s', 'param'=>'image_id'),
            'aftersales-admin_returnproduct-settag'  => array('app'=>'aftersales', 'ctl'=>'admin_returnproduct', 'act'=>'setTag', 'module'=>'aftersales','operate_type'=>'设置售后服务标签', 'template'=>'售后服务单ID %s 批量设置售后服务标签 %s', 'param'=>'return_id'),
        );
        $ids = '';
        $unseria_ids = unserialize($_POST['filter']);
        foreach($unseria_ids[$register[$log_key]['param']] as $value){
            $ids .= $value.',';
        }
        $tagName = '';
        foreach($_POST['tag']['stat'] as $key=>$value){
            if($value=='0'){
                $tagName .= $_POST['tag']['name'][$key].',';
            }
        }

        $obj = new desktop_user();
        $data['username'] = ($obj->get_login_name())?($obj->get_login_name()):'system_core';
    
        $data['module'] = $register[$log_key]['module'];
        $data['operate_type'] = $register[$log_key]['operate_type'];
        $data['dateline'] = time();
        $data['memo'] = sprintf($register[$log_key]['template'],$ids,$tagName);
        app::get('operatorlog')->model('normallogs')->insert($data);
    }


}//End Class