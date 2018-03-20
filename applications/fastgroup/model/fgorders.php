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


class fastgroup_mdl_fgorders extends dbeav_model
{
    var $has_tag = true;
    var $defaultOrder = array('createtime','DESC');
    public function __construct($app)
    {
        parent::__construct($app);

    }
    //根据活动id 获得订购量
    public function get_quantity_bysubject($subject_id){
        $SQL = "SELECT sum(oi.nums) as quantity FROM vmc_b2c_order_items as oi RIGHT JOIN vmc_fastgroup_fgorders as ff ON oi.order_id=ff.order_id WHERE ff.subject_id=$subject_id AND ff.order_status IN ('active','finish')";
        return $this->db->selectrow($SQL);
    }

    //根据手机 获得订购量
    public function get_quantity_bymobile($subject_id,$mobile){
        $SQL = "SELECT sum(oi.nums) as quantity FROM vmc_b2c_order_items as oi RIGHT JOIN vmc_fastgroup_fgorders as ff ON oi.order_id=ff.order_id WHERE ff.subject_id=$subject_id AND mobile=$mobile AND ff.order_status IN ('active','finish')";
        return $this->db->selectrow($SQL);
    }

    //生成秘钥
    public function gen_skey($seed){

        $tb = $this->table_name(1);
        do{
            $skey = intval(($seed - time())/mt_rand(15,55));
            $row = $this->db->selectrow('SELECT skey from '.$tb.' where skey ='.$skey);
        }while($row);
        return $skey;
    }

    /**
     * 删除前
     */
    public function pre_recycle($rows)
    {
        $this->recycle_msg = '删除成功!';
        //todo 判断
        return true;
    }
}
