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

class vshop_ctl_admin_voucher extends desktop_controller
{
    public function index()
    {
        if($this->has_permission('vshop_voucher_statement')){
            $actions[] = array(
                'label' => ('生成财务结算单'),
                'icon' => 'fa-paperclip',
                'data-submit' => 'index.php?app=vshop&ctl=admin_statement&act=create',
                'data-target' => '_ACTION_MODAL_',
            );
        }
        $this->finder('vshop_mdl_voucher', array(
            'title' => ('结算凭证'),
            'use_buildin_recycle'=>$this->has_permission('vshop_voucher_delete'),
            //'use_buildin_export'=>true,
            'use_buildin_set_tag'=>$this->has_permission('vshop_voucher_tag'),
            'use_buildin_filter' => true,
            'actions' => $actions,
        ));
    }
    public function update($voucher_id){
        $this->begin();
        $mdl_voucher = $this->app->model('voucher');
        $voucher = $mdl_voucher->dump($voucher_id);
        $data = $_POST;
        $op_name = $this->user->get_name();
        if($data['memo'] && trim($data['memo'])!=''){
            $data['memo'] = '['.date('Y-m-d H:i:s').']'.$op_name.'：'.$data['memo'];
            if($voucher['memo']){
                $voucher['memo'] = $voucher['memo'].'<br>'.$data['memo'];
            }else{
                $voucher['memo'] = $data['memo'];
            }
            $flag = $mdl_voucher->save($voucher);
            $this->end($flag);
        }else{
            $this->end(false,'备注不能为空');
        }

    }


}
