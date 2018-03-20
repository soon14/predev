
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


class community_ctl_mobile_relation extends community_mcontroller
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
    }

    public function my_relation_user()
    {
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($this->app->member_id);
        $mdl_relation = $this->app->model('relation');
        $relation_data = $mdl_relation->getRelationList($user['user_id']);
        $this->pagedata['my_relation_user'] = $relation_data;
        $this->page('mobile/default.html');
    }
    public function unrelation()
    {
        $this->begin();
        $unrelation_id = $_POST['user_id'];
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($this->app->member_id);
        $filter = array(
            'user_id' => $user['user_id'],
            'relation_id'=>$unrelation_id
        );
        $mdl_relation = $this->app->model('relation');
        $is_unrelationed = $mdl_relation->delete($filter);
        $this->end($is_unrelationed);
    }
    public function my_relation_map()
    {
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($this->app->member_id);
        $filter = array(
            'user_id' => $user['user_id'],
        );
        $mdl_relation = $this->app->model('relation');
        $relation_data = $mdl_relation->getList('*', $filter);
        $map = array();
        foreach ($relation_data as $key => $value) {
            $map['_'.$value['relation_id']] = 1;
        }
        $this->pagedata['my_relation_map'] = $map;
        $this->page('mobile/default.html');
    }
    public function follow($relation_user_id)
    {
        $this->begin();
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($this->app->member_id);
        if (!$user || !$user['user_id']) {
            $this->end(flase, '未知用户');
        }
        if ($mdl_users->count(array('user_id' => $relation_user_id))) {
            $mdl_relation = $this->app->model('relation');
            $new_relation = array(
                'user_id' => $user['user_id'],
                'relation_id' => $relation_user_id,
                'bind_relation_time' => time(),
            );
            if ($mdl_relation->save($new_relation)) {
                if (!$mdl_users->update_user_count($user['user_id'], 'follow_count', 1)) {
                    $this->end(false, '操作失败:关注计数失败');
                }
                $this->end(true, '操作成功');
            } else {
                $this->end(false, '操作失败:关系保存失败');
            }
        } else {
            $this->end(false, '关注失败:未知关注用户');
        }
    }
}
