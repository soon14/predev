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


class store_pos_cash
{
    /**
     * 查询可操作的店铺的信息
     *
     * @param array $storeCondition 查询条件
     * @param bool|false $paging 是否分页
     * @param int $nowPage 当前页
     * @param int $pageSize 每页显示多少条
     *
     * @return array
     */
    public function get_can_cash_store_infos($storeCondition = [], $paging = false, $nowPage = 1, $pageSize = 6)
    {
        $return = [];

        $modelStore = app::get('store')->model('store');
        //根据条件查询店铺数量
        $storeCount = $modelStore->count($storeCondition);
        $return['storeCount'] = $storeCount;
        if ($storeCount == 0) {

            return $return;
        }

        if ($paging === true) {
            $totalPage = ceil($storeCount / $pageSize);
            if ($nowPage < 1) {
                $nowPage = 1;
            } else if ($nowPage > $totalPage) {
                $nowPage = $totalPage;
            }

            $start = ($nowPage - 1) * $pageSize;

            $return['pageInfo'] = [
                'nowPage'   => $nowPage,
                'totalPage' => $totalPage,
                'pageSize'  => $pageSize,
            ];
        } else {
            $start = 0;
            $pageSize = -1;
        }

        //查询店铺数据
        $storeInfos = $modelStore->getList('*', $storeCondition, $start, $pageSize);

        //处理店铺数据
        $objViewHelper = vmc::singleton('base_view_helper');
        foreach ($storeInfos as $key => $storeInfo) {
            $storeInfo['addrDetail'] = $objViewHelper->modifier_region($storeInfo['store_area']) . $storeInfo['store_address'];

            $storeInfos[$key] = $storeInfo;
        }

        $return['storeInfos'] = $storeInfos;

        return $return;
    }
}
