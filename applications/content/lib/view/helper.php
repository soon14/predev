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


class content_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function function_WIDGET_CONTENT_NODE($params, &$smarty)
    {
        $tree = vmc::singleton('content_openapi_node')->tree($params,true);
        $render = new base_render(app::get('content'));
        $render->pagedata['tree'] = $tree;
        $render->pagedata['class_name'] = $params['class'];
        $render->pagedata['target'] = $params['target'];
        return $render->fetch('widget/node_tree.html');
    }


}
