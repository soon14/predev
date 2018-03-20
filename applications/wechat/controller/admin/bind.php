<?php
/**
 * 微信公众账号绑定
 */
class wechat_ctl_admin_bind extends desktop_controller {
    /*
     * @param object $app
    */
    function __construct($app) {
        parent::__construct($app);
        $this->bindModel = app::get('wechat')->model('bind');
    } //End Function
    //绑定列表
    public function index() {
        if($this ->has_permission('wechat_edit')){
            $actions[]= array(
                'label' => ('添加公众号') ,
                'href' => 'index.php?app=wechat&ctl=admin_bind&act=bind_view',
                'icon' => 'fa-weixin'
            );
        }
        $this->finder('wechat_mdl_bind', array(
            'title' => '微信公众号' ,
            'actions' => $actions,
            'use_buildin_recycle' => $this ->has_permission('wechat_delete'),
        ));
    }
    /**
     * 微信公众账号配置页面
     */
    public function bind_view($id) {

        if ($id) {
            $bindInfo = $this->bindModel->getRow('*', array(
                'id' => $id
            ));
            $this->pagedata['data'] = $bindInfo ? $bindInfo : array();
        } else {
            $obj_stage = vmc::singleton('wechat_stage');
            $bindInfo['id'] = '';
            $bindInfo['eid'] = $obj_stage->gen_eid();
            $bindInfo['url'] = $obj_stage->get_api($bindInfo['eid']);
            $bindInfo['token'] = $obj_stage->gen_token();
            $bindInfo['aeskey'] = str_pad(md5(time()),43,'0',STR_PAD_RIGHT);
            $this->pagedata['data'] = $bindInfo;
        }
        $this->pagedata['wechat_type_select'] = array(
            'service' => '服务号',
            'subscription' => '订阅号'
        );
        $this->page('admin/bind.html');
    }
    /**
     * 绑定微信公众账号
     */
    public function save_bind() {
        $this->begin();
        $data = $_POST;
        if ($data['wechat_type'] == 'service') {
            if (empty($data['appid']) || empty($data['appsecret'])) {
                $this->end(false, '服务号的appid和appsecret必填!');
            }
        }

        if ($this->bindModel->count(array('name'=>$data['name'],'id|notin'=>array($data['id'])))) {
            $this->end(false, '公众账号名称重复');
        }
        if ($this->bindModel->count(array('wechat_account'=>$data['wechat_account'],'id|notin'=>array($data['id'])))) {
            $this->end(false, '微信号重复');
        }
        if ($this->bindModel->count(array('wechat_id'=>$data['wechat_id'],'id|notin'=>array($data['id'])))) {
            $this->end(false, '原始ID重复');
        }

        $this->bindModel->save($data);
        $this->end(true, '公众号信息已保存' , null, array(
            'id' => $data['id']
        ));
    }
}
