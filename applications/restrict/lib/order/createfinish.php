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




class restrict_order_createfinish
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 订单创建完成时 ,记录下单购买数量
     * @params array - 订单完整数据，含ITEMS
     * @return boolean - 执行成功与否
     */
    public function exec($sdf, &$msg = '')
    {
        $mdl_member_log = app::get('restrict')->model('member_log');

        $obj_restrict = vmc::singleton('restrict_check');
        $restrict_info = $obj_restrict->get_list($sdf['items'],$msg,false,true);
        $restrict_product = $restrict_info['product'];
        $restrict_goods = $restrict_info['goods'];
        foreach($sdf['items'] as  $product) {
            $product_id = $product['product_id'];
            $goods_id = $product['goods_id'];
            if((!($res_id = $restrict_product[$product_id]['res_id']) && !($res_id = $restrict_goods[$goods_id]['res_id']))) {
                continue;
            }
            $data = array(
                'res_id' => $res_id,
                'member_id' => $sdf['member_id'],
                'product_id' => $product_id,
                'goods_id' => $goods_id,
                'quantity' => $product['nums']
            );
            $data['order_id'] = $sdf['order_id'];
            $data['createtime'] = $sdf['createtime'];
            if(!$mdl_member_log->save($data)){
                $msg = '记录购买限购商品失败';
                return false;
            };
        }
        return true;
    }

}