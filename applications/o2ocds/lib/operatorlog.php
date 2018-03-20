<?php
class o2ocds_operatorlog{

    public function __construct(&$app)
    {
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
        $this->app = $app;
    }

    //关系操作的日志记录
    public function relation_log($params,$action) {
        if($params['store_id']) {
            $type = '店铺';
            $name = $this->app->model('store')->getRow('name',array('store_id'=>$params['store_id']))['name'];
            $relation_id = $params['store_id'];
        }elseif($params['enterprise_id']){
            $type = '企业';
            $name = $this->app->model('enterprise')->getRow('name',array('enterprise_id'=>$params['enterprise_id']))['name'];
            $relation_id = $params['enterprise_id'];
        }else{
            return ;
        }
        $login_account = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$params['member_id']))['login_account'];
        $memo_content = array(
            'new' => array(
                '登录账号' => $login_account,
                '分佣关系' => $this->relation_name($params['relation']),
            ),
            'old' => array(
                '登录账号' => $login_account,
                '分佣关系' => $this->relation_name($this->app->model('relation')->getRow('relation',array('relation_id'=>$relation_id))['relation']),
            )
        );
        $memo = 'serialize'.$this->delimiter.$this->action_name($action).'关系-'.$type.'名：'.$name.$this->delimiter.serialize($memo_content);
        $this->objlog->logs('member', $this->action_name($action).$type.'--'.$this->relation_name($params['relation']).'关系', $memo);
    }

    //企业操作的日志记录
    public function enterprise_log($data,$action = 'update') {
        if(!$data['enterprise_id'] && $action != 'delete') {
            $action = 'add';
        }
        $memo_content['new'] = $data;
        if($data['enterprise_id']) {
            $memo_content['old'] = $this->app->model('enterprise')->dump($data['enterprise_id']);
        }
        $memo  = 'serialize'.$this->delimiter.$this->action_name($action)."企业 ".$this->delimiter.serialize($memo_content);
        $this->objlog->logs('enterprise',$this->action_name($action).'o2o分销企业',$memo);
    }

    //店铺操作的日志记录
    public function store_log($data,$action = 'update') {
        if(!$data['store_id'] && $action != 'delete') {
            $action = 'add';
        }
        $memo_content['new'] = $data;
        if($data['store_id']) {
            $memo_content['old'] = $this->app->model('store')->dump($data['store_id']);
        }
        $memo  = 'serialize'.$this->delimiter.$this->action_name($action)."店铺 ".$this->delimiter.serialize($memo_content);
        $this->objlog->logs('enterprise',$this->action_name($action).'o2o分销店铺',$memo);
    }

    //结算凭证操作的日志记录
    public function achieve_log($data) {
        $memo_content['new'] = $data;
        $memo  = 'serialize'.$this->delimiter."删除结算凭证 ".$this->delimiter.serialize($memo_content);
        $this->objlog->logs('enterprise','删除结算凭证',$memo);
    }

    //结算单操作的日志记录
    public function statement_log($data,$action = 'update') {
        if($action == 'delete') {
            $memo_content['new'] = $data;
        }elseif($action == 'update') {
            $memo_content['new'] = $data;
            if($data['statement_id']) {
                $memo_content['old'] = $this->app->model('statement')->getRow('*',array('statement_id'=>$data['statement_id']));
            }
        }elseif($action == 'add') {
            $memo_content['new'] = $data;
        }else{
            return;
        }
        $memo  = 'serialize'.$this->delimiter.$this->action_name($action)."结算单 ".$this->delimiter.serialize($memo_content);
        $this->objlog->logs('enterprise',$this->action_name($action).'结算单',$memo);
    }


    private function relation_name($relation) {
        $relation_set = array(
            'admin' => '管理员',
            'salesman' => '业务员',
            'manager' => '店长',
            'salesclerk' => '店员',
        );
        return $relation_set[$relation];
    }

    private function action_name($action) {
        $action_set = array(
            'add' => '添加',
            'update' => '修改',
            'delete' => '删除',
        );
        return $action_set[$action];
    }

}