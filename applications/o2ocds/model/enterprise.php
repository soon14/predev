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


class o2ocds_mdl_enterprise extends dbeav_model
{

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function check(&$enterprise,&$msg) {
        if(!$enterprise['area']) {
            $msg = '地区未填写';
            return false;
        }
        if(!$enterprise['enterprise_id']) {
            $eno = vmc::singleton('o2ocds_area')->get_initial($enterprise['area'],2);
            $eno_num = $this->count(array('eno|has'=>$eno));
            $eno_num += 1;
            if($eno_num<10) {
                $eno_num = '0'.$eno_num;
            }
            $enterprise['eno'] = $eno.$eno_num;
            $enterprise['apply_time'] = time();
        }
        if($enterprise['member_id']) {
            if($this->app->model('relation')->getRow('*',array('member_id'=>$enterprise['member_id'],'relation|notin'=>'admin'))) {
                $msg = '已有其他身份';
                return false;
            }
        }
        if(!$enterprise['name']) {
            $msg = '企业名称不能为空';
            return false;
        }
        if($this->getRow('enterprise_id',array('name'=>$enterprise['name'],'enterprise_id|notin'=>$enterprise['enterprise_id']))) {
            $msg = '企业名称重复';
            return false;
        };
        if($this->app->model('relation')->getRow('member_id',array('relation_id|notin'=>$enterprise['enterprise_id'],'type'=>'admin'))) {
            $msg = '该会员已经绑定过企业';
            return false;
        };
        return true;
    }

    /*
     * 查询关联店铺信息
     * */
    public function relevance_store($filter = array(),$offset = 0, $limit = -1,$achieve = false)
    {
        $mdl_qrcode = $this->app->model('qrcode');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        if($filter['enterprise']) {
            $where_str = $dbeav_filter->dbeav_filter_parser(array('enterprise_id'=>$filter['enterprise']['enterprise_id']), 'os', false, app::get('o2ocds')->model('store'));
        }else{
            return false;
        }
        if($filter['invitation']) {
            $where_str .= substr($dbeav_filter->dbeav_filter_parser($filter['invitation'], 'oi', false, app::get('o2ocds')->model('invitation')),'1');
        }
        if($createtime = $filter['order']['createtime']) {
            $in_where_str = " AND bo.createtime >= {$createtime['from']} AND bo.createtime < {$createtime['to']}";
            $not_where_str = " AND ((bo.createtime < {$createtime['from']} AND bo.createtime >= {$createtime['to']}) OR bo.order_id IS NULL)";
        }
        //满足条件
        $IN_SQL = "SELECT count(sc.order_id) as order_count,SUM(bo.order_total) AS order_sum,pm.`login_account` as salesman_account ,os.*
                FROM  `vmc_o2ocds_store` os
                LEFT JOIN `vmc_o2ocds_service_code` sc ON sc.store_id = os.store_id
                LEFT JOIN `vmc_b2c_orders` bo ON sc.order_id = bo.order_id
                LEFT JOIN `vmc_o2ocds_invitation` oi ON oi.store_id = os.store_id
                LEFT JOIN `vmc_pam_members` pm ON pm.member_id = oi.member_id
                WHERE {$where_str}{$in_where_str}  AND bo.pay_status IN('1','2') AND pm.login_type = 'wechat'  AND os.store_id  IS NOT NULL GROUP BY os.store_id ";
        //不满足条件
        $NOT_SQL = "SELECT 0 AS order_count,0 AS order_sum,pm.`login_account` as salesman_account ,os.*
                FROM  `vmc_o2ocds_store` os
                LEFT JOIN `vmc_o2ocds_service_code` sc ON sc.store_id = os.store_id
                LEFT JOIN `vmc_b2c_orders` bo ON sc.order_id = bo.order_id
                LEFT JOIN `vmc_o2ocds_invitation` oi ON oi.store_id = os.store_id
                LEFT JOIN `vmc_pam_members` pm ON pm.member_id = oi.member_id
                WHERE {$where_str}{$not_where_str}  AND (bo.pay_status NOT IN('1','2') or bo.order_id IS NULL) AND pm.login_type = 'wechat'  AND os.store_id IS NOT NULL GROUP BY os.store_id ";
        //union 进行排序分组
        $SQL  = "SELECT * FROM ({$IN_SQL} UNION ALL {$NOT_SQL}) tmp_alias GROUP BY store_id ORDER BY order_sum DESC";
        if($limit > 0) {
            $SQL .= " LIMIT {$offset},{$limit}";
        }
        if(!$store_list = $mdl_qrcode->db->select($SQL)) {
            return false;
        };
        //统计店铺利润
        if($achieve && $store_list) {
            $mdl_store = $this->app->model('store');
            $store_ids = array_keys(utils::array_change_key($store_list,'store_id'));

            $achieve_where_str = $dbeav_filter->dbeav_filter_parser(array('relation_id'=>$store_ids,'type'=>'store'), false, false, app::get('o2ocds')->model('orderlog_achieve'));
            $achieve_sql = "SELECT sum(achieve_fund) as total,relation_id FROM vmc_o2ocds_orderlog_achieve   WHERE $achieve_where_str";
            if($achieve_count = $mdl_store->db->select($achieve_sql)) {
                $achieve_count = utils::array_change_key($achieve_count,'relation_id');
            };
            foreach($store_list as &$store) {
                $store = array_merge($store,(array)$achieve_count[$store['store_id']]);
            }
        }
        return $store_list;
    }

    public function count_store($filter) {
        $mdl_qrcode = $this->app->model('qrcode');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        if($filter['enterprise']) {
            $where_str = $dbeav_filter->dbeav_filter_parser(array('enterprise_id'=>$filter['enterprise']['enterprise_id']), 'os', false, app::get('o2ocds')->model('store'));
        }else{
            return false;
        }
        if($filter['invitation']) {
            $where_str .= substr($dbeav_filter->dbeav_filter_parser($filter['invitation'], 'oi', false, app::get('o2ocds')->model('invitation')),'1');
        }
        //满足条件
        $SQL = "SELECT count(*) as count
                FROM  `vmc_o2ocds_store` os
                LEFT JOIN `vmc_o2ocds_invitation` oi ON oi.store_id = os.store_id
                WHERE {$where_str} ";
       return $mdl_qrcode->db->count($SQL);
    }

    /*
     * 查询关联业务员
     * */
    public function relation_sales($relation_id,$filter = array(),$offset = 0, $limit = -1,$orderType = null)
    {
        if (!$relation_id) {
            return false;
        }

        //统计业务员邀请店数
        if($filter) {
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $where_str = substr($dbeav_filter->dbeav_filter_parser($filter, 'oi', false, app::get('o2ocds')->model('invitation')),'1');
        }
        $IN_SQL = "SELECT vor.relation_id as enterprise_id,vor.member_id,vor.time as bind_time,vor.relation,bm.avatar,bm.name,bm.mobile,pm.login_account,pm.createtime as reg_time,count(oi.store_id) as invitation_count
                FROM `vmc_o2ocds_relation` vor
                LEFT JOIN  `vmc_b2c_members` bm ON bm.member_id = vor.member_id
                LEFT JOIN  `vmc_pam_members` pm ON pm.member_id = vor.member_id
                LEFT JOIN  `vmc_o2ocds_invitation` oi ON oi.member_id = vor.member_id
                WHERE  vor.relation_id = {$relation_id} AND vor.type='enterprise' AND pm.login_type = 'wechat' {$where_str} AND oi.store_id>0  GROUP BY vor.member_id ";
        $NOT_SQL = "
        SELECT vor.relation_id as enterprise_id,vor.member_id,vor.time as bind_time,vor.relation,bm.avatar,bm.name,bm.mobile,pm.login_account,pm.createtime as reg_time,count(oi.store_id) as invitation_count
                FROM `vmc_o2ocds_relation` vor
                LEFT JOIN  `vmc_b2c_members` bm ON bm.member_id = vor.member_id
                LEFT JOIN  `vmc_pam_members` pm ON pm.member_id = vor.member_id
                LEFT JOIN  `vmc_o2ocds_invitation` oi ON oi.member_id = vor.member_id
                WHERE  vor.relation_id = {$relation_id} AND vor.type='enterprise' AND pm.login_type = 'wechat' {$where_str} AND (oi.store_id IS NULL OR oi.store_id = 0)  GROUP BY vor.member_id";
        $SQL  = "SELECT * FROM ({$IN_SQL} UNION ALL {$NOT_SQL}) tmp_alias GROUP BY member_id";
        if($orderType) {
            $SQL .= " ORDER BY {$orderType} DESC ";
        }else{
            $SQL .= " ORDER BY invitation_count DESC ";
        }
        if($limit > 0) {
            $SQL .= " LIMIT {$offset},{$limit}";
        }

        if(!$relation_list = $this->db->select($SQL)) {
            return false;
        };
        return $relation_list;
    }


    public function modifier_member_id($col) {
        if($members = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$col))) {
            return $members['login_account'];
        };
        return '';
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
        if(method_exists($obj_log,'enterprise_log')) {
            $obj_log->enterprise_log($rows,'delete');
        }
        return true;
    }

    /**
     * 企业删除之后做的事情
     * @param array post
     * @return boolean
     */
    public function suf_recycle($filter=array())
    {
        $is_delete = true;
        if($filter['enterprise_id']) {
            $mdl_relation = $this->app->model('relation');
            $delete_filter = array('type'=>'enterprise','relation_id'=>$filter['enterprise_id']);
            $is_delete = $mdl_relation->delete($delete_filter);
        }
        return $is_delete;
    }



}
