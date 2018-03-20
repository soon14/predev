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


class store_foundation
{
    /**
     * 错误信息
     *
     * @var string
     */
    protected $msg = '';

    /**
     * 返回错误信息
     */
    public function getMsg(){

        return $this->msg;
    }
}
