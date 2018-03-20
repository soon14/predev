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




class groupbooking_mdl_orders extends dbeav_model{
    var $has_tag = true;
    var $defaultOrder = array('createtime DESC');

    /**
     * @params null
     * @return string 拼团订单编号
     */
    public function apply_id()
    {
        $tb = $this->table_name(1);
        do{
            $i = substr(mt_rand() , -5);
            $new_order_id = '1'.(date('y')+date('m')+date('d')).date('His').$i;
            $row = $this->db->selectrow('SELECT id from '.$tb.' where id ='.$new_order_id);
        }while($row);

        return $new_order_id;
    }

    /**
     * @params activity_id  活动id
     * @params member_id  会员id
     * @return string 查询主订单
     */
    public function orders_list($activity,$member_id) {
        $activity_id = $activity['activity_id'];
        if($orders_list = $this->getList("member_id,gb_id",array('activity_id'=>$activity_id,'main_id'=>0,'pay_status'=>array('1','2')))) {
            $member_ids = array_keys(utils::array_change_key($orders_list,'member_id'));
            $members = app::get('b2c')->model('members')->getList('member_id,avatar,name',array('member_id'=>$member_ids));
            $members = utils::array_change_key($members,'member_id');
            foreach($orders_list as &$order) {
                $order['member'] = $members[$order['member_id']];
                $order['count'] = $this->count(array('main_id'=>$order['gb_id'],'activity_id'=>$activity_id,'status|noequal'=>'2'))+1;
                $order['surplus_people'] = $activity['people_number']-$order['count'];
                if($order['member_id'] == $member_id) {
                    $order['is_member'] = true;
                }
            }
        };
        return $orders_list;
    }
    

    public function count_product($filter) {
        $filter = $this->_filter($filter);
        $SQL = "SELECT SUM(nums) as product_count FROM vmc_groupbooking_orders WHERE {$filter}";
        $count = $this->db->selectRow($SQL);
        return $count['product_count'];
    }

    public function refund_money($gb_id) {
        $mdl_bill = app::get('ectools')->model('bills');
        $filter = array(
            'order_id' => $gb_id,
            'pay_object' => 'gborder',
            'app_id' => 'groupbooking',
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
        if($params['quantity'] < 1) {
            $msg = "未知数量";
            return false;
        }
        if(!$params['member_id']) {
            $msg = "未知会员";
            return false;
        }elseif(!$params['activity']['activity_id']) {
            $msg = "未知活动";
            return false;
        }
        if(!in_array($this->app->model('activity')->get_member_lv($params['member_id']),explode(',',$params['activity']['member_lv_ids']))) {
            $msg = "你暂时无法参与此活动";
            return false;
        }
        if($params['main_id']) {
            if(!$main_order = $this->getRow('*',array('gb_id'=>$params['main_id'],'main_id'=>'0'))) {
                $msg = "未知团";
                return false;
            };
            if($main_order['is_failure'] == '1') {
                $msg = "该团已经失效";
                return false;
            }elseif($main_order['status'] == '1'){
                $msg = "已经成团";
                return false;
            }
            $table_name = $this->table_name(true);
            $SQL = "SELECT activity_id FROM {$table_name}
                WHERE activity_id = {$params['activity']['activity_id']} AND member_id = {$params['member_id']} AND (gb_id = {$params['gb_id']} OR main_id = {$params['gb_id']})";
            if($this->db->select($SQL)){
                $msg = "已参加过该团";
                return false;
            };
            $order_people_number = $this->count(array(
                'main_id' => $params['main_id'],
                'is_failure' => '0',
            ));
            $order_people_number += 1;
            if($order_people_number >= $params['activity']['people_number'] ) {
                $msg = "拼团参与人数已满";
                return false;
            }
        }
        $current_product = $params['activity']['conditions'][$params['product_id']];
        //判断当前库存是否够
        $bn = app::get('b2c')->model('products')->getRow('bn',array('product_id'=>$current_product['product_id']));
        $obj_goods_stock = vmc::singleton('b2c_goods_stock');
        if(!$obj_goods_stock->is_available_stock($bn['bn'],$params['quantity'],$stock)) {
            $msg = "购买数量超购了，还能购买 ".($stock).' 件';
            return false;
        };
        $buy_number = $this->count_product(array('is_failure'=>'0','product_id'=>$params['product_id'],'activity_id'=>$params['activity']['activity_id']));
        if($current_product['restrict_number'] > 0) {
            $params['activity']['surplus_number'] = $current_product['restrict_number']-$buy_number;
            if($params['activity']['surplus_number'] > $stock) {
                $params['activity']['surplus_number'] = $stock;
            }
            if($params['quantity'] > $params['activity']['surplus_number']) {
                $msg = "购买数量超购了，还能购买 ".($params['activity']['surplus_number'] > 0?$params['activity']['surplus_number']:0).' 件';
                return false;
            }
        }else{
            $params['activity']['surplus_number'] = $stock;
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
