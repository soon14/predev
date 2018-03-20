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
 * 店铺订单model
 * 只取vmc_b2c_orders表里是店铺订单的数据
 *
 * Class store_mdl_storeorder
 */
class store_mdl_storeorder extends dbeav_model
{
    public $has_tag = true;
    public $defaultOrder = array('createtime', 'DESC');

    /**
     * 订单表model
     *
     * @var
     */
    private $orders_mdl;
    private $relation_orders_mdl;

    public function __construct($app)
    {
        $this->orders_mdl = app::get('b2c')->model('orders');
        $this->relation_orders_mdl = app::get('store')->model('relation_orders');
        parent::__construct($app);
        $this->use_meta = true;
    }

    /**
     * 右边搜索配置扩展
     *
     * @return array
     */
    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                $columns[$k] = $v['label'];
            }
        }
        return $columns;
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
        $class_name = get_class($this);
        $table_name = substr($class_name, 5 + strpos($class_name, '_mdl_'));
        if ($real) {
            return vmc::database()->prefix . 'b2c' . '_orders' ;
        } else {
            return $table_name;
        }
    }
    /**
     * 查询结果
     *
     * @param string $cols 查询字段
     * @param array $filter 查询条件
     * @param int $offset 从哪一条开始查询
     * @param int $limit 每次查询多少条
     * @param null $orderType 排序
     *
     * @return mixed
     */
    public function getList($cols = '*', $filter = [], $offset = 0, $limit = -1, $orderType = null)
    {
        $rows = parent::getList("*", $filter, $offset, $limit, $orderType);
        //为订单添加店铺信息
        if($rows){
            $rows = utils::array_change_key($rows ,'order_id');
            $goods_id = array_keys($rows);
            $relation_rows = $this ->relation_orders_mdl->getList('*' ,array('order_id'=>$goods_id));
            $relation_rows = utils::array_change_key($relation_rows ,'order_id');
            foreach($rows as $k=>$v){
                $rows[$k] = array_merge($v ,$relation_rows[$v['order_id']]);
            }
        }
        $this->tidy_data($rows);
        return $rows;
    }

    /**
     * 生成查询条件语句
     *
     * @param $filter
     * @param null $tableAlias
     * @param null $baseWhere
     *
     * @return string
     * @throws Exception
     */
    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        if(vmc::singleton('desktop_user') ->is_super()){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") ."order_id IN(SELECT order_id FROM vmc_store_relation_orders)";
        }else{
            $user_id = vmc::singleton('desktop_user')->get_id();
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") ."order_id IN(SELECT order_id FROM vmc_store_relation_orders WHERE store_id IN (SELECT store_id FROM vmc_store_relation_desktopuser WHERE user_id =$user_id))";
        }
        if($filter['store_id']>0){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") . "order_id IN(SELECT order_id FROM vmc_store_relation_orders WHERE store_id =$filter[store_id])";
        }
        unset($filter['store_id']);
        return parent::_filter($filter) ;
    }

    /**
     * 获取店铺订单字段信息
     *
     * @return mixed
     */
    public function get_schema()
    {
        $schema = $this->orders_mdl->schema;
        foreach($schema['columns'] as $columnName => $columnInfo){
            if(strpos($columnInfo['type'],'table:' ) !==false){
                $columnInfo['type'] .="@b2c";
            }
            $schema['columns'][$columnName] = $columnInfo;
        }

        $relation_orders = $this->relation_orders_mdl->schema;
        $schema['columns'] = array_merge($relation_orders['columns'] ,$schema['columns']);
        $schema['in_list'] = array_merge($relation_orders['in_list'] ,$schema['in_list']);
        return $schema;

    }

    public function modifier_pay_app($col)
    {
        $mdl_papp = app::get('ectools')->model('payment_applications');
        $papp = $mdl_papp->dump($col);
        return $papp['name'] ? $papp['name'] : $col;
    }

    public function modifier_member_id($col)
    {
        if ($col === 0 || $col == '0') {
            return ('非会员顾客');
        } else {
            return vmc::singleton('b2c_user_object')->get_member_name(null, $col);
        }
    }

    public function modifier_op_id($col)
    {
        if (!$col) {
            return ('电商');
        } else {
            $user = app::get('desktop')->model('users')->getRow('*', array('user_id' => $col));
            return $user['name'];
        }
    }
}//End Class
