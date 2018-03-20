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


class content_mdl_article_bodys extends dbeav_model
{
    /**
     * 格式化参数.
     *
     * @param array $params 录入参数
     *
     * @return array 格式化后参数
     */
    public function format_params($params)
    {
        if (isset($params['seo_title'])) {
            $params['seo_title'] = htmlspecialchars($params['seo_title'], ENT_QUOTES);
        }
        if (isset($params['seo_keywords'])) {
            $params['seo_keywords'] = htmlspecialchars($params['seo_keywords'], ENT_QUOTES);
        }
        if (isset($params['seo_description'])) {
            $params['seo_description'] = htmlspecialchars($params['seo_description'], ENT_QUOTES);
        }
        if (isset($params['content'])) {
            $params['content'] = $params['content'];
        }
        $params['length'] = strlen($params['content']);

        return $params;
    } //End Function

    /**
     * 检查插入时参数.
     *
     * @param array $params 插入时参数
     *
     * @return bool|array 返回检查结果
     */
    public function valid_insert($params)
    {
        if (empty($params['article_id'])) {
            trigger_error('文章ID不能为空', E_USER_ERROR);

            return false;
        }
        $params = $this->format_params($params);

        return $params;
    } //End Function

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
    } //End Function

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
        $insert_id = parent::insert($params);
        if ($insert_id) {
            $rows = $this->getList('article_id', array(
                'id' => $insert_id,
            ));
            vmc::singleton('content_article_detail')->delete_body_kvstore($rows[0]['article_id']);
            vmc::singleton('content_article_detail')->store_detail_change();

            return $insert_id;
        } else {
            return false;
        }
    } //End Function

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
        $rows = $this->getList('article_id', $filter);
        if (parent::update($params, $filter)) {
            if($rows){
                foreach ($rows as $row) {
                    vmc::singleton('content_article_detail')->delete_body_kvstore($row['article_id']);
                }
            }
            vmc::singleton('content_article_detail')->store_detail_change();

            return true;
        } else {
            return false;
        }
    } //End Function

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
                vmc::singleton('content_article_detail')->delete_body_kvstore($row['article_id']);
            }
            vmc::singleton('content_article_detail')->store_detail_change();

            return true;
        } else {
            return false;
        }
    } //End Function
} //End Class
