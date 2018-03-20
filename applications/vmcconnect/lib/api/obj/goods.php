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

class vmcconnect_api_obj_goods extends vmcconnect_api_obj_base {

    protected $_fields = 'goods_id, gid, name, type, category, extended_cat, brand, marketable, uptime, downtime, last_modify, w_order, gain_score, brief, goods_type, image_default_id, description, min_buy, nostore_sell, goods_setting, spec_desc, disabled, comment_count, view_w_count, view_count, buy_count, buy_w_count, params, props, seo_info, product, images';
    protected $_sku_fields = 'product_id, goods_id, bn, barcode, name, price, mktprice, weight, unit, spec_info, spec_desc, is_default, image_id, uptime, downtime, last_modify, disabled, marketable';
    protected $_stock_fields = 'stock_id, title, sku_bn, barcode, quantity, freez_quantity, warehouse, last_modify';

    /*
     * 新增商品
     */
    public function write_add() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // ----------
        if (!isset($name)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }

        if (!isset($cat_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }

        // ----------
        if (!strlen($name)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }

        if (!is_numeric($cat_id) || $cat_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_save_good($params);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['create_time'] = date('Y-m-d', ($data['create_time'] ? $data['create_time'] : time()));

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 修改商品
     */
    public function write_update() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // ----------
        if (!isset($goods_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'goods_id'
            );
            return $res;
        }
        if (!isset($name)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }

        if (!isset($cat_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }

        // ----------
        if (!$goods_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'goods_id'
            );
            return $res;
        }
        if (!strlen($name)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }

        if (!is_numeric($cat_id) || $cat_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_save_good($params);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['modified'] = date('Y-m-d', ($data['modified'] ? $data['modified'] : time()));

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 商品上下架
     */
    public function write_upOrDown() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // ----------
        if (!isset($goods_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'goods_id'
            );
            return $res;
        }
        if (!isset($op_type)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'op_type'
            );
            return $res;
        }

        // ----------
        if (!$goods_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'goods_id'
            );
            return $res;
        }
        // 返回数据
        $data = $this->_up_or_down($goods_id, $op_type);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['modified'] = date('Y-m-d', ($data['modified'] ? $data['modified'] : time()));

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 获取单个商品
     */
    public function read_byId() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($goods_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'goods_id'
            );
            return $res;
        }
        if (!is_numeric($goods_id) || $goods_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'goods_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_byId($goods_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 获取单个SKU
     */
    public function sku_read_findSkuById() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($sku_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'sku_id'
            );
            return $res;
        }
        if (!$sku_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'sku_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_skuById($sku_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 获取sku库存信息
     */
    public function sku_stock_read_find() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($sku_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'sku_id'
            );
            return $res;
        }
        if (!$sku_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'sku_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_skuStockById($sku_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 设置sku库存
     */
    public function sku_stock_write_update() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // ----------
        if (!isset($sku_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'sku_id'
            );
            return $res;
        }
        if (!isset($quantity)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'quantity'
            );
            return $res;
        }

        // ----------
        if (!$sku_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'sku_id'
            );
            return $res;
        }
        if (!is_numeric($quantity) || $quantity < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'quantity'
            );
            return $res;
        }
        // 返回数据
        $data = $this->_set_sku_stock($sku_id, $quantity);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['modified'] = date('Y-m-d', ($data['modified'] ? $data['modified'] : time()));

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    // --------------------------------------------------- 相关方法
    protected function _mod_cat() {
        static $mod_goods_cat;
        if ($mod_goods_cat) return $mod_goods_cat;
        $mod_goods_cat = app::get('b2c')->model('goods_cat');
        return $mod_goods_cat;
    }

    protected function _mod_goods() {
        static $mod_goods;
        if ($mod_goods) return $mod_goods;
        $mod_goods = app::get('b2c')->model('goods');
        return $mod_goods;
    }

    protected function _mod_products() {
        static $mod_products;
        if ($mod_products) return $mod_products;
        $mod_products = app::get('b2c')->model('products');
        return $mod_products;
    }

    protected function _mod_image() {
        static $mod_image;
        if ($mod_image) return $mod_image;
        $mod_image = app::get('image')->model('image');
        return $mod_image;
    }

    protected function _gid() {
        return 'v' . str_pad(substr(preg_replace('/[a-z]|4/', '', uniqid()), -6), 8, rand(10, 99), STR_PAD_BOTH);
    }

    protected function _bn() {
        return 'bn' . str_pad(preg_replace('/[a-z]/', '', substr(uniqid(), -5)), 7, time(), STR_PAD_BOTH);
    }

    protected function _tmp_file_name() {
        return md5(str_pad(substr(preg_replace('/[a-z]|4/', '', uniqid()), -6), 8, rand(10, 99), STR_PAD_BOTH)) . '.jpg';
    }

    protected function _set_tag($image_id, $tag_name) {
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $data['rel_id'] = $image_id;
        $tags = $tag_name;
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

    protected function _up_images(&$images) {

        $upload_images = Array(
            '5911bbb3Na397d8eb' => '816a635a713d6e148e2f6f9a48cfedde',
            '5911bbbdNca037b83' => '992c75bd8e4d415cfda2cd9e84ac56a5',
            '59130e4cNa6d07fe0' => 'fcc30dd13f1c0dc4c2614c7d5e50be51',
            '59130edbN396f5dc4' => '0d8a70d077ea8f3815ea59928d0c2eb6',
        );
        return $upload_images;

        if (!$images || !is_array($images)) return false;

        $mod_img = $this->_mod_image();
        $_tmp_save_dir = TMP_DIR . '/up_tmp/';
        !is_dir($_tmp_save_dir) && mkdir($_tmp_save_dir, 0666, true);
        if (!is_dir($_tmp_save_dir)) return false;
        $upload_images = array();
        foreach ($images as $_k => $_v) {
            if (is_string() && strlen($_v) < 40) {
                $upload_images[$_k] = $_v;
                continue;
            }
            $_tmp_file = $_tmp_save_dir . $this->_tmp_file_name();

            file_put_contents($_tmp_file, $_v);
            $_image_id = $mod_img->store($_tmp_file, null, null, $_k);
            is_file($_tmp_file) && unlink($_tmp_file);
            if (!$_image_id) continue;
            $upload_images[$_k] = $_image_id;
            $this->_set_tag($_image_id, array('商品相册图'));
            $_image_id && $mod_img->rebuild($_image_id, array('L', 'M', 'S', 'XS'));
        }
        return $upload_images;
    }

    protected function _save_good($params) {

        $good_id = (isset($params['goods_id']) && $params['goods_id']) ? $params['goods_id'] : null;
        $is_update = ($good_id) ? true : false;
        $datas = array();
        $is_update && $datas['goods_id'] = (int) $params['goods_id'];

        // 商品编号
        $datas['gid'] = isset($params['gid']) ? trim($params['gid']) : $this->_gid();
        // 商品名称
        $datas['name'] = trim($params['name']);

        // 商品简介
        $datas['brief'] = (isset($params['brief']) && strlen($params['brief'])) ? trim($params['brief']) : '';

        // 商品分类
        $datas['category'] = array(
            'cat_id' => (int) $params['cat_id'],
        );
        // 扩展分类
        $datas['extended_cat'] = isset($params['extended_cat']) ? (is_array($params['extended_cat']) ? $params['extended_cat'] : array((int) $params['extended_cat'])) : array();

        // 上架销售
        $datas['marketable'] = (!isset($params['marketable']) || $params['marketable']) ? 'true' : 'false';
        // 是否忽略库存
        $datas['nostore_sell'] = isset($params['nostore_sell']) ? (int) $params['nostore_sell'] : 0;
        // 积分
        $datas['gain_score'] = (isset($params['score']) && $params['score']) ? (int) $params['score'] : null;

        // 详细介绍
        $datas['description'] = isset($params['description']) ? trim($params['description']) : '';
        // 商品类型
        $datas['type'] = isset($params['type']) ? (
                is_array($params['type']) ?
                $params['type'] :
                array(
            'type_id' => (int) $params['type'],
                )) : array();

        //  --------------------------------
        // props 商品类型 扩展属性
        $_props = null;
        if (isset($params['type_props'])) {
            !is_array($params['type_props']) && $params['type_props'] = explode(',', $params['type_props']);
            $_props = array();
            $n = 1;
            foreach ($params['type_props'] as $_v) {
                $_props['p_' . $n] = array(
                    'value' => $_v,
                );
                $n++;
            }
        }
        $datas['props'] = $_props;

        // params 商品类型 商品参数表
        $datas['params'] = (isset($params['type_params']) && is_array($params['type_params'])) ? $params['type_params'] : null;

        // 品牌
        $datas['brand'] = isset($params['brand']) ? (
                is_array($params['brand']) ?
                $params['brand'] :
                array(
            'brand_id' => (int) $params['brand'],
                )) : array();

        // 设置
        $datas['goods_setting'] = array(
            'spec_info_vimage' => -1,
            'site_template' => 'item-default.html',
            'mobile_template' => 'item-default.html',
        );

        // SEO相关
        $datas['seo_info'] = array(
            'seo_title' => (isset($params['seo_title']) ? trim($params['seo_title']) : $datas['name']),
            'seo_keywords' => (isset($params['seo_keywords']) ? trim($params['seo_keywords']) : $datas['name']),
            'seo_description' => (isset($params['seo_description']) ? trim($params['seo_description']) : $datas['name']),
        );

        $datas['keywords'] = array();
        if (isset($params['seo_title']) && $params['keywords']) {
            $_keywords = is_array($params['keywords']) ? $params['keywords'] : explode(',', $params['keywords']);
            foreach ($_keywords as $_v) {
                $_v = trim($_v);
                if (!strlen($_v)) continue;
                $datas['keywords'][] = array(
                    'keyword' => $_v,
                    'res_type' => 'goods',
                );
            }
        }
        
        // 商品类型
        $datas['goods_type'] = (isset($params['goods_type']) && in_array($params['goods_type'], array(
                    'normal',
                    'bind',
                    'gift',
                ))) ? $params['goods_type'] : 'normal';

        // 先处理图象
        $_good_images = (isset($params['images']) && $params['images']) ? $params['images'] : null;
        $upload_images = $this->_up_images($_good_images);

        // images
        $_image_default_id = null;
        $_default_img = (isset($params['default_img']) && $params['default_img']) ? $params['default_img'] : null;
        $datas['images'] = array();
        if ($upload_images) {
            foreach ($upload_images as $_k => $_v) {
                if (!$_image_default_id || ($_default_img && $_default_img == $_k)) {
                    $_image_default_id = $_v;
                }
                $datas['images'][] = array(
                    'target_type' => 'goods',
                    'image_id' => $_v,
                );
            }
        }
        $datas['image_default_id'] = $_image_default_id;

        // ----------------- 默认数据
        // 货号 
        $_bn = isset($params['bn']) && $params['bn'] ? $params['bn'] : null;
        // 条码 
        $_barcode = isset($params['barcode']) && $params['barcode'] ? $params['barcode'] : null;
        // 销售价 
        $_price = isset($params['price']) && $params['price'] ? $params['price'] : 0;
        // 市场价 
        $_mktprice = isset($params['mktprice']) && $params['mktprice'] ? $params['mktprice'] : 0;
        // 重量 
        $_weight = isset($params['weight']) && $params['weight'] ? $params['weight'] : 0;
        // 单位 
        $_unit = isset($params['unit']) && $params['unit'] ? $params['unit'] : '件';
        // 规格 
        $_spec = isset($params['spec']) && $params['spec'] ? $params['spec'] : null;
        // 上架销售 
        $_marketable = $datas['marketable'];

        // 商品可选规格
        $_spec = array();
        if (isset($params['specs']) && $params['specs'] && is_array($params['specs'])) {
            foreach ($params['specs'] as $_k => $_v) {
                $_spec['t'][] = $_k;
                $_spec['v'][] = ($_v ? (is_array($_v) ? implode(',', $_v) : $_v) : null);
            }
        }
        $datas['spec_desc'] = $_spec;
        
        // prod
        $_products = array();
        
        $is_set_def = false;
        
        $_list_exts = array();
        $_def_prod_id = null;

        
        if (isset($params['products']) && $params['products'] && is_array($params['products'])) {
            $i = 1;
            foreach ($params['products'] as $_k => $_v) {
                $__spec = (isset($_v['spec']) && is_array($_v['spec'])) ? $_v['spec'] : null;
                if (!$__spec) continue;
                $_tmp = array();
                
                isset($_v['product_id']) && $_v['product_id'] && $_tmp['product_id'] = $_v['product_id'];

                $_prod_id = $_tmp['product_id'];

                $_tmp['spec_info'] = implode('/', $__spec);
                $_tmp['spec_desc'] = implode(':::', $__spec);

                $__tmp_bn = (isset($_v['bn']) && $_v['bn']) ? $_v['bn'] : null;
                if (
                        $this->_mod_products()->count(array(
                            'bn' => $__tmp_bn,
                            'goods_id|notin' => $good_id,
                        )) > 0
                ) {
                    $__tmp_bn = $this->_bn();
                }
                !$__tmp_bn && $__tmp_bn = $this->_bn();

                $_tmp['bn'] = $__tmp_bn;
                $_tmp['barcode'] = (isset($_v['barcode']) && $_v['barcode']) ? $_v['barcode'] : $_barcode;
                $_tmp['price'] = (isset($_v['price']) && $_v['price']) ? $_v['price'] : $_price;
                $_tmp['mktprice'] = (isset($_v['mktprice']) && $_v['mktprice']) ? $_v['mktprice'] : $_mktprice;
                $_tmp['weight'] = (isset($_v['weight']) && $_v['weight']) ? $_v['weight'] : $_weight;
                $_tmp['unit'] = (isset($_v['unit']) && $_v['unit']) ? $_v['unit'] : $_unit;

                $_tmp['marketable'] = (isset($_v['marketable']) && $_v['marketable']) ? ($_v['marketable'] ? 'true' : 'false') : $_marketable;

                $_tmp['name'] = (isset($_v['name']) && $_v['name']) ? $_v['name'] : ($datas['name'] . ' ' . $_tmp['spec_info']);
                $_tmp_img = (isset($_v['image']) && $_v['image']) ? $_v['image'] : null;
                $_tmp['image_id'] = ($_tmp_img && $upload_images && isset($upload_images[$_tmp_img])) ? $upload_images[$_tmp_img] : ($_tmp_img && $upload_images && in_array($_tmp_img, $upload_images) ? $_tmp_img : $_image_default_id);

                $_in_list = (isset($_v['list_extension']) && $_v['list_extension']) ? $_v['list_extension'] : $_list_extension;
                $_in_list && $_prod_id && $_list_exts[] = $_prod_id;

                $_def = (isset($_v['is_default']) && $_v['is_default'] == 'true') ? true : false;
                if ($_def && !$is_set_def) {
                    $is_set_def = false;
                } else {
                    $_def = false;
                }
                $_tmp['is_default'] = $_def ? 'true' : 'false';

                !$_def_prod_id && $_prod_id && $_def_prod_id = $_prod_id;
                
                $_products['new-' . $i] = $_tmp;
                
                $i++;
            }
        }


        if (
                (!isset($params['products']) || !$params['products']) &&
                (!isset($params['specs']) || !$params['specs']) &&
                $_bn
        ) {
            if (
                    $this->_mod_products()->count(array(
                        'bn' => $_bn,
                        'goods_id|notin' => $good_id,
                    )) > 0
            ) {
                $_bn = $this->_bn();
            }
            $_products = array();
            $_new_0 = array(
                'marketable' => $_marketable,
                'bn' => $_bn,
                'barcode' => $_barcode,
                'price' => $_price,
                'mktprice' => $_mktprice,
                'weight' => $_weight,
                'unit' => $_unit,
                'spec_info' => $_spec,
                'is_default' => true,
                'image_id' => $_image_default_id,
            );

            $_products['new_0'] = $_new_0;
        }
        $datas['product'] = $_products;
        
        $_def_prod_id && $datas['default_product'] = $_def_prod_id;
        $_list_exts && $datas['goods_setting']['list_extension'] = $_list_exts;
        

        if (!$datas['product'] || !strlen($datas['name'])) {
            return false;
        }

        $_mod_goods = $this->_mod_goods();

        $this->_begin();


        $_mod_goods->has_many['product'] = 'products:contrast';
        $res = $_mod_goods->save($datas);

        if (!$res || !isset($datas['goods_id']) || !$datas['goods_id']) {
            $this->_end(false);
            return false;
        }

        vmc::singleton('b2c_goods_stock')->refresh($msg);
        
        $_new_id = $datas['goods_id'];
        
        $get_prods = $this->_mod_products()->getList('bn, spec_info', array('goods_id' => $_new_id));
        $_skus = array();
        if($get_prods){
            foreach ($get_prods as $_v){
                $_skus[$_v['bn']] = $_v['spec_info'];
            }
        }
        
        $res_data = array();
        !$is_update && $res_data['create_time'] = time();
        $is_update && $res_data['modified'] = time();
        $res_data['goods_id'] = $_new_id;
        $res_data['skus'] = $_skus;

        $this->_end(true);

        return $res_data;
    }

    protected function _up_or_down($good_id, $op_type) {

        $_set_data = array();
        $_set_data['marketable'] = $op_type ? 'true' : 'false';

        $filter = array();
        $filter['goods_id'] = array($good_id);

        $this->_begin();
        if (!$this->_mod_goods()->update($_set_data, $filter) || !$this->_mod_products()->update($_set_data, $filter)) {
            $this->_end(false);
            return false;
        }

        $res_data = array();
        $res_data['modified'] = time();
        $res_data['goods_id'] = $good_id;

        $this->_end(true);

        return $res_data;
    }

    protected function _get_good($goods_id) {
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $goods_id = (int) $goods_id;
        if (!$goods_id) return false;

        $row = $this->_mod_goods()->dump($goods_id, '*', 'default');
        if (!$row) return false;

        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_byId($good_id, $fields) {
        $row = $this->_get_good($good_id);
        if (!$row) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $res = $this->_field_row($fields, $row);
        return $res;
    }

    protected function _get_sku($sku_id) {
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $sku_id = $sku_id;
        if (!$sku_id) return false;

        $row = $this->_mod_products()->getRow('*', array(
            'bn' => $sku_id,
        ));
        if (!$row) return false;

        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_skuById($sku_id, $fields) {
        $row = $this->_get_sku($sku_id);
        if (!$row) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_sku_fields;
        $res = $this->_field_row($fields, $row);
        return $res;
    }

    protected function _set_sku_stock($sku_id, $quantity) {

        $filter = array();
        $filter['sku_bn'] = $sku_id;

        $data = array();
        $data['quantity'] = (int) $quantity;

        $this->_begin();
        if (!app::get('b2c')->model('stock')->update($data, $filter)) {
            $this->_end(false);
            return false;
        }

        $res_data = array();
        $res_data['modified'] = time();

        $this->_end(true);

        return $res_data;
    }

    protected function _get_skuStock($sku_id) {
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();
        if (!$sku_id) return false;
        
        $sku_id = !is_array($sku_id) ? explode(',', $sku_id) : $sku_id;

        $row = app::get('b2c')->model('stock')->getRow('*', array(
            'sku_bn|in' => $sku_id,
        ));
        if (!$row) return false;

        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_skuStockById($sku_id, $fields) {
        $row = $this->_get_skuStock($sku_id);
        if (!$row) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_stock_fields;
        $res = $this->_field_row($fields, $row);
        return $res;
    }

}
