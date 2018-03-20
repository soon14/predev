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


class integraldeduction_cart_postfilter_deduction implements b2c_interface_cart_postfilter
{

    public function filter(&$filter, &$cart_result, $config = false)
    {
        if (app::get('integraldeduction')->getConf('enabled') != 'true') {
            return false;
        }
        if (app::get('integraldeduction')->getConf('order_scale_enabled') == 'false') {
            return false;
        }

        $current_member = vmc::singleton('b2c_cart_stage')->get_member();
        if (!$current_member) {
            return fasle;
        }
        $cart_goods_items = &$cart_result['objects']['goods'];
        // 没有商品数据
        if (empty($cart_goods_items)) {
            return false;
        }
        //会员积分额度
        $member_integral_position = $current_member['integral'];
        $member_integral_use = $_SESSION['INTEGRAL_DEDUCTION_USE'];

        if (!$member_integral_use || $member_integral_position == 0) {
            return false;
        }
        if ($member_integral_use > $member_integral_position) {
            //limit
            $member_integral_use = $_SESSION['INTEGRAL_DEDUCTION_USE'] = $member_integral_position;
        }

        //积分与货币汇率
        $currency_rate = app::get('integraldeduction')->getConf('currency_rate');
        $order_scale = app::get('integraldeduction')->getConf('order_scale');
        if ($order_scale > 1) {
            $order_scale = 1;
        }
        if ($order_scale < 0) {
            $order_scale = 0;
        }
        $omath = vmc::singleton('ectools_math');
        $max_deduction = $abs_deduction = $omath->number_multiple(array(
            $cart_result['finally_cart_amount'],
            $order_scale,
        ));

        $max_integral_need = $omath->number_div(array(
            $max_deduction,
            $currency_rate,
        ));

        //积分不足,且抵扣比例被锁定
        if ($member_integral_use < $max_integral_need) {
            $integral_used = $member_integral_use;
            $abs_deduction = $omath->number_multiple(array(
                $integral_used,
                $currency_rate,
            ));
        } else { 
            $integral_used = $max_integral_need;
        }
        $cart_result['integraldeduction']['max'] = round($max_integral_need);
        $integral_used = round($integral_used);
        $this->_apply_order($cart_result, $integral_used, $abs_deduction);
    }

    private function _apply_order(&$cart_result, $integral_used, $abs_deduction)
    {
        //$integral_used = round($integral_used);
        $omath = vmc::singleton('ectools_math');
        //购物车优惠总计同步
        $cart_result['order_promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['order_promotion_discount_amount'],
            $abs_deduction,
        ));
        $cart_result['promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['promotion_discount_amount'],
            $abs_deduction,
        ));
        $pitem = array(
            'tag' => '积分抵扣', //促销规则标签
            'rule_type'=>'normal',
            'name' => '积分抵扣订单金额', //促销规则名称
            'desc' => "抵扣{$abs_deduction},消耗{$integral_used}积分.", //促销规则描述
            'rule_id' => '0', //促销规则ID
            'solution' => "抵扣{$abs_deduction},消耗{$integral_used}积分." , //促销规则触发方案
            'save' => $abs_deduction, //节省小计
        );
        $cart_result['promotions']['order'][] = $pitem;

        $cart_result['integraldeduction']['score_u'] = $integral_used;
        $cart_result['integraldeduction']['deduction'] = $abs_deduction;
    }
}
