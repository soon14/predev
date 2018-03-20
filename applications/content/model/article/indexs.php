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

class content_mdl_article_indexs extends dbeav_model
{
    /*
    * @var bool 启用标签
    */
    public $has_tag = true;
    /*
    * @var array 发布时间排序
    */
    public $defaultOrder = array('ordernum ASC,pubtime DESC');
    /*
    * @var array 关联关系
    */
    public $has_many = array(
        //'tag'=>'tag_rel@desktop:replace:article_id^rel_id',
    );

    /**
     * 列表项搜索字段.
     *
     * @return array
     */
    public function searchOptions()
    {
        $arr = parent::searchOptions();

        return array_merge($arr, array(
                'title' => '标题',
            ));
    }//End Function
    /**
    * 记录数.
    */
    public function count($filter = null)
    {
        if ($filter['node_id'] > 0) {
            $filter['node_id'] = app::get('content')->model('article_nodes')->get_childrens_id($filter['node_id']);
        }

        return parent::count($filter);
    }//End Function
    /**
    * 列表数据.
    */
    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null)
    {
        if ($filter['node_id'] > 0) {
            $filter['node_id'] = app::get('content')->model('article_nodes')->get_childrens_id($filter['node_id']);
        }
        if (!$orderType) {
            $orderType = $this->defaultOrder;
        }
        return parent::getList($cols, $filter, $offset, $limit, $orderType);
    }//End Function

    /**
     * 重写getList.
     */
    public function getList_1($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null)
    {
        return parent::getList($cols, $filter, $offset, $limit, $orderType);
    }//End Function

    /**
     * 格式化参数.
     *
     * @param array $params 录入参数
     *
     * @return array 格式化后参数
     */
    public function format_params($params)
    {
        if (isset($params['type'])) {
            $params['type'] = in_array($params['type'], array(1, 2, 3)) ? $params['type'] : 1;
        }
        if (isset($params['title'])) {
            $params['title'] = htmlspecialchars($params['title'], ENT_QUOTES);
        }
        if (isset($params['author'])) {
            $params['author'] = htmlspecialchars($params['author'], ENT_QUOTES);
        }
        if (isset($params['ifpub'])) {
            $params['ifpub'] = ($params['ifpub']) ? 'true' : 'false';
        }

        return $params;
    }//End Function

    /**
     * 检查插入时参数.
     *
     * @param array $params 插入时参数
     *
     * @return bool|array 返回检查结果
     */
    public function valid_insert($params)
    {
        if (empty($params['title'])) {
            trigger_error('文章名称不能为空', E_USER_ERROR);

            return false;
        }
        if (empty($params['node_id'])) {
            trigger_error('所属节点不能为空', E_USER_ERROR);

            return false;
        }
        $params = $this->format_params($params);

        return $params;
    }//End Function

    /**
     * 检查更新时参数.
     *
     * @param array $params 更新时参数
     *
     * @return array 返回检查结果
     */
    public function valid_update($params)
    {
        $params = $this->format_params($params);

        return $params;
    }//End Function

    /**
     * 插入数据.
     *
     * @param array $params 插入的数据
     *
     * @return bool|int 返回插入结果
     */
    public function insert(&$params)
    {
        $params = $this->valid_insert($params);
        if (!$params) {
            return false;
        }
        $params['uptime'] = time();
        if (empty($params['pubtime'])) {
            $params['pubtime'] = $params['uptime'];
        }
        $insert_id = parent::insert($params);
        if ($insert_id) {
            vmc::singleton('content_article_detail')->delete_index_kvstore($insert_id);
            vmc::singleton('content_article_detail')->store_detail_change();

            return $insert_id;
        } else {
            return false;
        }
    }//End Function

    /**
     * 更新数据.
     *
     * @param array $params 更新的数据
     * @param array $filter 更新的条件
     *
     * @return bool 返回更新结果
     */
    public function update($params, $filter = array(), $mustUpdate = null)
    {
        $params = $this->valid_update($params);
        if (!$params) {
            return false;
        }
        if (empty($params['pubtime'])) {
            unset($params['pubtime']);
        }
        $params['uptime'] = time();
        $rows = $this->getList('article_id', $filter);
        if (parent::update($params, $filter)) {
            foreach ($rows as $row) {
                vmc::singleton('content_article_detail')->delete_index_kvstore($row['article_id']);
            }
            vmc::singleton('content_article_detail')->store_detail_change();

            return true;
        } else {
            return false;
        }
    }//End Function

    /**
     * 删除数据.
     *
     * @param array $filter 符合删除的条件
     *
     * @return bool 返回删除结果
     */
    public function delete($filter, $subSdf = 'delete')
    {
        $rows = $this->getList('article_id', $filter);
        if (parent::delete($filter)) {
            foreach ($rows as $row) {
                vmc::singleton('content_article_detail')->delete_index_kvstore($row['article_id']);
                vmc::singleton('content_article_detail')->delete_body_kvstore($row['article_id']);
            }
            vmc::singleton('content_article_detail')->store_detail_change();

            return true;
        } else {
            return false;
        }
    }//End Function

    /**
     * 更新发布时间.
     *
     * @param array $filter 符合条件
     *
     * @return bool 返回插入结果
     */
    public function update_time($filter)
    {
        $params['uptime'] = time();

        return parent::update($params, $filter);
    }//End Function
}//End Class
