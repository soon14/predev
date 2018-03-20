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


class store_ctl_admin_center extends desktop_controller
{
    private $is_super=false;
    public function __construct($app) {
        parent::__construct($app);
        $this->_request = vmc::singleton('base_component_request');
        $this ->is_super = vmc::singleton('desktop_user') ->is_super();
    }

    public function index(){
        $this ->finder('store_mdl_center',array(
            'title' =>'中央收银台',
            'actions' => array(
                array(
                    'label' => ('添加收银台') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=store&ctl=admin_center&act=edit',
                ) ,
            )
        ));
    }

    public function edit($center_id){
        if($this ->_request ->is_post()){
            $this ->begin('index.php?app=store&ctl=admin_center&act=index');
            $this ->app ->model('center') ->save($_POST);
            if($_POST['center_id']){
                $this ->app ->model('center_desktopuser') ->delete(array('center_id' =>$_POST['center_id']));
                foreach($_POST['user'] as $user_id){
                    $data = array(
                        'center_id' => $_POST['center_id'],
                        'user_id' => $user_id
                    );
                    if(!$this ->app ->model('center_desktopuser') ->save($data)){
                        $this ->end(false ,'操作失败');
                    }
                }
            }else{
                $this ->end(false ,'操作失败');
            }
            $this ->end(true ,'操作成功');
        }else{
            if($center_id){
                $center = $this ->app ->model('center') ->getRow('*' ,array('center_id' => $center_id));
                $users = $this ->app ->model('center_desktopuser') ->getList("*" ,array('center_id' =>$center_id));
                $this ->pagedata['user_list'] = app::get('desktop')->model('users') ->getList('*', array('user_id' =>array_keys(utils::array_change_key($users,'user_id'))));
                $this ->pagedata['store_list'] = $this->app ->model('store') ->getList('*', array('store_id' =>$center['store']));
                $this ->pagedata['center'] = $center;
            }
            $this ->page('admin/center/edit.html');
        }
    }

    public function ajax_center_store(){
        $store_list = $this->app ->model('store') ->getList('*', array('store_id' =>$_POST['store_id']));
        $this->pagedata['store_list'] = $store_list;

        $this->display('admin/center/ajax_store_item.html');
    }

    public function ajax_center_users(){
        $user_list = app::get('desktop')->model('users') ->getList('*', array('user_id' =>$_POST['user_id']));
        $this->pagedata['user_list'] = $user_list;
        $this->display('admin/center/ajax_user_item.html');
    }


    public function select(){
        if($this ->is_super){
            $center_id = null;
        }else{
            $center_id = $this ->_get_user_center();
        }
        $center = array();
        if($center_id !== false){
            //获取当前页
            $now_page = $_GET['page'] ? $_GET['page'] :1;
            $limit = 6;
            $total = $this ->app ->model('center') ->count($center_id);
            $total_page = ceil($total/$limit);
            $center = $this ->app ->model('center') ->getList('*' ,$center_id ,($now_page-1)*$limit ,$limit);
            $this->paging($now_page ,$total_page );
        }
        $this ->pagedata['center_list'] = $center;
        $this ->page('admin/center/select.html');
    }

    public function checkstand($id){
        if(!$id){
            //查找一个中台作为默认的
            $default_store = app::get('store')->model('center_desktopuser') ->getRow('*' ,array('user_id' =>$this ->user->user_id));
            $id = $default_store['center_id'];
        }

        if(!$this->is_super){
            $center_id = $this ->_get_user_center();
            if(!in_array($id ,$center_id['center_id'])){
                $this ->redirect('index.php?app=store&ctl=admin_center&act=select');
            }
        }
        $center = $this ->app ->model('center') ->getRow('*' ,array('center_id'=>$id));
        if($center){
            $_SESSION['center_id'] = $id;
            $this ->pagedata['center_info'] = $center;
            $this ->pagedata['user'] = $this ->user ->user_data;
            $this ->pagedata['report'] =array(
                'from' => date('Y-m-d 00:00'),
                'to' => date('Y-m-d H:i')
            );
            $this->singlepage('admin/center/checkstand.html', '中央收银台');
        }
        $this ->redirect('index.php?app=store&ctl=admin_center&act=select');

    }


    public function get_order_by_barcode(){
        $order_id = $_POST['filter'];
        $order = app::get('b2c') ->model('orders') ->dump($order_id);
        $order_store = app::get('store') ->model('relation_orders') ->getRow('*',array('order_id'=>$order_id));
        $order = array_merge($order_store ,$order);

        if($order){
            $center = $this ->app ->model('center') ->dump($_SESSION['center_id']);
            if(!in_array($order['store_id'] ,$center['store'])){
                $this ->splash('error' ,'' ,'您没有权限查看该订单');
            }
            $order['all_total'] = $order['finally_cart_amount']+$order['memberlv_discount']+$order['pmt_goods']+$order['pmt_order'];
            $order['pmt_total'] = $order['pmt_goods']+$order['pmt_order'];
            $order['need_pay'] = $order['order_total'] -$order['payed'];
            $order['items'] = app::get('b2c') ->model('order_items')->getList('*' ,array('order_id' => $order_id));
            $this ->splash('success' ,'' ,$order);
        }
        $this ->splash('error' ,'' ,'没有查询到该订单');
    }

    private function _get_user_center()
    {
        $center = $this ->app ->model('center_desktopuser') ->getList('*' ,array('user_id' =>$this->user->user_id));
        if(is_array($center)){
            return array('center_id' =>array_keys(utils::array_change_key($center ,'center_id')));
        }
        return false;
    }

    /**
     * 分页方法
     *
     * @param int $nowPage 当前页
     * @param int $totalPage 总页数
     * @param array $urlParams 构造url所需参数
     */
    public function paging($nowPage = 1, $totalPage = 1, $urlParams = [])
    {
        if (empty($urlParams['app']) === true) {
            $urlParams['app'] = 'store';
        }

        if (empty($urlParams['ctl']) === true) {
            $urlParams['ctl'] = 'admin_center';
        }

        if (empty($urlParams['act']) === true) {
            $urlParams['act'] = 'checkstand';
        }

        $token = time();

        if (is_array($urlParams['args']) === false) {
            $urlParams['args'] = [];
        }

        $urlParams['page'] = $token;

        $this->pagedata['pager'] = [
            'total'   => $totalPage,
            'current' => $nowPage,
            'link'    => vmc::router()->gen_url($urlParams),
            'token'   => $token,
        ];
    }
}