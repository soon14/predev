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


class image_finder_image
{
    public $detail_basic = '图片详细信息';
    public $column_img = '图片';
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * finder下拉详细展示页面.
     *
     * @param string image id
     *
     * @return string 详细页面的html
     */
    public function detail_basic($image_id)
    {
        $app = app::get('image');

        $render = $app->render();

        $image = $this->app->model('image');
        $image_info = $image->dump($image_id);
        $allsize = app::get('image')->getConf('image.default.set');

        $render->pagedata['allsize'] = $allsize;
        $render->pagedata['image'] = $image_info;

        return $render->fetch('finder/image.html');
    }

    /**
     * finder img列的链接修改.
     *
     * @param array 某行具体数据的数组
     *
     * @return string 链接html
     */
    public function column_img($row)
    {
        $row = $row['@row'];
        if ($row['storage'] == 'network') {
            return '<a class="btn btn-xs btn-default" href="'.$row['ident'].'" target="_blank">网络图片</a>';
        }

        return '<a href="'.base_storager::image_path($row['image_id']).'" target="_blank">
<img class="img-thumbnail" src="'.(base_storager::image_path($row['image_id'], 'xs')).'" style="height:50px" /></a>';
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
