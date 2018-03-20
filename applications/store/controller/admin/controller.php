<?php

class store_ctl_admin_controller extends desktop_controller
{

    /**
     * 当前后台登陆用户可操作店铺的id数组
     *
     * @var array
     */
    protected $can_cashier_store_ids = [];

    /**
     * 当前选中的操作的店铺id
     *
     * @var int
     */
    protected $now_selected_store = 0;

    public function __construct($app)
    {
        parent::__construct($app);

        //根据操作员id查询可操作店铺信息
        $this->check_user_store();

        //获取当前选择操作的店铺
        $this->now_selected_store = $_SESSION['now_selected_store'];

        $this->_request = vmc::singleton('base_component_request');
    }

    /**
     * 选择门店页面
     */
    public function select_store()
    {

        //获取当前页
        $nowPage = $_GET['page'];
        $storeCondition['store_id'] = $this->can_cashier_store_ids;
        $obj_pos_cash = vmc::singleton('store_pos_cash');

        //获取可操作的店铺信息,如果指定分页,还会返回分页信息
        if($_GET['store_name']){
            $condition['store_name|has'] = $_GET['store_name'];
        }
        if($_GET['store_bn']){
            $condition['store_bn|has'] = $_GET['store_bn'];
        }
        $storeInfos = $obj_pos_cash->get_can_cash_store_infos($condition, true, $nowPage);
        $this->pagedata['store_list'] = $storeInfos['storeInfos'];
        $this->pagedata['storeCount'] = $storeInfos['storeCount'];

        if ($storeInfos['pageInfo']) {
            $this->paging($storeInfos['pageInfo']['nowPage'], $storeInfos['pageInfo']['totalPage']);
        }

        $this->page('admin/pos/select_store_content.html');
    }

    /**
     * 保存选中的店铺
     */
    public function save_selected_store()
    {
        $store_id = $_POST['store_id'];
        if (is_numeric($store_id) == false || $store_id <= 0) {

            $this->splash('error', '', '店铺信息错误');
        }

        $redirect_url = "index.php?app=store&ctl=admin_checkstand&act=single_index&store_id={$store_id}&singlepage=1";
        $this->splash('success', $redirect_url, '选择成功');

    }

    /**
     *  根据操作员id查询可操作店铺信息
     */
    protected function check_user_store()
    {
        $modelRelationDesktopuser = app::get('store')->model('relation_desktopuser');
        $this->can_cashier_store_ids = $modelRelationDesktopuser->get_can_cashier_store_ids($this->user->user_id, $this->user->is_super());

        $this->pagedata['can_cashier_store_ids'] = $this->can_cashier_store_ids;
    }

    /**
     * 分页方法
     *
     * @param int $now_page 当前页
     * @param int $total_page 总页数
     * @param array $url_params 构造url所需参数
     */
    protected function paging($now_page = 1, $total_page = 1, $url_params =array())
    {
        $url_params['app'] = $url_params['app'] ?$url_params['app'] :'store';
        $url_params['ctl'] = $url_params['ctl'] ?$url_params['ctl'] :'admin_checkstand';
        $url_params['act'] = $url_params['act'] ?$url_params['act'] :'index';
        $url_params['args'] = $url_params['args'] ?$url_params['args'] :array();
        $token = time();
        $url_params['page'] = $token;

        $this->pagedata['pager'] = array(
            'total'   => $total_page,
            'current' => $now_page,
            'link'    => app::get('desktop')->router()->gen_url($url_params),
            'token'   => $token,
        );
    }

    /**
     * 根据店铺id获取店铺信息
     */
    public function ajax_get_store_info_by_store_id()
    {
        $store_id = $_POST['store_id'];
        if (is_numeric($store_id) == false || $store_id <= 0) {

            $this->splash('error', null, '错误的店铺id');
        }

        //查询店铺信息
        $model_store = app::get('store')->model('store');
        $store_columns = 'store_id, store_name, store_bn, store_area, store_address, store_contact';
        $store_info = $model_store->getList($store_columns, ['store_id' => $store_id]);
        if (is_array($store_info) == false || count($store_info) == 0) {

            $this->splash('error', null, '没有这个店铺');
        }

        $store_info = $store_info['0'];
        $store_info['parsed_store_area'] = vmc::singleton('base_view_helper')->modifier_region($store_info['store_area']);

        $this->splash('success', null, '没有这个店铺', null, ['store_info' => $store_info]);
    }


    /**获取订单及店铺信息
     * @param $order_id
     * @return array|bool
     */
    protected function get_order_info($order_id){
        $order = app::get('b2c')->model('orders')->dump($order_id);
        if(!$order){
            return false;
        }
        $order_store = app::get('store') ->model('relation_orders') ->getRow('*',array('order_id'=>$order_id));
        $order = array_merge($order_store ,$order);
        return $order;
    }
}