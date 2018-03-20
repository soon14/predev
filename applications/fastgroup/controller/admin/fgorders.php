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

class fastgroup_ctl_admin_fgorders extends desktop_controller
{
    public function index()
    {
        $this->finder('fastgroup_mdl_fgorders', array(
            'title' => ('团购纪录列表'),
            'use_buildin_filter' => true,
            'use_buildin_set_tag'=> true,
        ));
    }

    public function finish($skey){
        //完成订单.
        $this->begin();
        $mdl_fgorders = $this->app->model('fgorders');
        $fgorder = $mdl_fgorders->dump($skey);
        $subject = $this->app->model('subject')->dump($fgorder['subject_id']);
        $order_id = $fgorder['order_id']; //发货订单
        $mdl_orders = app::get('b2c')->model('orders');
        $order = $mdl_orders->dump($order_id,'*', array(
            'items' => array(
                '*',
            )
        ));
        if ($order['is_cod'] != 'Y' && $order['pay_status'] < 1) {
            $this->end(false, '订单未付款!');
        }
        $delivery_sdf = array(
            'order_id' => $order_id,
            'delivery_type' => 'send', //发货
            'member_id' => $order['member_id'],
            'op_id' => $this->user->user_id,
            'status' => 'succ',
            'memo' => '快团|'.$subject['fg_title'].'|'.$fgorder['skey'],
        );
        foreach ($order['items'] as $item) {
            $send_arr[$item['item_id']] = $item['nums'];
        }
        $obj_delivery = vmc::singleton('b2c_order_delivery');
        if (!$obj_delivery->generate($delivery_sdf, $send_arr, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
            $this->end(false, $msg);
        }
        $sdf['order_id'] = $order_id;
        $sdf['op_id'] = $this->user->user_id;
        $sdf['op_name'] = $this->user->user_data['account']['login_name'];
        if (vmc::singleton('b2c_order_end')->generate($sdf, $msg)) {
            $fgorder_update = array(
                'skey'=>$skey,
                'order_status'=>'finish'
            );
            if($mdl_fgorders->save($fgorder_update)){
                $this->end(true, '订单完成归档成功！');
            }else{
                $this->end(false, '订单完成归档失败！');
            }
        } else {
            $this->end(false, '订单完成归档失败！'.$msg);
        }
    }

    public function cancel($skey){
        //关闭订单
        $this->begin();
        $mdl_fgorders = $this->app->model('fgorders');
        $fgorder = $mdl_fgorders->dump($skey);
        $order_id = $fgorder['order_id'];
        $sdf['order_id'] = $order_id;
        $sdf['op_id'] = $this->user->user_id;
        $sdf['op_name'] = $this->user->user_data['account']['login_name'];

        if (vmc::singleton('b2c_order_cancel')->generate($sdf, $msg)) {
            $this->end(true, ('订单取消成功！'));
        } else {
            $this->end(false, ('订单取失败！'.$msg));
        }

    }

}
