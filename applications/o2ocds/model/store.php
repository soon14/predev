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


class o2ocds_mdl_store extends dbeav_model
{
    public $has_tag = true;
    public $defaultOrder = array(
        'store_id DESC',
    );
    public $has_many = array(
        'images' => 'image_attach:contrast:store_id^target_id',
    );
    public $has_one = array();
    public $subSdf = array(
        'default' => array(
            'images' => array(
                'image_id',
            ),
        ),
        'delete' => array(
            'images' => array(
                '*',
            ),
        ),
    );

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function check(&$store,&$msg) {
        if(!$store['store_id']) {
            $store['apply_time'] = time();
        }
        if(!$store['name']) {
            $msg = '店铺名称不能为空';
            return false;
        }
        if($store['member_id']) {
            if($this->app->model('relation')->getRow('*',array('member_id'=>$store['member_id'],'relation|notin'=>'manager'))) {
                $msg = '已有其他身份';
                return false;
            }
        }
        if($this->getRow('store_id',array('name'=>$store['name'],'store_id|notin'=>$store['store_id']))) {
            $msg = '店铺名称重复';
            return false;
        };

        return true;
    }

    public function get_qrcode_ids($store_id){
        if($qrcode_ids = app::get('o2ocds')->model('qrcode')->getList('qrcode_id',array('store_id'=>$store_id))) {
            $qrcode_ids = array_keys(utils::array_change_key($qrcode_ids,'qrcode_id'));
            return $qrcode_ids;
        };
        return false;
    }

    /*
     * 店铺下的店员
     * */
    public function relation_clerk($relation_id,$filter = array(),$offset = 0, $limit = -1,$orderType = null) {
        if(!$relation_id) {
            return false;
        }
        if($filter) {
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $where_str = substr($dbeav_filter->dbeav_filter_parser($filter, 'sc', false, app::get('o2ocds')->model('service_code')),'1');
        }
        $IN_SQL = "SELECT vor.time as bind_time,vor.member_id,vor.relation,vor.relation_id as store_id,pm.login_account,pm.createtime as reg_time,bm.avatar,bm.mobile,bm.name,SUM(sc.integral) as integral_sum,count(sc.order_id) as order_count,IFNULL(SUM(bo.order_total),0) as order_sum
                FROM `vmc_o2ocds_relation` vor
                LEFT JOIN `vmc_pam_members` pm ON vor.member_id = pm.member_id
                LEFT JOIN `vmc_b2c_members` bm ON vor.member_id = bm.member_id
                LEFT JOIN `vmc_o2ocds_service_code` sc ON vor.member_id = sc.member_id
                LEFT JOIN `vmc_b2c_orders` bo ON sc.order_id = bo.order_id
                WHERE vor.relation_id = {$relation_id} AND bo.pay_status IN('1','2')  AND vor.type = 'store' AND pm.login_type = 'wechat' $where_str GROUP BY vor.member_id ";
        $NOT_SQL = "
        SELECT vor.time as bind_time,vor.member_id,vor.relation,vor.relation_id as store_id,pm.login_account,pm.createtime as reg_time,bm.avatar,bm.mobile,bm.name,SUM(sc.integral) as integral_sum,count(sc.order_id) as order_count,IFNULL(SUM(bo.order_total),0) as order_sum
                FROM `vmc_o2ocds_relation` vor
                LEFT JOIN `vmc_pam_members` pm ON vor.member_id = pm.member_id
                LEFT JOIN `vmc_b2c_members` bm ON vor.member_id = bm.member_id
                LEFT JOIN `vmc_o2ocds_service_code` sc ON vor.member_id = sc.member_id
                LEFT JOIN `vmc_b2c_orders` bo ON sc.order_id = bo.order_id
                WHERE vor.relation_id = {$relation_id}  AND vor.type = 'store' AND pm.login_type = 'wechat' $where_str GROUP BY vor.member_id
        ";
        $SQL  = "SELECT * FROM ({$IN_SQL} UNION ALL {$NOT_SQL}) tmp_alias GROUP BY member_id";
        if($orderType) {
            $SQL .= " ORDER BY {$orderType} DESC ";
        }else{
            $SQL .= " ORDER BY order_count DESC ";
        }
        if($limit > 0) {
            $SQL .= " LIMIT {$offset},{$limit}";
        }
        if(!$relation_list = $this->db->select($SQL)) {
            return false;
        };
        return $relation_list;
    }

    public function count_order($relation_id) {
        if(!$relation_id) {
            return false;
        }
        $SQL = "SELECT count(order_id) as count
                FROM `vmc_o2ocds_relation` vor
                LEFT JOIN `vmc_o2ocds_service_code` sc ON vor.member_id = sc.member_id
                WHERE vor.relation_id = {$relation_id}
        ";
        return $this->db->count($SQL);
    }

    /**
     * @params null
     * @return string 店铺编号
     */
    public function apply_sno()
    {
        $tb = $this->table_name(1);
        do{
            $new_sno = rand(100000,999999);
            $row = $this->db->selectrow('SELECT sno from '.$tb.' where sno ='.$new_sno);
        }while($row);

        return $new_sno;
    }

    public function modifier_enterprise_id($col) {
        if($enterprise = $this->app->model('enterprise')->getRow('name',array('enterprise_id'=>$col))) {
            return $enterprise['name'];
        };
        return '系统';
    }


    /**
     * 删除前写入管理员操作日志
     * @param array post
     * @return boolean
     */
    public function pre_recycle($rows)
    {
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'store_log')) {
            $obj_log->store_log($rows,'delete');
        }
        return true;
    }

    /**
     * 企业店铺之后做的事情
     * @param array post
     * @return boolean
     */
    public function suf_recycle($filter=array())
    {
        $is_delete = true;
        if($filter['store_id']) {
            $mdl_relation = $this->app->model('relation');
            $delete_filter = array('type'=>'store','relation_id'=>$filter['store_id']);
            $is_delete = $mdl_relation->delete($delete_filter);
        }
        return $is_delete;
    }


}
