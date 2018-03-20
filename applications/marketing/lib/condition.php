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
class marketing_condition{

    public function get_conditions($name){
        $conditions =  array(
            'order_count'=>array(
                'label' =>'交易总次数',
                'handle'=>'',
                'operator' =>array(
                    '>'=>'大于',
                    '='=>'等于',
                    '<'=>'小于',
                ),
                'view'=>'admin/conditions/order_count.html'
            ),
            'order_sum'=>array(
                'label' =>'交易总金额',
                'handle'=>'',
                'operator' =>array(
                    '>'=>'大于',
                    '='=>'等于',
                    '<'=>'小于',
                ),
                'view'=>'admin/conditions/order_sum.html'
            ),
            'order_month_count'=>array(
                'label' =>'平均每月订单数',
                'handle'=>'',
                'operator' =>array(
                    '>'=>'大于',
                    '='=>'等于',
                    '<'=>'小于',
                ),
                'view'=>'admin/conditions/order_month_count.html'
            ),
            'order_month_sum'=>array(
                'label' =>'平均每月交易金额',
                'handle'=>'',
                'operator' =>array(
                    '>'=>'大于',
                    '='=>'等于',
                    '<'=>'小于',
                ),
                'view'=>'admin/conditions/order_month_sum.html'
            ),
            'order_count_rank'=>array(
                'label' =>'总交易次数排名筛选',
                'handle'=>'',
                'operator' =>array(
                    '>='=>'排名前', //实际还是看交易次数大于多少
                    '<='=>'排名后',
                ),
                'view'=>'admin/conditions/order_count_rank.html'
            ),
            'order_sum_rank'=>array(
                'label' =>'总交易金额排名筛选',
                'handle'=>'',
                'operator' =>array(
                    '>='=>'排名前',
                    '<='=>'排名后',
                ),
                'view'=>'admin/conditions/order_sum_rank.html'
            ),
            'order_per_amount'=>array(
                'label' =>'平均订单价',
                'handle'=>'',
                'operator' =>array(
                    '>'=>'大于',
                    '='=>'等于',
                    '<'=>'小于',
                ),
                'view'=>'admin/conditions/order_per_amount.html'
            ),
            'order_items_quantity'=>array(
                'label' =>'交易商品总数',
                'handle'=>'',
                'operator' =>array(
                    '>'=>'大于',
                    '='=>'等于',
                    '<'=>'小于',
                ),
                'view'=>'admin/conditions/order_per_num.html'
            ),

        );
        return $name ? $conditions[$name] :$conditions;
    }

    public function is_meet($condition , $member ,$filter){
        $month = round(($filter['to_time']-$filter['from_time'])/3600/24/30 ,2);
        switch($condition['attribute']){
            case 'order_count':
                $value = $member['order_count'];
                break;
            case 'order_sum':
                $value = $member['order_sum'];
                break;
            case 'order_month_count':
                $value = $member['order_count']/$month;
                break;
            case 'order_month_sum':
                $value = $member['order_month_sum']/$month;
                break;
            case 'order_count_rank':
                //TODO 排名
                $value = $member['order_count'];
                $limit = ($condition['value']-1).',1';
                $order_by = $condition['operator'] == '>=' ?'desc' :'asc';
                $sql = "select count(1) as order_count from vmc_b2c_orders where {$filter['order_filter']} group by member_id order by order_count {$order_by} limit {$limit}";
                $count = vmc::database() ->selectrow($sql);
                $condition['value'] = $count['order_count'];
                break;
            case 'order_sum_rank':
                //TODO 排名
                $value = $member['order_sum'];
                $limit = ($condition['value']-1).',1';
                $order_by = $condition['operator'] == '>=' ?'desc' :'asc';
                $sql = "select sum(order_total) as order_sum from vmc_b2c_orders where {$filter['order_filter']} group by member_id order by order_sum {$order_by} limit {$limit}";
                $count = vmc::database() ->selectrow($sql);
                $condition['value'] = $count['order_sum'];
                break;
            case 'order_per_amount':
                $value = $member['order_sum']/$member['order_count'];
                break;
            case 'order_items_quantity':
                $value = $member['order_items_quantity'];
                break;
            default:
                return false;
        }

        $is_meet =eval('return '.$value.$condition['operator'].$condition['value'].';');
        return $is_meet;
    }

}