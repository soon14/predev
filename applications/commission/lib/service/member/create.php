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
class commission_service_member_create
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 注册成功
     */
    public function create_after($member_id){
        //若设定了默认等级，同步默认等级的经验值
        $member_info = app::get('b2c')->model('members') ->getRow('*' ,array('member_id' => $member_id));
        $member_lv = app::get('b2c')->model('member_lv') ->getRow('*' ,array('member_lv_id' => $member_info['member_lv_id']));
        app::get('b2c')->model('members')->update(array('experience' =>$member_lv['experience'] ) ,array('member_id' => $member_id));
        $parent = array();
        if($_COOKIE['dp']){
            $parent = $this ->app ->model('member_relation') ->getRow('*' ,array('domain_pre'=> $_COOKIE['dp'] ,'is_commission' =>'1'));
        }
        if($_COOKIE['fmid']){
            $parent = $this ->app ->model('member_relation') ->getRow('*' ,array('member_id'=> $_COOKIE['fmid'] ,'is_commission' =>'1'));
        }
        $this ->create_relation($member_id , $parent);
    }

    /*
     *查找上级，建立关系
     */
    public function create_relation($member_id, $parent)
    {
        if($parent){
            if($this ->app->getConf('relation_change') == 2){//不跟随会员等级升迁
                $data = array(
                    'relation_change' => '2',
                    'parent_id' => $parent['member_id'],
                    'parent_path' => $parent['parent_id'] ? $parent['member_id'].','.$parent['parent_id'] : $parent['member_id'],
                    'parents' => $parent['parents']? $parent['member_id'].','.$parent['parents'] : $parent['member_id'],
                );
            }else{
                $parent_member = app::get('b2c') ->model('members') ->getRow('member_lv_id' ,array('member_id' => $parent['member_id']));
                $current_member = app::get('b2c') ->model('members') ->getRow('member_lv_id' ,array('member_id' => $member_id));
                $member_lv = app::get('b2c')->model('member_lv')->getList("*", array('disabled' => 'false'), 0, -1,
                    'experience ASC');
                $member_lv = utils::array_change_key($member_lv ,'member_lv_id');
                if($member_lv[$parent_member['member_lv_id']]['experience'] > $member_lv[$current_member['member_lv_id']]['experience']){
                    $data = array(
                        'relation_change' => '1',
                        'parent_id' => $parent['member_id'],
                        'parent_path' => $parent['parent_id'] ? $parent['member_id'].','.$parent['parent_id'] : $parent['member_id'],
                        'parents' => $parent['parents']? $parent['member_id'].','.$parent['parents'] : $parent['member_id'],
                    );
                }
            }

        }
        $data ['member_id'] = $member_id;
        $data ['relation_change'] = $this ->app ->getConf('relation_change');
        $this->app->model('member_relation')->save($data);
    }
}