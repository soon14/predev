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


class restrict_mdl_restrict extends dbeav_model
{
    var $defaultOrder = array('createtime','DESC');

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function pre_recycle($data = array())
    {
        if (is_array($data)) {
            $mdl_restrict_products = $this->app->model('products');
            $mdl_restrict_goods = $this->app->model('goods');
            $mdl_member_lv = $this->app->model('member_lv');
            foreach ($data as $key => $value) {
                if (!$value['res_id']) {
                    continue;
                }
                if(!$mdl_restrict_products->delete(array('res_id'=>$value['res_id']))) {
                    return false;
                };
                if(!$mdl_restrict_goods->delete(array('res_id'=>$value['res_id']))) {
                    return false;
                };
                if(!$mdl_member_lv->delete(array('res_id'=>$value['res_id']))) {
                    return false;
                };
            }
        }
        return true;
    }

    /*
     * 按照货品限购查询规则
     * $param 货品ID
     * $param 会员ID
     * */
    public function product_restrict($product_ids,$member_id) {
        $time = time();
        if(!$product_ids) {
            return false;
        }
        if(is_array($product_ids)) {
            $where_product = ' rp.product_id IN('.implode(",",$product_ids).')';
        }else{
            $where_product = ' rp.product_id = '.$product_ids;
        }
        if($member_id) {
            $member_lv_id = app::get('b2c')->model('members')->getRow('member_lv_id',array('member_id'=>$member_id))['member_lv_id'];
        }
        if(!$member_lv_id) {
            $sql = "SELECT * from (SELECT rp.*,rr.res_orderby FROM `vmc_restrict_restrict` rr INNER JOIN `vmc_restrict_products` rp ON rr.res_id = rp.res_id
                  WHERE rr.status='1' AND from_time <= $time AND to_time >= $time AND $where_product  ORDER BY rr.res_orderby DESC)tmp  group by product_id ";
            $res = $this->db->select($sql);
            if(is_string($product_ids)) {
                $res = $res[0];
            }
        }else{
            $sql = "SELECT * from (SELECT rp.*,rr.res_orderby FROM `vmc_restrict_restrict` rr INNER JOIN `vmc_restrict_products` rp ON rr.res_id = rp.res_id LEFT JOIN `vmc_restrict_member_lv` ml ON rr.res_id = ml.res_id
                  WHERE rr.status='1' AND from_time <= $time AND to_time >= $time AND $where_product AND ml.member_lv_id IN($member_lv_id)  ORDER BY rr.res_orderby DESC)tmp  group by product_id ";
            $res = $this->db->select($sql);
            if(is_string($product_ids)) {
                $res = $res[0];
            }
        }
        return $res;
    }

    /*
     * 按照货品限购查询规则
     * $param 商品ID
     * $param 会员ID
     * */
    public function goods_restrict($goods_ids,$member_id) {
        $time = time();
        if(!$goods_ids) {
            return false;
        }
        if(is_array($goods_ids)) {
            $where_goods = ' rg.goods_id IN('.implode(",",$goods_ids).')';
        }else{
            $where_goods = ' rg.goods_id = '.$goods_ids;
        }
        if($member_id) {
            $member_lv_id = app::get('b2c')->model('members')->getRow('member_lv_id',array('member_id'=>$member_id))['member_lv_id'];
        }
        if(!$member_lv_id) {
            $sql = "SELECT *  from (SELECT rg.*,rr.res_orderby FROM `vmc_restrict_restrict` rr INNER JOIN `vmc_restrict_goods` rg ON rr.res_id = rg.res_id
                  WHERE rr.status='1' AND from_time <= $time AND to_time >= $time AND $where_goods ORDER BY rr.res_orderby DESC) tmp  group by goods_id";
            $res = $this->db->select($sql);
            if(is_string($goods_ids)) {
                $res = $res[0];
            }
        }else{
            $sql = "SELECT *  from (SELECT rg.*,rr.res_orderby FROM `vmc_restrict_restrict` rr INNER JOIN `vmc_restrict_goods` rg ON rr.res_id = rg.res_id LEFT JOIN `vmc_restrict_member_lv` ml ON rr.res_id = ml.res_id
                  WHERE rr.status='1' AND from_time <= $time AND to_time >= $time AND $where_goods AND ml.member_lv_id IN($member_lv_id) ORDER BY rr.res_orderby DESC) tmp  group by goods_id";
            $res = $this->db->select($sql);
            if(is_string($goods_ids)) {
                $res = $res[0];
            }
        }
        return $res;
    }

}
