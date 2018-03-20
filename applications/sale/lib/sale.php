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


class sale_sale
{
    public function get_status($sale,$now){
        //商品预约还未开始
        if($sale['reserve_start'] > $now){
            return 'not';
        }
        //商品预约中
        if($sale['reserve_start'] <= $now && $sale['reserve_end'] > $now){
            return 'reserve';
        }
        //商品预约结束还未开始购买
        if($sale['reserve_end'] <= $now && $sale['start'] > $now){
            return 'wait';
        }
        //商品开始抢购
        if($sale['start'] <= $now && $sale['end'] > $now){
            return 'start';
        }
        //抢购结束
        if($sale['end'] <= $now ){
            return 'end';
        }
    }
}
