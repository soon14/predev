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


class store_ctl_admin_store_dashboard extends store_ctl_admin_controller
{
    private $dashboard_from = '';
    private $dashboard_to = '';
    private $default_enter = true;
    /**
     * 是否显示查看店铺按钮
     *
     * @var bool
     */
    private $show_select_store_btn = false;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->_request = vmc::singleton('base_component_request');
        $this->show_select_store_btn = true;
    }

    /**
     * 店铺仪表盘
     */
    public function index()
    {
        //接收筛选条件
        $this->dashboard_from = $this->_request->get_post('f');
        $this->dashboard_to = $this->_request->get_post('t');
        $input_store_id = $this->_request->get_post('store_id');

        //是否强制显示单一店铺信息
        $show_single_store = false;
        if(is_numeric($input_store_id) && $input_store_id > 0){
            $store_id = $input_store_id;
            $show_single_store = true;//如果接收到了选中的店铺id,强制显示这个店铺的仪表盘信息
            $this->pagedata['store_info'] = $this ->app ->model('store') ->getRow("*" ,array('store_id'=>$input_store_id));
        }

        //处理筛选条件
        $this->handle_filter();

        //权限带入
        $is_super = $this->user->is_super();
        if ($is_super) {
            $group = ['-1'];
        }else{
            $group = (array) $this->user->group();
        }

        //根据权限获取仪表盘上显示的挂件
        $widgets = [];

        $obj_store_dashboard_widgets = vmc::servicelist('store.dashboard.widgets');
        foreach ($obj_store_dashboard_widgets as $key => $obj) {
            if ($is_super || in_array(get_class($obj), $group) == true) {

                #设置筛选条件
                $get_html_condition = [
                    'from' => $this->dashboard_from,
                    'to' => $this->dashboard_to,
                    'store_id' => $store_id,
                    'can_cashier_store_ids' => $this->can_cashier_store_ids,
                    'show_single_store' => $show_single_store
                ];
                if(is_callable([$obj, 'set_get_html_condition']) == true){
                    $obj->set_get_html_condition($get_html_condition);
                }

                $item = array(
                    'title'  => $obj->get_title(),
                    'layout' => $obj->get_layout(),
                    'html'   => $obj->get_html($this->dashboard_from, $this->dashboard_to),
                    'order'  => $obj->get_order(),
                );

                if (method_exists($obj, 'get_classname')) {
                    $item['classname'] = $obj->get_classname();
                }
                $widgets[$item['layout']][] = $item;
            }
        }


        $this->pagedata['widgets'] = $widgets;
        $this->pagedata['dashboard_from'] = $this->dashboard_from;
        $this->pagedata['dashboard_to'] = $this->dashboard_to;

        //快捷
        $this->pagedata['tody'] = date('Y-m-d H:i', strtotime(date('Y-m-d')));
        $this->pagedata['from_arr'] = array(
            'w' => date('Y-m-d H:i', strtotime('-1 week', strtotime($this->pagedata['tody']))),
            'm' => date('Y-m-d H:i', strtotime('-1 month', strtotime($this->pagedata['tody']))),
            'q' => date('Y-m-d H:i', strtotime('-3 month', strtotime($this->pagedata['tody']))),
        );

        $this->pagedata['show_select_store_btn'] = $this->show_select_store_btn;
        $this->pagedata['store_filter']['store_id'] = $this->can_cashier_store_ids;
        $this->page('admin/store_dashboard.html');
    }

    /**
     * 处理筛选条件
     */
    private function handle_filter(){
        //统一时间格式
        if (strpos($this->dashboard_from, ':')) {
            $this->dashboard_from = strtotime($this->dashboard_from);
            $this->dashboard_to = strtotime($this->dashboard_to);
        }

        //如果没有开始和结束日期
        #结束日期取当前时间
        if (!$this->dashboard_to) {
            $this->dashboard_to = strtotime(date('Y-m-d H:i:s'));
        } else {
            $this->default_enter = false;
        }
        #开始日期取当前时间前一周
        if (!$this->dashboard_from) {
            $this->dashboard_from = strtotime(date('Y-m-d'));
        } else {
            $this->default_enter = false;
        }

        //如果开始日期大于结束日期,把两个日期互换
        if ($this->dashboard_from > $this->dashboard_to) {
            $tmp_t = $this->dashboard_to;
            $this->dashboard_to = $this->dashboard_from;
            $this->dashboard_from = $tmp_t;
        }
    }
}
