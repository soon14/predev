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


class integraldeduction_cart_prefilter_deduction implements b2c_interface_cart_prefilter
{
    public function filter(&$cart_result, $config = false)
    {
        if (app::get('integraldeduction')->getConf('enabled') != 'true') {
            return false;
        }
        if (app::get('integraldeduction')->getConf('order_scale_enabled') == 'true') {
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
        foreach ($cart_goods_items as &$cart_object) {
            if ($cart_object['disabled'] == 'true') {
                continue;
            } //该项被禁用
            $goods_id_arr[] = $cart_object['item']['product']['goods_id'];
        }
        $mdl_optgoods = app::get('integraldeduction')->model('optgoods');
        $optgoods = $mdl_optgoods->getList('*', array('goods_id' => $goods_id_arr));

        if (!$optgoods) {
            return false;
        } else {
            $optgoods = utils::array_change_key($optgoods, 'goods_id');
        }
        //积分与货币汇率
        $currency_rate = app::get('integraldeduction')->getConf('currency_rate');
        $omath = vmc::singleton('ectools_math');
        foreach ($cart_goods_items as &$cart_object) {
            if ($member_integral_use <= 0) {
                break;
            }
            $goods_id = $cart_object['item']['product']['goods_id'];
            if (!$optgoods[$goods_id]) {
                continue;
            }
            $scale = $optgoods[$goods_id]['scale'];
            if ($scale > 1) {
                $scale = 1;
            }
            if ($scale < 0) {
                $scale = 0;
            }
            $max_deduction = $abs_deduction = $omath->number_multiple(array(
                $cart_object['item']['product']['buy_price'],
                $cart_object['quantity'],
                $scale,
            ));
            $max_integral_need = $omath->number_div(array(
                $max_deduction,
                $currency_rate,
            ));
            //$max_integral_need = round($max_integral_need);

            //积分不足,且抵扣比例被锁定
            if ($member_integral_use < $max_integral_need) {
                if ($optgoods[$goods_id]['lock_scale'] == 'true') {
                    continue;
                }
                $integral_used = $member_integral_use;
                $abs_deduction = $omath->number_multiple(array(
                    $integral_used,
                    $currency_rate,
                ));
            } else {
                $integral_used = $max_integral_need;
            }
            $cart_result['integraldeduction']['max'] += round($max_integral_need);
            $integral_used = round($integral_used);
            $this->_apply($cart_object, $cart_result, $integral_used, $abs_deduction);
            $member_integral_use -= $integral_used;
        }
    }
    /**
     *
     */
    private function _apply(&$cart_object, &$cart_result, $integral_used, $abs_deduction)
    {

        $omath = vmc::singleton('ectools_math');
        //修改购物车商品项成交价
        $cart_object['item']['product']['buy_price'] = $omath->number_minus(array(
            $cart_object['item']['product']['buy_price'],
            $omath->number_div(array(
                $abs_deduction,
                $cart_object['quantity'],
            )),
         ));
        //购物车优惠总计同步
        $cart_result['goods_promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['goods_promotion_discount_amount'],
            $abs_deduction,
        ));
        $cart_result['promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['promotion_discount_amount'],
            $abs_deduction,
        ));

        $pitem = array(
            'tag' => '积分抵扣', //促销规则标签
            'name' => '积分抵扣商品金额', //促销规则名称
            'desc' => "抵扣{$abs_deduction},消耗{$integral_used}积分.", //促销规则描述
            'rule_id' => '0', //促销规则ID
            'solution' => "抵扣{$abs_deduction},消耗{$integral_used}积分." , //促销规则触发方案
            'save' => $abs_deduction, //节省小计
        );
        $cart_result['promotions']['goods'][$cart_object['obj_ident']][] = $pitem;
        $cart_result['integraldeduction']['score_u'] += $integral_used;
        $cart_result['integraldeduction']['deduction'] = $omath->number_plus(array(
            $cart_result['integraldeduction']['deduction'],
            $abs_deduction,
        ));
    }
}
