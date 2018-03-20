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


/**
 * 店铺库存model
 * 数据都来自与vmc_b2c_stock表
 *
 * Class store_mdl_storestock
 */
class store_mdl_storestock extends dbeav_model
{
    private $obj_desktop_user = null;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->obj_desktop_user = vmc::singleton('desktop_user');
    }

    /**
     * 修改库存finder列表里的库存数量字段
     *
     * @param $col
     * @param $stock_info
     *
     * @return string
     */
    public function modifier_quantity($col,$stock_info)
    {
        $_return = $col;

        //拥有直接在finder列表里修改库存权限的,才能直接修改
        if($this->obj_desktop_user->has_permission('store_finder_edit_stock')){
            $_return = "<input class='form-control edit-col input-sm input-xsmall' name='quantity' type='text' data-pkey='{$stock_info['stock_id']}' value='{$col}'>";
        }

        return $_return;
    }


    public function modifier_freez_quantity($col,$stock_info)
    {
        $_return = $col;

        //拥有直接在finder列表里修改库存权限的,才能直接修改
        if($this->obj_desktop_user->has_permission('store_finder_edit_stock')){
            $_return = "<input class='form-control edit-col input-sm input-xsmall' name='freez_quantity' type='text' data-pkey='{$stock_info['stock_id']}' value='{$col}'>";
        }

        return $_return;
    }

    /**
     * 获取数据表名
     *
     * @param bool|false $real
     *
     * @return string
     */
    public function table_name($real = false)
    {
        return 'vmc_b2c_stock';
    }

    /**
     * 生成查询条件语句
     *
     * @param $filter
     * @param null $tableAlias
     * @param null $baseWhere
     *
     * @return string
     */
    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        //店铺库存增加店铺相关条件
        $this->_filter_store($filter);
        return parent::_filter($filter);
    }

    /**
     * 定义虚拟表字段
     *
     * @return array
     */
    public function get_schema()
    {
        return app::get('b2c')->model('stock')->schema;
    }

    /**
     * 店铺库存增加店铺相关条件
     *
     * @param array $filter
     *
     * @return string
     */
    private function _filter_store(&$filter){

        if(vmc::singleton('desktop_user') ->is_super()){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") . "sku_bn in  (SELECT bn FROM vmc_b2c_products WHERE goods_id IN(SELECT goods_id FROM vmc_store_relation_goods))";
        }else{
            $user_id = vmc::singleton('desktop_user')->get_id();
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") . "sku_bn in  (SELECT bn FROM vmc_b2c_products WHERE goods_id IN(SELECT goods_id FROM vmc_store_relation_goods WHERE store_id IN (SELECT store_id FROM vmc_store_relation_desktopuser WHERE user_id =$user_id)))";
        }

        if($filter['store_id'] && $filter['store_id']>0){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") . "sku_bn in  (SELECT bn FROM vmc_b2c_products WHERE goods_id IN(SELECT goods_id FROM vmc_store_relation_goods WHERE store_id =$filter[store_id]))";
        }
        unset($filter['store_id']);

    }
}//End Class
