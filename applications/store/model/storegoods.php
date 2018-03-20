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
 * 店铺商品model
 *
 * Class store_mdl_storegoods
 */
class store_mdl_storegoods extends dbeav_model
{
    /**
     * 店铺商品关联信息
     *
     * @var array
     */

    public function __construct($app)
    {
        $this->goods_mdl = app::get('b2c')->model('goods');
        $this->relation_goods_mdl = app::get('store')->model('relation_goods');
        parent::__construct($app);
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

        $ext_columns = array(
            'bn|has' => ('货号'),
            'keyword' => ('商品关键字'),
            'barcode|has' => ('条码'),
            'store_name' => '店铺名称',
        );

        return array_merge($columns, $ext_columns);
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
            return vmc::database()->prefix . 'b2c' . '_goods' ;
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
        //查询店铺商品信息
        $rows = parent::getList("*", $filter, $offset, $limit, $orderType);
        //为商品添加店铺信息
        if($rows){
            $rows = utils::array_change_key($rows ,'goods_id');
            $goods_id = array_keys($rows);
            $relation_rows = $this ->relation_goods_mdl->getList('*' ,array('goods_id'=>$goods_id));
            foreach($relation_rows as $k=>$v){
                $relation_rows[$k] = array_merge($v ,$rows[$v['goods_id']]);
            }
            $rows = $relation_rows;
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
     */
    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        //店铺商品增加店铺筛选条件
        $this->get_store_filter($filter);
        return parent::_filter($filter) ;
    }

    /**
     * 定义虚拟表字段
     *
     * @return array
     */
    public function get_schema()
    {
        $schema = $this->goods_mdl->schema;
        foreach($schema['columns'] as $columnName => $columnInfo){
            if(strpos($columnInfo['type'],'table:' ) !==false){
                $columnInfo['type'] .="@b2c";
            }
            $schema['columns'][$columnName] = $columnInfo;
        }

        $relation_goods_schema = $this->relation_goods_mdl->schema;
        $schema['columns'] = array_merge($relation_goods_schema['columns'] ,$schema['columns']);
        $schema['in_list'] = array_merge($relation_goods_schema['in_list'] ,$schema['in_list']);
        return $schema;
    }

    /**
     * 店铺的商品增加店铺查询条件
     *
     * @param $filter
     *
     * @return mixed
     */
    private function get_store_filter(&$filter){
        if(vmc::singleton('desktop_user') ->is_super()){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") ."goods_id IN(SELECT goods_id FROM vmc_store_relation_goods)";
        }else{
            $user_id = vmc::singleton('desktop_user')->get_id();
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") ."goods_id IN(SELECT goods_id FROM vmc_store_relation_goods WHERE store_id IN (SELECT store_id FROM vmc_store_relation_desktopuser WHERE user_id =$user_id))";
        }
        if($filter['store_id']>0){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") . "goods_id IN(SELECT goods_id FROM vmc_store_relation_goods WHERE store_id =$filter[store_id])";
        }
        if($filter['store_name']){
            $filter['filter_sql'] .= ($filter['filter_sql'] ?" AND ":"") . "goods_id IN(SELECT goods_id FROM vmc_store_relation_goods WHERE store_id IN (select store_id FROM vmc_store_store where store_name like '%$filter[store_name]%'))";
        }
        unset($filter['store_id']);
        unset($filter['store_name']);
    }

}//End Class
