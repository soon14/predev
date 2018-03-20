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




class preselling_mdl_orders extends dbeav_model{
    var $has_tag = true;
    var $defaultOrder = array('createtime','DESC');

    /**
     * @params null
     * @return string 预售订单编号
     */
    public function apply_id()
    {
        $tb = $this->table_name(1);
        do{
            $i = substr(mt_rand() , -5);
            $new_order_id = '2'.(date('y')+date('m')+date('d')).date('His').$i;
            $row = $this->db->selectrow('SELECT id from '.$tb.' where id ='.$new_order_id);
        }while($row);

        return $new_order_id;
    }

    public function payment_money($order,&$msg) {
        if($order['status'] == '0') {
            return $order['deposit_price'];
        }elseif($order['status'] == '1') {
            $obj_math = vmc::singleton('ectools_math');
            $time = time();
            if($order['balance_starttime'] > $time ) {
                $msg = '尾款支付时间未开始';
                return false;
            }elseif($order['balance_endtime'] < $time) {
                $msg = '尾款支付时间已结束';
                return false;
            }
            return $obj_math->number_plus(array($order['balance_payment'],$order['cost_freight']));
        }elseif($order['status'] == '2') {
            $msg = '预售成功';
            return false;
        }elseif($order['status'] == '3') {
            $msg = '预售失败';
            return false;
        }
        $msg = '未知订单状态';
        return false;
    }

    /**
     * @params activity_id  活动id
     * @params member_id  会员id
     * @return string 查询主订单
     */
    public function orders_list($activity,$member_id) {
        $activity_id = $activity['activity_id'];
        if($orders_list = $this->getList("member_id,presell_id",array('activity_id'=>$activity_id,'main_id'=>0,'pay_status'=>array('1','2')))) {
            $member_ids = array_keys(utils::array_change_key($orders_list,'member_id'));
            $members = app::get('b2c')->model('members')->getList('member_id,avatar,name',array('member_id'=>$member_ids));
            $members = utils::array_change_key($members,'member_id');
            foreach($orders_list as &$order) {
                $order['member'] = $members[$order['member_id']];
                $order['count'] = $this->count(array('main_id'=>$order['presell_id'],'activity_id'=>$activity_id,'status|noequal'=>'2'))+1;
                $order['surplus_people'] = $activity['people_number']-$order['count'];
                if($order['member_id'] == $member_id) {
                    $order['is_member'] = true;
                }
            }
        };
        return $orders_list;
    }

    /*
     * 购买的商品数量
     * */
    public function sum_product($filter) {
        $filter = $this->_filter($filter);
        $SQL = "SELECT SUM(nums) as product_count FROM vmc_preselling_orders WHERE {$filter}";
        $count = $this->db->count($SQL);
        return $count;
    }

    public function refund_money($presell_id) {
        $mdl_bill = app::get('ectools')->model('bills');
        $filter = array(
            'order_id' => $presell_id,
            'pay_object' => 'gborder',
            'app_id' => 'preselling',
            'status' => 'succ',
        );
        $filter = $mdl_bill->_filter($filter);
        $SQL = "SELECT sum(money) as refund_money FROM `vmc_ectools_bills` WHERE $filter";
        $refund_money = $this->db->select($SQL);
        return $refund_money[0]['refund_money'];
    }

    /**
     * @params array  活动id,会员id,主订单号
     * @return string 检查该会员是否已经参加过该团
     */
    public function check(&$params,&$msg) {
        if(!$params['member_id']) {
            $msg = "未知会员";
            return false;
        }elseif(!$params['activity']['activity_id']) {
            $msg = "未知活动";
            return false;
        }
        $time = time();
        if($params['order']['presell_id']) {
            if($params['activity']['balance_starttime'] > $time) {
                $msg = "尾款支付时间未开始";
                return false;
            };
            if($params['activity']['balance_endtime'] < $time) {
                $msg = "尾款支付时间已结束";
                return false;
            };
            if($params['order']['status'] != '1') {
                $msg = "订单不是待支付尾款状态";
                return false;
            }
        }else{
            if($params['activity']['deposit_starttime'] > $time) {
                $msg = "定金支付时间未开始";
                return false;
            };
            if($params['activity']['deposit_endtime'] < $time) {
                $msg = "定金支付时间已结束";
                return false;
            };
        }
        if(!in_array($this->app->model('activity')->get_member_lv($params['member_id']),explode(',',$params['activity']['member_lv_ids']))) {
            $msg = "你暂时无法参与此活动";
            return false;
        }

        $current_product = $params['activity']['conditions'][$params['product_id']];
        //判断当前库存是否够
        $bn = app::get('b2c')->model('products')->getRow('bn',array('product_id'=>$current_product['product_id']));
        $obj_goods_stock = vmc::singleton('b2c_goods_stock');
        if(!$obj_goods_stock->is_available_stock($bn['bn'],$params['quantity'],$stock)) {
            $msg = "购买数量超购了，还能购买 ".($stock).' 件';
            return false;
        };
        if($current_product['restrict_number'] > 0) {
            $buy_number = $this->sum_product(array(
                'product_id'=>$params['product_id'],
                'activity_id'=>$params['activity']['activity_id'],
                'member_id'=>$params['member_id'],
                'status|noequal' => '3',
            ));
            $params['activity']['restrict_number'] = $current_product['restrict_number']-$buy_number;
            if($params['activity']['restrict_number'] > $stock) {
                $params['activity']['restrict_number'] = $stock;
            }
            if($params['quantity'] > $params['activity']['restrict_number']) {
                $msg = "购买数量超购了，还能购买 ".($params['activity']['restrict_number'] > 0?$params['activity']['restrict_number']:0).' 件';
                return false;
            }
        }else{
            $params['activity']['restrict_number'] = $stock;
        }

        return true;
    }

    public function modifier_order_id($col)
    {
        $opt_btn = '';
        if($col) {
            $opt_btn =  "<a href='index.php?app=b2c&ctl=admin_order&act=detail&p[0]=".$col."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> ".$col."</a>";
        }
        return $opt_btn;
    }






}
