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


abstract class toauth_abstract
{

    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app)
    {
        $this->app = $app ? $app : app::get('toauth');
    }


    /**
     * 得到配置参数.
     *
     * @params string key
     * @payment api interface class name
     */
    protected function getConf($key, $pkey)
    {
        $val = app::get('toauth')->getConf($pkey);
        $val = unserialize($val);

        return $val[$key];
    }


    //信任登录回调真实登录操作 需要HTPP request 状态
    /**
     * $member_sdf @see like wechat/toauth/pam.php
     */
    protected function dologin($member_sdf,&$msg)
    {
        if(empty($member_sdf['pam_account']['openid'])||
        empty($member_sdf['pam_account']['password_account']) ||      empty($member_sdf['pam_account']['login_account'])||
        empty($member_sdf['pam_account']['login_type'])||
        empty($member_sdf['pam_account']['login_password'])||
        empty($member_sdf['pam_account']['createtime'])){
            $msg = '缺少pam_account必要参数!';
            return false;
        }
        $mdl_pam_members = app::get('pam')->model('members');
        $login_type = $member_sdf['pam_account']['login_type'];
        $openid = $member_sdf['pam_account']['openid'];
        $unionid = $member_sdf['pam_account']['unionid'];
        if(!empty($unionid)){
            $pam_member = $mdl_pam_members->getRow('*',array('unionid'=>$unionid,'login_type'=>$login_type));
        }
        if(!$pam_member || empty($pam_member)){
            $pam_member = $mdl_pam_members->getRow('*',array('openid'=>$openid,'login_type'=>$login_type));
            if(!empty($unionid)){
                $new_unionid = $unionid;
            }
        }
        if($pam_member && !empty($pam_member['member_id'])){
            $authenticated_member_id = $pam_member['member_id'];
            if(!$this->bind_member($authenticated_member_id,$msg)){
                return false;
            }
            if($new_unionid){
                $unionid_update_filter = array(
                    'member_id'=>$authenticated_member_id,
                    'openid'=>$openid,
                    'login_type'=>$login_type
                );
                $mdl_pam_members->update(array('unionid'=>$new_unionid),$unionid_update_filter);
                //更新unionid 到 pam_member
            }
            return $authenticated_member_id;
        }else{
            $member_id = $this->create_member($member_sdf,$msg);
            if(empty($member_id)){
                return false;
            }
            if(!$this->bind_member($member_id,$msg)){
                return false;
            }
            return $member_id;
        }

    }
    /**
     * 临时创建会员
     */
    protected function create_member($member_sdf,&$msg){
        $member_sdf = $this->pre_sdf($member_sdf);
        $mdl_members = app::get('b2c')->model('members');
        if(!$mdl_members->save($member_sdf)){
            $msg = '本地会员自动创建失败!';
            return false;
        }
        $member_id = $member_sdf['member_id'];

        foreach (vmc::servicelist('member.create_after') as $object) {
            $object->create_after($member_id);
        }

        return $member_id;

    }
    /**
     * 会员登录session \cookie
     */
    protected function bind_member($member_id,&$msg){
        //must in http
        vmc::singleton('b2c_user_object')->set_member_session($member_id);
        $frontpage_class = base_mobiledetect::is_mobile() ? 'b2c_mfrontpage' : 'b2c_frontpage';
        vmc::singleton($frontpage_class)->bind_member($member_id);
        //TODO

        return $member_id;

    }
    /**
     * 会员SDF数据包装处理
     */
    protected function pre_sdf($member_sdf){
        //头像存储
        if(!empty($member_sdf['avatar'])){
                $avatar_image_arr  = array(
                    'image_id' => md5($member_sdf['avatar']),
                    'storage' => 'network',
                    'image_name' => 'TOAUTH_HEAD_IMG_'.$member_sdf['pam_account']['password_account'],
                    'ident'=>md5($member_sdf['avatar']),
                    'url' => $member_sdf['avatar'],
                    'last_modified' => time(),
                );
                $mdl_image = app::get('image')->model('image');
                $mdl_image->save($avatar_image_arr);
                $member_sdf['avatar'] = $avatar_image_arr['image_id'];
        }

        $member_sdf['pam_account'] = array($member_sdf['pam_account']);
        return utils::_filter_input($member_sdf);
    }



}
