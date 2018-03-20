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
class sale_ctl_site_member extends b2c_ctl_site_member
{

    public function __construct(&$app)
    {
        parent::__construct(app::get('b2c'));
    }

    public function index()
    {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        $sql = "select a.createtime,b.image_id,b.name,b.price,b.product_id,c.reserve_start,c.reserve_end,c.alert,c.start,c.end,c.name as sale_name,c.status from vmc_sale_reserve as a join (select * from vmc_b2c_products group by goods_id) as b on a.goods_id = b.goods_id join vmc_sale_sales as c on a.sale_id = c.id where a.member_id=".$member_id;
        $db = vmc::database();
        $this->pagedata['now'] = time();
        $this->pagedata['reserve'] = $db->select($sql);
        $this->output('sale');
    }


}
