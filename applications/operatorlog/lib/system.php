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



#系统管理
class operatorlog_system{

    function __construct(){
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
    }

    // 记录商店配置日志
    function logSystemConfigInfo($confinName,$pre_config,$now_config){
        $memo = '配置 ' . $confinName . ' 由 '. $pre_config . ' 修改为 ' . $now_config;

        $this->objlog->logs('site', '商店配置', $memo);
    }

    // 记录价格精度配置日志
    function logEctoolsConfigInfo($confinName,$pre_config,$now_config){
        $memo .= '配置 ' . $confinName . ' 由 '. $pre_config . ' 修改为 ' . $now_config;

        $this->objlog->logs('site', '价格精度配置', $memo);
    }

    // 记录支付自定义配置日志
    function logPaymentnoticeConfigInfo($confinName,$pre_config,$now_config){
        $memo .= '配置 ' . $confinName . ' 由 '. $pre_config . ' 修改为 ' . $now_config;

        $this->objlog->logs('site', '支付自定义设置', $memo);
    }

    // 记录登录配置日志
    function logAdminLoginInfo(){
        $account_mdl = vmc::singleton('pam_mdl_account');
        $desktop_user = $account_mdl->getList('account_id',array('login_name'=>$_POST['uname'],'account_type'=>'shopadmin'));

        if($desktop_user){
            $memo = '管理员帐号：'.$_POST['uname'];
            $this->objlog->logs('site', '管理员帐号登录', $memo);
        }
    }

    function removeArea($delarea){
        $this->objlog->logs('system', '删除地区', '删除地区 '.$delarea);
    }

    function pamsetting($predata,$currentdata){
        $modify_flag = false;
        if($predata['site_passport_status']['value'] != $currentdata['site_passport_status']['value']){
            $arr = array('true'=>'是','false'=>'否');
            $memo = '前台开启 修改为 ' . $arr[$currentdata['site_passport_status']['value']];
            $modify_flag = true;
        }
        if($modify_flag){
            $memo  = '编辑通行证 ' . $currentdata['passport_name']['value'].','.$memo;
            $this->objlog->logs('member', '登录认证设置', $memo);
        }
    }


    function dorecycle($delinfo){
        $key = $_GET['app'].'.'.$_GET['ctl'].'.'.$_GET['act'];
        $delarr = array(
            'b2c.admin_goods.index' =>array('goods', '删除商品',$delinfo['name']),
            'b2c.admin_goods_type.index' =>array('goods', '删除商品类型',$delinfo['name']),
            'b2c.admin_specification.index' =>array('goods', '删除商品规格',$delinfo['name']),
            'b2c.admin_brand.index' =>array('goods', '删除商品品牌',$delinfo['brand_name']),
            'ectools.admin_payment.index' =>array('order', '删除收款单',$delinfo['payment_id']),
            'ectools.admin_refund.index' =>array('order', '删除退款单',$delinfo['refund_id']),
            'b2c.admin_delivery.index' =>array('order', '删除发货单',$delinfo['delivery_id']),
            'b2c.admin_reship.index' =>array('order', '删除退货单',$delinfo['reship_id']),
            'b2c.admin_member.index' =>array('member', '删除会员',$delinfo['pam_account']['login_name']),
            'b2c.admin_member_lv.index' =>array('member', '删除会员等级',$delinfo['name']),
            'b2c.admin_sales_order.index' =>array('sales', '删除订单促销规则',$delinfo['name']),
            'b2c.admin_sales_goods.index' =>array('sales', '删除商品促销规则',$delinfo['name']),
            'giftpackage.admin_giftpackage.index' =>array('sales', '删除礼包',$delinfo['name']),
            'groupactivity.admin_purchase.index' =>array('sales', '删除团购',$delinfo['name']),
            'b2c.admin_sales_coupon.index' =>array('sales', '删除优惠券',$delinfo['cpns_name']),
            'b2c.admin_gift.index' =>array('sales', '删除赠品',$delinfo['info'][0]['name']),
            'bdlink.link.lists' =>array('sales', '删除推广链接',$delinfo['generatecode']),
            'site.admin_menu.index' =>array('site', '删除导航菜单',$delinfo['title']),
            'content.admin_article.index' =>array('site', '删除文章',$delinfo['title']),
            'ectools.currency.index' =>array('system', '删除货币',$delinfo['cur_name']),
            'b2c.admin_dlytype.index' =>array('system', '删除配送方式',$delinfo['dt_name']),
            'b2c.admin_dlycorp.index' =>array('system', '删除物流公司',$delinfo['name']),
            'desktop.users.index' =>array('system', '删除操作员',$delinfo['name']),
            'desktop.roles.index' =>array('system', '删除角色',$delinfo['role_name']),
        );
        if(array_key_exists($key,$delarr)){
            $this->objlog->logs($delarr[$key][0], $delarr[$key][1], $delarr[$key][1].' '.$delarr[$key][2]);
        }
    }


    function image_log($newdata,$olddata){
        $lmsname = array('L'=>'商品相册图设定','M'=>'商品页详细图设定','S'=>'列表页缩略图设定');
        foreach($newdata as $key1=>$val1){
            $modify_flag = 0;
            $data = array();
            foreach($val1 as $key2=>$val2){
                if($val1[$key2] != $olddata[$key1][$key2]){
                    $data['new'][$key2] = $val2;
                    $data['old'][$key2] = $olddata[$key1][$key2];
                    $modify_flag++;
                }
            }
            if($modify_flag>0){
                $memo  = "serialize".$this->delimiter."商品图片配置({$lmsname[$key1]})".$this->delimiter.serialize($data);
                $this->objlog->logs('goods', '商品图片配置', $memo);
            }
        }
    }


    function adminusers_log($newdata,$olddata){
        $modify_flag = 0;
        $data = array();
        foreach($newdata as $key=>$val){
            if($newdata[$key] != $olddata[$key]){
                $data['new'][$key] = $val;
                $data['old'][$key] = $olddata[$key];
                $modify_flag++;
            }
        }
        if($modify_flag>0){
            $memo  = "serialize".$this->delimiter."编辑操作员ID {$newdata['user_id']}".$this->delimiter.serialize($data);
            $this->objlog->logs('goods', '编辑操作员', $memo);
        }
    }


}//End Class
