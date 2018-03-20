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
class vmcocean_service_mobile_construct
{
    /**
     * 广告投放追踪cookie埋点
     * @param $request
     */
    public function exec($request)
    {
        $params = $request->get_params(true);
        if ($params && count($params) > 0) {
            foreach ($params as $k => $v) {
                if ($k) {
                    $head = strtolower(substr($k, 0, 3));
                    logger::error('666' . $k . $head);
                    if ($head === 'utm') {
                        logger::error('777' . $k . $head);
                        // 广告投放追踪cookie埋点保留45天
                        setCookie(strtoupper($k), urlencode($v), time() + 60 * 60 * 24 * 45/*45 DAY*/, '/');
                    }
                }
            }
        }
    }
}