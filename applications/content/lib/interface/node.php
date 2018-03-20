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


interface content_interface_node
{
    /**
     * 添加节点.
     *
     * @var array
     */
    public function insert($params);

    /**
     * 编辑节点.
     *
     * @var int
     * @var array
     */
    public function update($node_id, $params);

    /**
     * 移除节点.
     *
     * @var int
     */
    public function remove($node_id);

    /**
     * 节点发布.
     *
     * @var int
     * @var bool
     */
    public function publish($node_id, $pub = true);
}
