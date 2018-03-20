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
class o2ocds_service_store_createafter
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 店铺注册成功以后
     */
    public function exec($store, &$msg = '')
    {
        if($store['invitation'] && $store['member_id'] && $store['qrcode']) {
            $store['invitation'] = strtoupper($store['invitation']);
            $mdl_invitation = $this->app->model('invitation');
            if(!$invitation = $mdl_invitation->getRow('*',array('invitation_code'=>$store['invitation']))) {
                $msg = '未知邀请码';
                return false;
            };
            if(!$invitation['enterprise_id']) {
                $msg = '未知企业';
                return false;
            }
            if($invitation['store_id'] || $invitation['status'] == '1') {
                $msg = '邀请码已失效';
                return false;
            }
            if(!app::get('b2c')->model('member_lv')->getRow('member_lv_id',array('member_lv_id'=>$invitation['member_lv_id']))) {
                $msg = '未知邀请码等级';
                return false;
            };
            $invitation['use_member_id'] = $store['member_id'];
            $invitation['status'] = '1';
            $invitation['usetime'] = time();
            if($store['store_id']) {
                $invitation['store_id'] = $store['store_id'];
            }
            if(!$this->app->model('invitation')->save($invitation)) {
                logger::alert($msg.var_export($invitation,true));
                $msg = '邀请码操作失败';
                return false;
            };

            $mdl_qrcode = $this->app->model('qrcode');
            if(!$qrcode_id = $mdl_qrcode->get_qrcode_id($store['qrcode'],$msg)) {
                $msg = '未知二维码';
                return false;
            };
            if(!$qrcode_data = $mdl_qrcode->getRow('*',array('qrcode_id'=>$qrcode_id))) {
                $msg = '二维码异常';
                return false;
            };
            if($qrcode_data['store_id']) {
                $msg = '二维码已绑定店铺';
                return false;
            }
            if($qrcode_data['enterprise_id']) {
                if($qrcode_data['enterprise_id'] != $invitation['enterprise_id']) {
                    $msg = '邀请码与二维码信息错误';
                    return false;
                }
            }
            $qrcode_data['store_id'] = $store['store_id'];
            $qrcode_data['enterprise_id'] = $invitation['enterprise_id'];
            $qrcode_data['status'] = '1';
            if(!$mdl_qrcode->save($qrcode_data)) {
                $msg = '二维码信息更新失败';
                logger::alert($msg.var_export($qrcode_data,true));
                return false;
            };
            $mdl_store = $this->app->model('store');
            //修改店铺信息，绑定企业
            $num = $mdl_qrcode->count(array('enterprise_id'=>$qrcode_data['enterprise_id'],'store_id|notin'=>$store['store_id']));
            if($eno = $this->app->model('enterprise')->getRow('eno',array('enterprise_id'=>$qrcode_data['enterprise_id']))['eno']) {
                $num += 1;
                $newNumber = substr(strval($num+10000),1,4);
                $sno = $eno.$newNumber;
            }else{
                $sno = $mdl_store->apply_sno();
            };
            if(!$mdl_store->update(array('sno'=>$sno,'enterprise_id'=>$qrcode_data['enterprise_id']),array('store_id'=>$store['store_id']))) {
                $msg = '店铺编号信息更新失败';
                logger::alert($msg.'eno=>'.$sno);
                return false;
            };
            //保存关系
            $relation = array(
                'relation_id' => $store['store_id'],
                'member_id' => $store['member_id'],
                'type' => 'store',
                'relation' => 'manager',
                'time' => time()
            );
            if(!$this->app->model('relation')->save($relation)) {
                $msg = '店铺店长关系'.$store['name'].'关系保存失败';
                logger::alert($msg);
                return false;
            };
            if(!app::get('b2c')->model('members')->update(array('member_lv_id'=>$invitation['member_lv_id']),array('member_id'=>$store['member_id']))) {
                $msg = '更新店铺会员等级失败';
                logger::alert($msg);
                return false;
            };
        }else{
            $msg = '缺少参数';
            logger::error($msg);
            logger::error($store);
            return false;
        }

        return true;
    }

}
