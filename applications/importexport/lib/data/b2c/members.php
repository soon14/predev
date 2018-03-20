<?php

class importexport_data_b2c_members
{
    public function handle_rows(&$rows){

    }

    public function get_import_title()
    {
        return array(
            'login_account' => '登录帐号(account)',
            'member_lv_id'  => '会员等级(rank)',
            'avatar'        => '头像(portrait)',
            'order_num'     => '订单数(order amount)',
            'regtime'       => '注册时间(register time)',
            'sex'           => '性别(gender)',
            'mobile'        => '手机(mobile)',
            //            'taobao' => '淘宝账号(taobao)',
            //            'wechat'=>'微信(wechat)',
            'experience'    => '经验值(experience)',
            'name'    => '姓名(name)',
            'address'    => '地址(address)',
        );
    }

    /**
     *将导入的数据转换为sdf.
     *
     * @param array $contents 导入的一条会员数据
     * @param string $msg 传引用传出错误信息
     *
     * @return mixed
     */
    public function dataToSdf($contents, &$msg)
    {
        $members = current($contents);
        $membersData = [];

        try {
            $this->_check_column($members);

            //构造members表基础数据
            #$membersData['member_id'] = $this ->_get_account($members);
            $membersData['member_lv']['member_group_id'] = $this->_get_lv_id($members);
            $membersData['avatar'] = $members['avatar'];
            $membersData['order_num'] = $members['order_num'];
            $membersData['regtime'] = strtotime($members['regtime']) ? strtotime($members['regtime']) : time();
            $membersData['profile']['gender'] = $this->_get_sex($members);
            $membersData['contact']['phone']['mobile'] = $members['mobile'];
            $membersData['experience'] = $members['experience'];
            $membersData['regtime'] = strtotime($members['regtime']);

            //会员信息表导入时要同时插入pam_members表数据
            $membersData['pam_members_data'] = [
                'login_account' => $members['login_account'],
                'openid' => $members['openid'] ? $members['openid'] : '',
                'login_type' => $this->get_account_type($members['login_account']),
                'login_password' => md5(uniqid()),
                'password_account' => $members['login_account'],
                'createtime' => $membersData['regtime']
            ];
        } catch (Exception $e) {

            $msg = $e->getMessage();
        }

        return $membersData;
    }


    /**
     * 获取member_id在pam表中插入数据
     *
     * @param array $data 会员信息数据
     * @param string $msg 错误信息
     *
     * @return bool
     */
    public function import_after($data, &$msg = '')
    {
        $pam_members_data = $data['pam_members_data'];
        $pam_members_data['member_id'] = $data['member_id'];

        $save_pam_members_data_result = app::get('pam')->model('members')->save($pam_members_data);
        if(!$save_pam_members_data_result){
            $msg = '保存pam_members数据失败';

            return false;
        }

        return true;
    }

    /**
     * 会员信息校验
     *
     * @param array $members 会员信息
     *
     * @throws Exception
     */
    private function _check_column($members)
    {
        if (!$members['login_account']) {
            throw new Exception(app::get('importexport')->_('登陆账号不能为空!'));
        }

        if (app::get('pam')->model('members')->getRow('member_id', array('login_account' => $members['login_account']))) {
            throw new Exception(app::get('importexport')->_('登陆账号:') . $members['login_account'] . app::get('importexport')->_('已存在!'));
        }
    }


    /**
     * 会员等级
     *
     * @param $members
     *
     * @return string
     */
    private function _get_lv_id($members)
    {
        if ($members['member_lv_id']) {
            $member_lv_condition = [
                'name' => trim($members['member_lv_id'])
            ];
            $member_lv = app::get('b2c')->model('member_lv')->getRow('member_lv_id',$member_lv_condition);
            if (is_array($member_lv) == true) {

                return $member_lv['member_lv_id'];
            }
        }

        return '1';//注册会员
    }

    /**
     * 会员性别
     *
     * @param $members
     *
     * @return string
     */
    private function _get_sex($members)
    {
        if ($members['sex'] == '女') {

            return '0';
        }

        if ($members['sex'] == '男') {

            return '1';
        }

        return '-';
    }

    /**
     * 根据用户登陆账号获取账号类型
     *
     * @param string $login_account 登陆账号
     *
     * @return string
     */
    private function get_account_type($login_account){
        $account_type = 'local';

        if(preg_match('/^1[34578]\d{9}$/', $login_account) > 0){

            $account_type = 'mobile';
        }else if(preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i', $login_account) > 0){

            $account_type = 'email';
        }

        return $account_type;
    }
}
