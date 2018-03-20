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


class vshop_mdl_shop extends dbeav_model
{
    var $has_tag = true;
    public $defaultOrder = array(
        'shop_id DESC',
    );

    public function __construct($app)
    {
        parent::__construct($app);

    }

    /**
     * 生成店铺唯一编号,生成规则格式为店铺ID最大值+1
     * @params null
     * @return string 店铺ID
     */
    public function apply_id()
    {
        $tb = $this->table_name(1);
        do{
            $i = substr(mt_rand() , -3);
            $new_shop_id = (date('y')+date('m')+date('d')).date('His').$i;
            $row = $this->db->selectrow('SELECT shop_id from '.$tb.' where shop_id ='.$new_shop_id);
        }while($row);

        return $new_shop_id;
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

    public function save(&$sdf, $mustUpdate = null, $mustInsert = false)
    {
        $sdf['consignor']['area'] = htmlspecialchars($sdf['consignor']['region']);
        $sdf['consignor']['addr'] = htmlspecialchars($sdf['consignor']['address']);
        $flag = parent::save($sdf, $mustUpdate = null, $mustInsert = false);

        return $flag;
    }
    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        foreach (vmc::servicelist('b2c_mdl_members.filter') as $k => $obj_filter) {
            if (method_exists($obj_filter, 'extend_filter')) {
                $obj_filter->extend_filter($filter);
            }
        }

        if ($filter['login_account']) {
            $aData = app::get('pam')->model('members')->getList('member_id', array('login_account|head' => $filter['login_account']));
            unset($filter['login_account']);

            if ($aData) {
                foreach ($aData as $key => $val) {
                    $member[$key] = $val['member_id'];
                }
                $filter['member_id'] = $member;
            } else {
                return 0;
            }
        }
        $filter = parent::_filter($filter);

        return $filter;
    }

    /**
     * 重写搜索的下拉选项方法.
     *
     * @param null
     */
    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                if ($k == 'member_id') {
                    $columns['member_key'] = $v['label'];
                } else {
                    $columns[$k] = $v['label'];
                }
            }
        }
        /** 添加店铺信息搜索 **/
        $columns = array_merge($columns, array(
            'login_account' => ('登录账号'),
        ));
        /** end **/
        return $columns;
    }
}
