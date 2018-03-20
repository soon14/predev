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
class commission_service_member
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 成为分佣者
     */
    public function become_commission($data){
        if(!$data['mobile']) throw new Exception("手机号码不能为空");
        if(!preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/' , $data['mobile'])) throw new Exception("手机号码不正确");
        if(!$data['vcode']) throw new Exception("验证码不能为空");
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $data['member_id']);

        //未绑定手机号
        if(!$pam_data['mobile']){
            //该步骤内部有验证码校验
            if(!vmc::singleton('b2c_user_passport')->set_mobile($data['mobile'],$data['vcode'],$msg)){
                throw new Exception($msg);
            }
        }else{
            if($pam_data['mobile']['login_account'] != $data['mobile']){
                throw new Exception('非法操作');
            }
            //验证码校验
            if (!vmc::singleton('b2c_user_vcode')->verify($data['vcode'], $data['mobile'], 'signup')) {
                throw new Exception('验证码错误!');
            }
        }

        $member_data = array(
            'member_id' => $data['member_id'],
            'domain_pre' => $domain = strtolower($data['domain_pre']),
            'is_commission' => '1',
            'commission_id' => $this->create_commission_id()
        );
        if(false == $this ->app ->model('member_relation') ->save($member_data)){
            throw new Exception('操作失败!');
        }
        $this ->set_commission_info($member_data);

    }
    /*
     * 如果是分佣者，则所有地址都跳转到自己的域名
     */
    public function redirect($url){
        $commission = $this ->get_commission_info();
        if($commission && vmc::singleton('b2c_user_object') ->is_login()){//必须登录后才有跳转
            preg_match('/^(\w+)\.\w+\.\w+[:\d+]*$/', $_SERVER['HTTP_HOST'], $matches);
            $domain = $commission['domain_pre'];
            if($domain != $matches[1]){
                $url = ($_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']:'http').'://'.$commission['domain_pre'].'.'.$this->app->getConf('root_domain').':'.($_SERVER['SERVER_PORT'] ==80 ?'':$_SERVER['SERVER_PORT']).$url;
                header('Location:'.$url);exit;
            }
        }
    }

    /*
     * 获取分佣者信息
     */
    public function get_commission_info(){
        $user_obj = vmc::singleton('b2c_user_object');
        $member_id = $user_obj ->get_member_id();
        if($member_id){
            if(empty($_COOKIE['commission'])){
                $member_info = $this ->app ->model('member_relation') ->getRow('*' ,array('member_id' => $member_id));
                if($member_info['is_commission'] == '1'){
                    $this ->set_commission_info($member_info);
                    return $member_info;
                }else{
                    return false;
                }
            }else{
                $commission_cookie = unserialize($_COOKIE['commission']);
                if($member_id == $commission_cookie['member_id']){
                    return $commission_cookie;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    /*
     * 暂存分佣者信息
     */
    public function set_commission_info($data){
        $user_obj = vmc::singleton('b2c_user_object');
        $member_id = $user_obj ->get_member_id();
        if($member_id && $member_id == $data['member_id']){
            $cookie_data = array(
                'member_id' => $data['member_id'] ,
                'commission_id' => $data['commission_id'] ,
                'domain_pre' => $data['domain_pre'] ,
            );
            setcookie('commission' ,serialize($cookie_data) ,null ,'/' ,COOKIE_DOMAIN);
        }else{
            return false;
        }
    }

    /*
     * 用户资金变动及日志,请在事务中调用
     */
    public function fund_change($data, &$msg)
    {
        if (!$data['member_id']) {
            $msg = "参数错误";

            return false;
        }
        $member = $this->app->model('member_relation')->getRow("*", array('member_id' => $data['member_id']));
        if (!$member) {
            $msg = "非法用户";

            return false;
        }
        $log_data = array(
            'member_id' => $member['member_id'],
            'change_fund' => $data['change_fund'],  //实际变动资金
            'frozen_fund' => $data['frozen_change'],
            'type' => $data['type'],
            'current_fund' => $member['used_fund'] + $data['change_fund'], //当前可用余额
            'opt_id' => $data['opt_id'],
            'opt_type' => $data['opt_type'] ? $data['opt_type'] : 'unknown',
            'opt_time' => $data['opt_time'] ? $data['opt_time'] : time(),
            'mark' => $data['mark'],
            'extfield' => $data['extfield']

        );
        $member_data = array(
            'member_id' => $member['member_id'],
            'frozen_fund' => $member['frozen_fund'] + $data['frozen_change'],//冻结资金
            'used_fund' => $member['used_fund'] + $data['change_fund'],  //可用资金
            'coin' => ($member['used_fund'] + $data['change_fund']) * ($this->app->getConf("exchange"))
        );
        if (false == $this->app->model('member_relation')->save($member_data)) {
            $msg = "用户资金变动错误";

            return false;
        }
        if (false == $this->app->model('fundlog')->save($log_data)) {
            $msg = "用户资金变动日志错误";

            return false;
        }

        return true;
    }

    /*
     * 生成分佣id
     */
    public function create_commission_id($member_id =0 , $length=8){
        $str = md5(uniqid().time().$member_id);
        $rand = rand(0, strlen($str) -$length);
        $str = strtoupper(substr($str , $rand , $length));
        $count = $this ->app->model('member_relation')->count(array('commission_id' => $str));
        if($count){
            $str = $this ->create_commission_id($member_id ,$length);
        }
        return $str;

    }

}