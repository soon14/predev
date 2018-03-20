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
class commission_service_member_save
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 用户等级修改后，经验变化
     */
    public function exec($member_id, &$msg = '')
    {
        $member_info = app::get('b2c')->model('members')->dump(array('member_id' => $member_id));
        $member_lv_id = $member_info['member_lv']['member_group_id'];
        $member_lv = app::get('b2c')->model('member_lv')->getList("*", array('disabled' => 'false'), 0, -1, null,
            'experience DESC');
        foreach ($member_lv as $k => $v) {
            if ($member_lv_id == $v['member_lv_id']) {
                $std_exp = $v['experience'];
                //更新会员经验值
                if ($member_info['experience'] < $std_exp || $member_info['experience'] >= $member_lv[$k + 1]['experience']) {
                    $re = app::get('b2c')->model('members')->update(array('experience' => $std_exp),
                        array('member_id' => $member_id));
                    if (!$re) {
                        $msg = '用户关系更新失败，error001';

                        return false;
                    }
                }
            }
        }
        if (false == $this->change_relation($member_id, $msg)) {
            return false;
        }

        return true;

    }

    /*
     * 用户等级变化，改变上下级关系,请在事务中执行
     */
    public function change_relation($member_id, &$msg = '')
    {
        if($this ->app->getConf('relation_change') == 2){//不跟随会员等级升迁
            return true;
        }
        $member_relation = $this->app->model('member_relation')->getRow('*', array('member_id' => $member_id));
        if ($member_relation['parent_id'] <1) {
            return true; //没有上级用户
        }
        $parent_member = app::get('b2c')->model('members')->dump(array('member_id' => $member_relation['parent_id']));
        $current_member = app::get('b2c')->model('members')->dump(array('member_id' => $member_id));
        $member_lv = app::get('b2c')->model('member_lv')->getList("*", array('disabled' => 'false'), 0, -1,
            'experience ASC');
        foreach ($member_lv as $k => $lv) {
            if ($lv['member_lv_id'] == $current_member['member_lv']['member_group_id']) {
                $current_key = $k;
                $current_exp = $lv['experience'];
            }
            if ($lv['member_lv_id'] == $parent_member['member_lv']['member_group_id']) {
                $parent_key = $k;
            }
        }
        $lv_step = $current_key - $parent_key;
        $tb_prefix = vmc::database()->prefix . "commission_";
        if ($lv_step >= 0) {
            //等级提升
            $update = $this->_find_parent($member_relation['parents'], $lv_step);
            if (false == $this->app->model('member_relation')->update($update, array('member_id' => $member_id ,'relation_change' =>'1'))) {
                $msg = '用户关系更新失败，error001';

                return false;
            }
            $children_new_parent_path =array('parent_path' => $member_id.','.$update['parent_id']) ;
            if(false == $this ->app ->model('member_relation') ->update($children_new_parent_path ,array('parent_id' => $member_id ,'relation_change' =>'1'))){
                $msg = '用户关系更新失败，error002';

                return false;
            }
            $old_parents = $member_relation['parents'] ? $member_id .','.$member_relation['parents'] : $member_id;
            $children_new_parents = $update['parents'] ? $member_id.','.$update['parents'] :$member_id;
            $sql = "UPDATE vmc_commission_member_relation SET parents = REPLACE(parents ,'{$old_parents}' ,'{$children_new_parents}') WHERE relation_change ='1' AND CONCAT(',', parents, ',')  LIKE '%,{$member_id},%' ";
            if (false == vmc::database()->exec($sql)) {
                $msg = '用户关系更新失败，error003';

                return false;
            };

        } else {
            //等级下降
            $children = $this->app->model('member_relation')->getList('*', array('parent_id' => $member_id));
            if(!empty($children)){
                $member_ids = implode(',', array_keys(utils::array_change_key($children, 'member_id')));
                $sql = "SELECT A.member_id, A.member_lv_id,B.experience FROM vmc_b2c_members AS A JOIN vmc_b2c_member_lv AS B ON A.member_lv_id = B.member_lv_id ";
                $where = " WHERE B.experience >= {$current_exp} AND A.member_id IN ({$member_ids})";
                $re = vmc::database()->select($sql . $where);
                if ($re) {
                    $update = array(
                        'parent_id' => $member_relation['parent_id'] ? $member_relation['parent_id'] : -1,
                        'parent_path' => $member_relation['parent_path'] ? $member_relation['parent_path'] : '-1',
                        'parents' => $member_relation['parents'] ? $member_relation['parents'] : '-1'
                    );
                    $where = array('member_id' => array_keys(utils::array_change_key($re, 'member_id')) ,'relation_change' =>'1');
                    if (false == $this->app->model('member_relation')->update($update, $where)) {
                        $msg = '用户关系更新失败，error004';

                        return false;
                    }
                }
            }
        }

        return true;

    }

    /*
     * 等级提升
     */
    private function _find_parent($parents, $step)
    {
        $parents = explode(',', $parents);
        if ($parents[$step + 1]) {
            $result = array(
                'parent_id' => $parents[$step + 1],
                'parent_path' => $parents[$step + 1] . ($parents[$step + 2] ? ',' . $parents[$step + 2] : '-1'),
                'parents' => implode(',', array_splice($parents, $step + 1))
            );
        } else {
            //平台直属用户
            $result = array(
                'parent_id' => -1,
                'parent_path' => '-1',
                'parents' => '-1'
            );
        }

        return $result;
    }
}