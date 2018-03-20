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


interface site_interface_detail
{
    /**
     * 添加文章.
     *
     * @var array
     * @var array
     */
    public function add($index, $body);

    /**
     * 编辑文章.
     *
     * @var int
     * @var array
     * @var array
     */
    public function edit($article_id, $index, $body);

    /**
     * 发布文章.
     *
     * @var int
     * @var bool
     */
    public function publish($article_id, $pub = true);

    /**
     * 移除文章.
     *
     * @var int
     */
    public function remove($article_id);

    /**
     * 恢复文章.
     *
     * @var int
     */
    public function restore($article_id);

    /**
     * 移动文章.
     *
     * @var int
     * @var int
     */
    public function move($article_id, $node_id);

    /**
     * 复制文章.
     *
     * @var int
     * @var int
     */
    public function copy($article_id, $node_id);
}
