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

class materiallib_ctl_admin_material extends materiallib_desktop {

    function __construct(&$app) {
        parent::__construct($app);
    }

    public function index($tag_id = '') {
        $params = $this->params;

        $query_str = '';
        $filter = array();

        if ($params['tag_id']) {
            $tag_id = $params['tag_id'];
        }
        $this->pagedata['is_multiple'] = $params['is_multiple'] ? true : false;
        if($this->pagedata['is_multiple']){
             $query_str .= ($query_str ? '&' : '') . 'is_multiple=1';
        }
        if ($tag_id) {
            $filter['filter_sql'] = "image_id IN(SELECT image_id FROM vmc_materiallib_tag_image WHERE tag_id = {$tag_id})";
            $query_str .= ($query_str ? '&' : '') . 'tag_id=' . $tag_id;
        }

        $limit = 12;
        $mdl_image = $this->app->model('images');
        $mdl_tags = $this->app->model('tags');
        $images = $mdl_image->getList('*', $filter, 0, 12);
        $tags = $mdl_tags->getList('*');
        $count = $mdl_image->count();
        if ($params['use_material']) {
            $this->pagedata['use_material'] = $params['use_material'];
            $query_str .= ($query_str ? '&' : '') . 'use_material=true';
        }
        $time = time();
        $this->pagedata['pager'] = array(
            'total' => ($count ? ceil($count / $limit) : 1),
            'current' => 1,
            'token' => $time,
            'link' => 'index.php?app=materiallib&ctl=admin_material&act=image_main&page=' . $time . ($query_str ? '&' . $query_str : '')
        );

        if($images){
            foreach ($images as $k => $v) {
                $images[$k]['image_path'] = base_storager::image_path($v['image_id'], 'm');
            }
        }

        $this->pagedata['images'] = $images;
        $this->pagedata['tags'] = $tags;
        $this->page('admin/material/index.html');
    }

    //图片列表
    public function image_main($tag_id = '') {
        $limit = 12;
        $params = $this->params;
        $mdl_image = $this->app->model('images');
        if ($params['tag_id']) {
            $tag_id = $params['tag_id'];
        }
        $page = $params['page'];
        !$page && $page = 1;
        if ($tag_id) {
            $filter['filter_sql'] = "image_id IN(SELECT image_id FROM vmc_materiallib_tag_image WHERE tag_id = {$tag_id})";
            $query_str = 'tag_id=' . $tag_id;
        }
        $images = $mdl_image->getList('*', $filter, $limit * ($page - 1), $limit);
        $count = $mdl_image->count($filter);
        $time = time();
        $this->pagedata['pager'] = array(
            'total' => ($count ? ceil($count / $limit) : 1),
            'current' => $page,
            'token' => $time,
            'link' => 'index.php?app=materiallib&ctl=admin_material&act=image_main&page=' . $time . ($query_str ? '&' . $query_str : '')
        );
        if($images){
            foreach ($images as $k => $v) {
                $images[$k]['image_path'] = base_storager::image_path($v['image_id'], 'm');
            }
        }
        $this->pagedata['images'] = $images;
        $this->page('admin/material/image_main.html');
    }

    //保存素材库 图片
    public function save_image() {
        $mdl_image = $this->app->model('images');
        if (!$image_id = $_POST['image_id']) {
            $this->splash('error', '', '未知图片');
        };
        $data = array(
            'image_id' => $image_id,
            'createtime' => time()
        );
        if (!$mdl_image->save($data)) {
            $this->splash('error', '', '保存图库失败');
        };
        if($tag_id = $_POST['tag_id']){
            $this->set_media_tag($image_id, $tag_id);
        }
        $this->image_main();
    }

    //删除素材库 图片
    public function delete_image() {
        $this->begin();
        $mdl_image = $this->app->model('images');
        $mdl_tag_image = $this->app->model('tag_image');
        if (!$image_ids = $_POST['image_id']) {
            $this->end(false, '未知图片');
        };
        if (!$mdl_image->delete(array('image_id' => $image_ids))) {
            $this->end(false, '删除失败');
        };
        if (!$mdl_tag_image->delete(array('image_id' => $image_ids))) {
            $this->end(false, '删除失败');
        };
        $this->end(true, '删除成功');
    }

    //操作标签
    public function save_tag() {
        $data = utils::_filter_input($_POST);
        $this->begin();
        if (!$data['tag_id']) {
            unset($data['tag_id']);
            $data['createtime'] = time();
        }
        $data['tag_name'] = trim($data['tag_name']);
        !strlen($data['tag_name']) && $this->end(false, '请输入标签名');
        if (mb_strlen($data['tag_name'], 'utf-8') > 6) {
            $this->end(false, '标签名字为1-6个字符');
        }
        if (!$this->app->model('tags')->save($data)) {
            $this->end(false, '标签操作失败');
        };
        $this->end(true, '操作成功', '', $data);
    }

    //删除标签
    public function delete_tag() {
        $data = utils::_filter_input($_GET);
        $this->begin();
        if (!$data['tag_id']) {
            $this->end(false, '未知标签');
        }
        $mdl_tags = $this->app->model('tags');
        if (!$mdl_tags->delete(array('tag_id' => $data['tag_id']))) {
            $this->end(false, '删除标签失败');
        };
        $this->end(true, '删除成功', '', $data);
    }

    //操作图片打标签
    public function save_tag_image() {
        $params = utils::_filter_input($_POST);
        $this->begin();

        if (!$tag_ids = $params['tag_ids']) {
            $this->end(false, '未知标签');
        }

        if (!$image_ids = $params['image_ids']) {
            $this->end(false, '未知图片');
        }

        !is_array($tag_ids) && $tag_ids = explode(',', $tag_ids);
        !is_array($image_ids) && $image_ids = explode(',', $image_ids);

        //按照标签给图片打上标签
        foreach ($tag_ids as $tag_id) {
            foreach ($image_ids as $image_id) {
                if (!$this->set_media_tag($image_id, $tag_id)) {
                    $this->end(false, '操作失败');
                };
            }
        }
        $this->end(true, '操作成功');
    }

    protected function set_media_tag($img_id, $tag_id){
        if(!$img_id || !$tag_id) return false;

        $mdl_tag_image = $this->app->model('tag_image');
        $data = array(
                    'image_id' => $img_id,
                    'tag_id' => $tag_id,
                );
        return $mdl_tag_image->save($data);
    }

    //解除标签和图片的绑定关系
    public function delete_tags_images(){
        $params = utils::_filter_input($_POST);
        
        $this->begin();

        if (!$tag_ids = $params['tag_ids']) {
            $this->end(false, '未知标签');
        }

        if (!$image_ids = $params['image_ids']) {
            $this->end(false, '未知图片');
        }

        !is_array($tag_ids) && $tag_ids = explode(',', $tag_ids);
        !is_array($image_ids) && $image_ids = explode(',', $image_ids);

        //按照标签给图片打上标签
        foreach ($tag_ids as $tag_id) {
            foreach ($image_ids as $image_id) {
                $this->del_media_tag($image_id, $tag_id);
            }
        }
        $this->end(true, '操作成功');
    }

    public function delete_tag_image() {

        $mdl_tag_image = $this->app->model('tag_image');
        $mdl_tags = $this->app->model('tags');
        $data = utils::_filter_input($_POST);
        if (!$data['image_id']) {
            $this->splash('error', '', '未知图片');
        }
        if (!$data['tag_id'] && $mdl_tags->getRow('*', array('tag_id' => $data['tag_id']))) {
            $this->splash('error', '', '未知标签');
        }
        if (!$mdl_tag_image->delete($data)) {
            $this->splash('error', '', '移除失败');
        };
        $this->image_main($data['tag_id']);
    }

    protected function del_media_tag($img_id, $tag_id){
        if(!$img_id || !$tag_id) return false;

        $mdl_tag_image = $this->app->model('tag_image');
        $mdl_tags = $this->app->model('tags');
        $data = array(
            'tag_id' => $tag_id,
            'image_id' => $img_id,
        );

        if (!$mdl_tags->getRow('*', array('tag_id' => $tag_id))) {
            return false;
        }
        if (!$mdl_tag_image->delete($data)) {
            return false;
        };
        return;
    }

    public function media_upload() {
        $upMediaFile = ($_FILES && isset($_FILES['uploadImg'])) ? $_FILES['uploadImg'] : null;
        !$upMediaFile && $this->failure('参数错误!');

        $image = app::get('image')->model('image');

        $image_name = $upMediaFile['name'];
        $image_tmp = $upMediaFile['tmp_name'];
        $img_ext = strtolower(trim(pathinfo($image_name, PATHINFO_EXTENSION)));
        $extname = array(
            'gif',
            'jpg',
            'png',
            'bmp',
        );

        !in_array($img_ext, $extname) && $this->failure('上传失败，上传文件扩展名为' . implode(',', $extname));

        $image_id = $image->store($image_tmp, null, null, $image_name);
        !$image_id && $this->failure('上传失败');

        $this->_set_tag($image_id, 'material');
        $img_url = $this->_getImgUrl($image_id);
        !$img_url && $this->failure('上传失败');

        $this->success(array(
            'image_id' => $image_id,
            'url' => $img_url,
        ));
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

}
