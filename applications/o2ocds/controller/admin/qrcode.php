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
class o2ocds_ctl_admin_qrcode extends desktop_controller {

    public function index() {
        if($this->has_permission('o2ocds_add_qrcode')) {
            $custom_actions[] = array(
                'label' => ('添加二维码') ,
                'icon' => 'fa-plus',
                'href' => 'index.php?app=o2ocds&ctl=admin_qrcode&act=edit',
            );
        }
        $this->finder('o2ocds_mdl_qrcode', array(
            'title' => ('二维码列表'),
            'actions' => $custom_actions,
            'use_buildin_recycle' => $this->has_permission('o2ocds_delete_qrcode'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'use_buildin_export' => $this->has_permission('o2ocds_export_qrcode'),
        ));
    }

    /*
     * 操作二维码
     * */
    public function edit($qrcdoe_id) {
        if($qrcdoe_id) {
            $mdl_qrcode = app::get('o2ocds')->model('qrcode');
            if($qrcode = $mdl_qrcode->getRow('*',array('qrcode_id'=>$qrcdoe_id))) {
                $this->pagedata['qrcode'] = $qrcode;
            };
            if($qrcode['enterprise_id']) {
                $this->pagedata['enterprise'] = $this->app->model('enterprise')->getRow('enterprise_id,name',array('enterprise_id'=>$qrcode['enterprise_id']));
            }
            if($qrcode['store_id']) {
                $this->pagedata['store'] = $this->app->model('store')->getRow('store_id,name',array('store_id'=>$qrcode['store_id']));
            }
        }
        $this->page('admin/qrcode/edit.html');
    }

    /*
     * 保存二维码
     * */
    public function save() {
        $this->begin();
        $data = $_POST;
        $mdl_qrcode = app::get('o2ocds')->model('qrcode');
        if($data['qrcode_id']) {
            if(!$data['enterprise_id']) {
                $data['enterprise_id'] = $mdl_qrcode->getRow('*',array('qrcode_id'=>$data['qrcode_id']))['enterprise_id'];
            }
            if($data['store_id'] && !$data['enterprise_id']) {
                $this->end(false,'必须绑定企业');
            }elseif($data['store_id'] && $data['enterprise_id']) {
                $data['status'] = '1';
                $mdl_store = $this->app->model('store');
                if($store = $mdl_store->getRow('enterprise_id',array('store_id'=>$data['store_id'],'enterprise_id|than'=>'0'))) {
                    $this->end(false,'该店铺已经绑定了企业');
                };
                //修改店铺信息，绑定企业
                $num = $mdl_qrcode->count(array('enterprise_id'=>$data['enterprise_id'],'store_id|notin'=>$data['store_id']));
                if($eno = $this->app->model('enterprise')->getRow('eno',array('enterprise_id'=>$data['enterprise_id']))['eno']) {
                    $num += 1;
                    $newNumber = substr(strval($num+10000),1,4);
                    $sno = $eno.$newNumber;
                }else{
                    $sno = $mdl_store->apply_sno();
                };
                if(!$mdl_store->update(array('sno'=>$sno,'enterprise_id'=>$data['enterprise_id']),array('store_id'=>$data['store_id']))) {
                    $this->end(false,'店铺编号信息更新失败');
                };
            }
            if(!$mdl_qrcode->save($data)) {
                $this->end(false,'操作失败');
            };

            $this->end(true,'操作成功','index.php?app=o2ocds&ctl=admin_qrcode&act=edit&p[0]='.$data['qrcode_id']);
        }else{
            if(!$mdl_qrcode->create_qrcode($data,$msg)) {
                $this->end(false,$msg);
            };
            $this->end(true,'操作成功','index.php?app=o2ocds&ctl=admin_qrcode&act=index');
        }

    }

    /*
     * 批量操作二维码
     * */
    public function batch_edit() {
        $filter = $_POST;
        if(empty($filter)){
            echo('<div class="alert alert-warning">您正在操作全部商品!</div>');
            exit;
        }
        $this->pagedata['filter'] = htmlspecialchars(serialize($filter));
        $this->page('admin/qrcode/batch_edit.html');
    }

    /*
     * 批量保存二维码
     * */
    public function batch_save() {
        $this->begin();
        $filter = unserialize($_POST['filter']);
        unset($_POST['filter']);
        $mdl_qrcode = $this->app->model('qrcode');
        if(!$_POST['remark']) {
            unset($_POST['remark']);
        }
        if(!$mdl_qrcode->update($_POST,$filter)) {
            $this->end(true,'操作失败');
        };
        $this->end(true,'操作成功');
    }

    public function qrcode($qrcode) {
        $url_preview = $this->app->getConf('domain');
        $url_preview .= app::get('mobile')->router()->gen_url(array('app'=>'o2ocds','ctl'=>'mobile_qrrouter'));
        $url_preview .= '?qrcode='.$qrcode;
        // image  response
        ectools_qrcode_QRcode::png($url_preview,false,0,7,10);
    }




}
