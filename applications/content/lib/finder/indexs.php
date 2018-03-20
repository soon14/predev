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
 * finder 显示类.
 */
class content_finder_indexs
{
    /**
     * 构造方法 实例化APP类.
     *
     * @param object $app
     */
    public function __construct(&$app)
    {
        $this->app = $app;
    }//End Function

    /**
     * 显示在finder列表上的标题.
     *
     * @var string
     */
    public $column_edit = '操作';

    /**
     * 列表编辑的显示数据.
     *
     * @param array $row finder上的一条记录
     *
     * @return string
     */
    public function column_edit($row)
    {
        if (vmc::singleton('desktop_user')->has_permission('cms_article_edit')) {
            $_return =  '<a class="btn btn-xs btn-default" href="index.php?app=content&ctl=admin_article&act=edit&article_id='.$row['article_id'].'"  ><i  class="fa fa-edit"></i>'.'编辑'.'</a>';
        }

        if (vmc::singleton('desktop_user')->has_permission('cms_article_view')) {
            $view = '<a class="btn btn-xs btn-default" href="'.app::get('site')->router()->gen_url(array('app' => 'content', 'ctl' => 'site_article', 'act' => 'index', 'arg0' => $row['article_id'])).'" target="_blank" ><i class="fa fa-eye"></i>'.'预览'.'</a>';
        }

        return $_return.$view;
    }

}//End Class
