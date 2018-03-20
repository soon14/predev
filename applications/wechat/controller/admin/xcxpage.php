<?php

/**
 * 微信小程序页面.
 */
class wechat_ctl_admin_xcxpage extends desktop_controller {
    /*
     * @param object $app
     */

    public function __construct($app) {
        parent::__construct($app);
        $this->pagedata['base_url'] = vmc::base_url(true);
        $this->pagedata['res_url'] = $this->app->res_url;
    }

//End Function

    public function index() {
        $actions[] = array(
            'label' => '添加新页面',
            'href' => 'index.php?app=wechat&ctl=admin_xcxpage&act=edit',
            'icon' => 'fa-plus',
        );
        $this->finder('wechat_mdl_xcxpage', array(
            'title' => '小程序页面管理',
            'actions' => $actions,
            'use_buildin_recycle' => $this->has_permission('wechat_xcxpage_delete'),
        ));
    }

    public function edit($page_id) {
        $_params = vmc::singleton('base_component_request')->get_params(true);

        $draft = true;
        ($_params && isset($_params['draft'])) && $draft = $_params['draft'] ? true : false;
        $this->pagedata['draft'] = $draft ? 'true' : 'false';

        $pageData = array();
        $page_id && $pageData = app::get('wechat')->model('xcxpage')->dump($page_id);

        vmc::singleton('wechat_xcxwidgets')->getEditData($pageData);
        // myfun::vard($pageData);
        $mdl_xcxpage = $this->app->model('xcxpage');
        $page_list = $mdl_xcxpage->getList('*');
        $this->pagedata['page_list'] = $page_list;
        $this->pagedata['data'] = $pageData;

        $this->pagedata['data_json'] = json_encode($this->pagedata['data']);
        $this->pagedata['gallery_remote'] = vmc::openapi_url('openapi.goods', 'gallery');
        $this->pagedata['category_remote'] = vmc::openapi_url('openapi.goods', 'catalog/parent_id');
        $this->pagedata['category_path_remote'] = vmc::openapi_url('openapi.goods', 'catalog_path/cat_id');

        // 预览地址
        $this->pagedata['preview_url'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'preview',
        ));


        // 上传地址
        $this->pagedata['upload_url'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'upimg',
        ));

        // 小程序页面
        $this->pagedata['xcxpages_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'xcxpages',
        ));

        // -----------------------
        // 获取产品列表
        $this->pagedata['product_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => 'desktop',
            'ctl' => 'finder',
            'act' => 'object_select',
            'finder_mdl' => 'b2c_mdl_products',
            'multiple' => 'false'
        ));

        // 产品信息
        $this->pagedata['product_row_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'product',
        ));

        // -----------------------
        // 获取万能表单列表
        $this->pagedata['universalform_select_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => 'desktop',
            'ctl' => 'finder',
            'act' => 'object_select',
            'finder_mdl' => 'universalform_mdl_form',
            'multiple' => 'false'
        ));

        // 万能表单信息
        $this->pagedata['universalform_rows_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => 'desktop',
            'ctl' => 'finder',
            'act' => 'object_row',
        ));
        // 获取单个万能表单
        $this->pagedata['universalform_remote'] = vmc::openapi_url('openapi.forms', 'get');



        // ------------------------------ 产品筛选设置
        // 获取商品列表
        $this->pagedata['goods_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => 'desktop',
            'ctl' => 'finder',
            'act' => 'object_select',
            'cols' => 'goods_id,name,gid',
            'finder_mdl' => 'b2c_mdl_goods',
            'multiple' => 'true'
        ));

        // 商品列表信息
        $this->pagedata['goods_row_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'goods',
        ));

        // 获取分类列表
        $this->pagedata['cat_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'cats',
        ));

        // 分类列表信息
        $this->pagedata['cat_row_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'cat',
        ));

        // 获取品牌列表
        $this->pagedata['brand_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => 'desktop',
            'ctl' => 'finder',
            'act' => 'object_select',
            'finder_mdl' => 'b2c_mdl_brand',
            'multiple' => 'true'
        ));

        // 品牌列表信息
        $this->pagedata['brand_row_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'brand',
        ));

        // 获取标签列表
        $this->pagedata['tag_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'tags',
        ));

        // 标签列表信息
        $this->pagedata['tag_row_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'tag',
        ));

        // 获取产品集合列表
        $this->pagedata['goods_collect_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => 'desktop',
            'ctl' => 'finder',
            'act' => 'object_select',
            'finder_mdl' => 'b2c_mdl_goods_collections',
            'multiple' => 'true'
        ));

        // 产品集合列表信息
        $this->pagedata['collect_row_remote'] = app::get('desktop')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'admin_xcxpage',
            'act' => 'collect',
        ));

        $this->page('admin/xcxpage/edit.html');
    }

    public function save($is_pub = false) {
        $this->begin();
        $save_data = $_POST;
        $mdl_xcxpage = app::get('wechat')->model('xcxpage');
        $save_data['widgets_draft'] = json_decode($save_data['widgets_draft'], 1);
        if ($is_pub) {
            $save_data['widgets'] = $save_data['widgets_draft'];
            $save_data['last_pubtime'] = time();
        }
        if (empty($save_data['id']) || !isset($save_data['id'])) {
            $save_data['createtime'] = time();
        }
        $save_data['sno'] = strtolower($save_data['sno']);
        if ($save_data['is_homepage'] == 'true') {
            $mdl_xcxpage->update(array('is_homepage' => 'false'), array('id|notin' => array(-1)));
        }
        $is_save = $mdl_xcxpage->save($save_data);
        $xcxpage_id = $save_data['id'];
        if (!$xcxpage_id) {
            $this->end(false);
        }
        $this->end($is_save, $save_data, "index.php?app=wechat&ctl=admin_xcxpage&act=edit&p[0]=$xcxpage_id&show_tab_widgets=1&draft=" . ($is_pub ? 1 : 0));
    }

    public function ajaxs() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $page_id = $_params && isset($_params['page_id']) ? $_params['page_id'] : null;
        $type = $_params && isset($_params['type']) ? $_params['type'] : null;
        $_method = '_ajax_' . $type;
        if (!method_exists($this, $_method)) return false;
        return call_user_func_array(array($this, $_method), array($page_id));
    }

    public function preview($page_id) {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        !$page_id && $page_id = ($_params && isset($_params['page_id'])) ? $_params['page_id'] : 0;

        $draft = true;
        ($_params && isset($_params['draft'])) && $draft = $_params['draft'] ? true : false;


        if (!$page_id) {
            return;
        }
        $mdl_xcxpage = app::get('wechat')->model('xcxpage');
        $_xcxpage = $mdl_xcxpage->dump($page_id);

        $preview_url = app::get('mobile')->router()->gen_url(array(
            'app' => $this->app->app_id,
            'ctl' => 'mobile_xcxpage',
            'act' => 'preview',
            'args' => array(
                0 => $_xcxpage['sno'],
                1 => $draft ? 1 : 0,
            ),
            'full' => 1,
        ));

        $url = vmc::openapi_url('openapi.qrcode', 'encode', array('size' => 15, 'margin' => 2)) . '?txt=' . urlencode($preview_url);
        echo $url;
        exit;
    }

    public function upimg() {
        $uploadImgFile = ($_FILES && isset($_FILES['uploadImg'])) ? $_FILES['uploadImg'] : null;
        $pageData = array(
            'error' => 500,
            'message' => '上传失败，请稍后再试',
            'url' => null,
        );

        if (!$uploadImgFile) {
            echo json_encode($pageData);
            return;
        }

        $image = app::get('image')->model('image');

        $image_name = $uploadImgFile['name'];
        $image_tmp = $uploadImgFile['tmp_name'];
        $img_ext = strtolower(trim(pathinfo($image_name, PATHINFO_EXTENSION)));
        $extname = array(
            'gif',
            'jpg',
            'png',
            'bmp',
        );
        if (!in_array($img_ext, $extname)) {
            $pageData = array(
                'error' => 400,
                'message' => '上传失败，上传文件扩展名为' . implode(',', $extname),
            );
            echo json_encode($pageData);
            return;
        }

        $image_id = $image->store($image_tmp, null, null, $image_name);
        if (!$image_id) {
            echo json_encode($pageData);
            return;
        }
        //$image->rebuild($image_id,array('L','M','S','XS'));
        $this->_set_tag($image_id, 'wechat');
        $img_url = $this->_getImgUrl($image_id);
        if (!$img_url) {
            echo json_encode($pageData);
            return;
        }
        $pageData = array(
            'error' => 0,
            'message' => null,
            'url' => $img_url,
        );
        echo json_encode($pageData);
        return;
    }

    protected function _getImgUrl($img_id) {
        if (!$img_id) return false;
        $url = base_storager::image_path($img_id, 'L');
        return $url;
    }

    protected function _set_tag($image_id, $tag_name) {
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $data['rel_id'] = $image_id;
        $tags = is_array($tag_name) ? $tag_name : explode(' ', $_POST['tag']['name']);
        $data['tag_type'] = 'image';
        $data['app_id'] = 'image';
        foreach ($tags as $key => $tag) {
            if (!$tag) continue;
            $data['tag_name'] = $tag; //todo 避免重复标签新建
            $tagctl->save($data);
            if ($data['tag_id']) {
                $data2['tag']['tag_id'] = $data['tag_id'];
                $data2['rel_id'] = $image_id;
                $data2['tag_type'] = 'image';
                $data2['app_id'] = 'image';
                $tag_rel->save($data2);
                unset($data['tag_id']);
            }
        }
    }

    public function product() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $product_id = $_params && isset($_params['product_id']) ? $_params['product_id'] : null;
        if (!$product_id) exit;
        $mdl_goods = app::get('b2c')->model('products');
        $filter = array();
        $filter['product_id'] = $product_id;
        $product_list = $mdl_goods->getList('product_id,name,spec_info,bn,barcode,price', $filter);
        if (!$product_list) exit;
        echo json_encode($product_list);
        exit;
    }

    public function xcxpages() {
        // 'is_homepage' => 'false'
        $xcxpages = app::get('wechat')->model('xcxpage')->getList('sno, title', array('is_homepage' => 'false'));
        echo json_encode($xcxpages ? $xcxpages : array());
        exit;
    }

    public function goods() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $goods_id = $_params && isset($_params['goods_id']) ? $_params['goods_id'] : null;
        if (!$goods_id) exit;
        $_mdl = app::get('b2c')->model('goods');
        $filter = array();
        $filter['goods_id'] = $goods_id;
        $lists = $_mdl->getList('gid,name', $filter);
        if (!$lists) exit;
        echo json_encode($lists);
        exit;
    }

    public function cats($page = 1, $keyword = false) {
        $pagelimit = 10;
        $mdl_goods_cat = app::get('b2c')->model('goods_cat');

        $filter = array();
        $keyword && $filter['filter_sql'] = "(`cat_id` LIKE '%{$keyword}%' OR `cat_name` LIKE '%{$keyword}%')";
        $cat_list = $mdl_goods_cat->getList('*', $filter, $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_goods_cat->count($filter);

        $this->pagedata['cat_list'] = $cat_list;
        $this->pagedata['page'] = $page;

        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit),
            'link' => 'index.php?app=' . $this->app->app_id . '&ctl=admin_xcxpage&act=cats&p[0]=' . time() . '&p[1]=' . $keyword . '&in_page=true',
            'token' => time(),
        );

        if ($_GET['in_page']) {
            $this->display('/admin/xcxpage/cats_list_page.html');
        } else {
            $this->page('/admin/xcxpage/cats_list.html');
        }
    }

    public function cat() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $cat_id = $_params && isset($_params['cat_id']) ? $_params['cat_id'] : null;
        if (!$cat_id) exit;
        $_mdl = app::get('b2c')->model('goods_cat');
        $filter = array();
        $filter['cat_id'] = $cat_id;
        $lists = $_mdl->getList('cat_id,cat_name', $filter);
        if (!$lists) exit;
        echo json_encode($lists);
        exit;
    }

    public function brand() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $brand_id = $_params && isset($_params['brand_id']) ? $_params['brand_id'] : null;
        if (!$brand_id) exit;
        $_mdl = app::get('b2c')->model('brand');
        $filter = array();
        $filter['brand_id'] = $brand_id;
        $lists = $_mdl->getList('brand_id,brand_name', $filter);
        if (!$lists) exit;
        echo json_encode($lists);
        exit;
    }

    public function tags($page = 1, $keyword = false) {
        $pagelimit = 10;
        $mdl_tag = app::get('desktop')->model('tag');

        $filter = array();
        $filter['app_id'] = "b2c";
        $filter['tag_type'] = "goods";
        $keyword && $filter['filter_sql'] = "(`tag_id` LIKE '%{$keyword}%' OR `tag_name` LIKE '%{$keyword}%')";
        $tag_list = $mdl_tag->getList('*', $filter, $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_tag->count($filter);

        $this->pagedata['tag_list'] = $tag_list;
        $this->pagedata['page'] = $page;

        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit),
            'link' => 'index.php?app=' . $this->app->app_id . '&ctl=admin_xcxpage&act=tags&p[0]=' . time() . '&p[1]=' . $keyword . '&in_page=true',
            'token' => time(),
        );

        if ($_GET['in_page']) {
            $this->display('/admin/xcxpage/tags_list_page.html');
        } else {
            $this->page('/admin/xcxpage/tags_list.html');
        }
    }

    public function tag() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $tag_name = $_params && isset($_params['tag_name']) ? $_params['tag_name'] : null;
        if (!$tag_name) exit;
        $_mdl = app::get('desktop')->model('tag');
        $filter = array();
        $filter['app_id'] = "b2c";
        $filter['tag_type'] = "goods";
        $filter['filter_sql'] = "(`tag_name` in ('" . implode("', '", $tag_name) . "'))";
        $lists = $_mdl->getList('tag_id,tag_name', $filter);
        if (!$lists) exit;
        echo json_encode($lists);
        exit;
    }

    public function collect() {
        $_params = vmc::singleton('base_component_request')->get_params(true);
        $collect_id = $_params && isset($_params['collect_id']) ? $_params['collect_id'] : null;
        if (!$collect_id) exit;
        $_mdl = app::get('b2c')->model('goods_collections');
        $filter = array();
        $filter['collection_id'] = $collect_id;
        $lists = $_mdl->getList('collection_id,collection_name,goods_count', $filter);
        if (!$lists) exit;
        echo json_encode($lists);
        exit;
    }

}
