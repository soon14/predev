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
class commission_ctl_admin_orderlog extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_request = vmc::singleton('base_component_request');
    }

    public function all_count()
    {
        $dashboard_from = $this->_request->get_post('f');
        $dashboard_to = $this->_request->get_post('t');
        $default_enter = true;
        if (!$dashboard_to) {
            $dashboard_to = strtotime(date('Y-m-d'));
        } else {
            $default_enter = false;
        }
        if (!$dashboard_from) {
            $dashboard_from = strtotime('-1 week', $dashboard_to);
        } else {
            $default_enter = false;
        }
        //权限带入
        $user = vmc::singleton('desktop_user');
        $is_super = $user->is_super();
        $group = $user->group();
        $group = (array)$group;
        //统一时间格式
        if (strpos($dashboard_from, ':')) {
            $dashboard_from = strtotime($dashboard_from);
            $dashboard_to = strtotime($dashboard_to);
        }

        if ($dashboard_from > $dashboard_to) {
            $tmp_t = $dashboard_to;
            $dashboard_to = $dashboard_from;
            $dashboard_from = $tmp_t;
        }

        //桌面挂件排序，用户自定义
        if ($default_enter && cacheobject::get('desktop.commission.widgets' . $dashboard_to,
                $cache_data) && isset($cache_data['data'])
        ) {
            $this->pagedata['cached_time'] = $cache_data['cached_time'];
            $widgets = $cache_data['data'];
        } else {
            foreach (vmc::servicelist('commission.widgets') as $key => $obj) {
                if ($is_super || in_array(get_class($obj), $group)) {
                    $item = array(
                        'title' => $obj->get_title(),
                        'layout' => $obj->get_layout(),
                        'html' => $obj->get_html($dashboard_from, $dashboard_to),
                        'order' => $obj->get_order(),
                    );
                    if (method_exists($obj, 'get_classname')) {
                        $item['classname'] = $obj->get_classname();
                    }
                    $widgets[$item['layout']][] = $item;
                }
            }
            if ($default_enter) {
                $cache_data = array('cached_time' => time(), 'data' => $widgets);
                cacheobject::set('desktop.commission.widgets' . $dashboard_to, $cache_data, time() + 86400);//缓存24小时
            }
        }
        $this->pagedata['widgets'] = $widgets;
        $this->pagedata['dashboard_from'] = $dashboard_from;
        $this->pagedata['dashboard_to'] = $dashboard_to;
        //快捷
        $this->pagedata['tody'] = date('Y-m-d H:i', strtotime(date('Y-m-d')));
        $this->pagedata['from_arr'] = array(
            'w' => date('Y-m-d H:i', strtotime('-1 week', strtotime($this->pagedata['tody']))),
            'm' => date('Y-m-d H:i', strtotime('-1 month', strtotime($this->pagedata['tody']))),
            'q' => date('Y-m-d H:i', strtotime('-3 month', strtotime($this->pagedata['tody']))),
        );
        $this->page("admin/orderlog/all_count.html");
    }

    public function index()
    {
        $this->finder('commission_mdl_orderlog', array(
            'title' => ('分佣统计记录'),
            'use_buildin_recycle' => true,
        ));
    }

    /*
     * 单品统计
     */
    public function products()
    {
        $this->finder('commission_mdl_products_count', array(
            'title' => ('商品分佣记录'),
            'use_buildin_recycle' => true,
        ));
    }
}