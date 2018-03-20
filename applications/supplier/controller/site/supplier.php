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


class supplier_ctl_site_supplier extends supplier_frontpage
{
    public $title = '供应商管理面板';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_supplier_member();
        $this->set_tmpl('supplier');
        $this->pagedata['supplier'] = $this->supplier;
        //$this->supplier   has data
    }

    public function index()
    {
        $supplier = $this->supplier;
        $mdl_reldelivery = $this->app->model('reldelivery');
        $mdl_relgoods = $this->app->model('relgoods');
        $mdl_voucher = $this->app->model('voucher');
        $this->pagedata['count_ready_delivery'] = $mdl_reldelivery->count_delivery($supplier['supplier_id'], array('status' => 'ready'));
        $this->pagedata['count_online_goods'] = $mdl_relgoods->count_relgoods($supplier['supplier_id'], array('marketable' => 'true'));
        $this->pagedata['count_voucher_subprice'] = $mdl_voucher->count_subprice($supplier['supplier_id'], strtotime(date('Y-m-01 00:00:00')));
        $this->page('site/supplier/index.html');
    }

    public function notice_list($page = 1)
    {
        $limit = 10;
        $mdl_notice = $this->app->model('notice');
        $notice_list = $mdl_notice->getList('*', array('ispub' => 'true'), ($page - 1) * $limit, $limit);
        $count = $mdl_notice->count(array('ispub' => 'true'));
        $this->pagedata['notice_list'] = $notice_list;
        $this->pagedata['count'] = $count;
        $this->pagedata['page'] = $page;
        $this->pagedata['totalpage'] = ceil($count / $limit);
        $this->pagedata['pager'] = array(
            'total' => $this->pagedata['totalpage'] ,
            'current' => $page,
            'link' => $this->gen_url(array(
                'app' => 'supplier',
                'ctl' => 'site_supplier',
                'act' => 'notice_list',
                'args' => array(
                    ($token = time()),
                ),
            )),
            'token' => $token,
        );
        $this->display('site/notice/list.html');
    }

    public function notice($notice_id)
    {
        $mdl_notice = $this->app->model('notice');
        $this->set_tmpl('supplier_notice');
        $notice = $mdl_notice->dump($notice_id);
        if ($notice['ispub'] == 'false') {
            $this->splash('error', array(
                'app' => 'supplier',
                'ctl' => 'site_supplier',
                'act' => 'index',
            ), '未发布的公告');
        }
        $this->pagedata['notice'] = $notice;
        $this->page('site/notice/detail.html');
    }

    /**
     * 库存管理 - 首页.
     */
    public function stock($page = 1, $limit = 20)
    {
        if ($limit > 500) {
            $limit = 500;
        }
        $supplier = $this->supplier;
        $mdl_relgoods = $this->app->model('relgoods');
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_product = app::get('b2c')->model('products');
        $mdl_stock = app::get('b2c')->model('stock');

        $_GET = utils::_filter_input($_GET);
        if ($_GET['filter']) {
            $filter_kv = explode('::', $_GET['filter']);
        }
        if (!empty($filter_kv[0]) && !empty($filter_kv[1])) {
            $filter[$filter_kv[0]] = $filter_kv[1];
        }
        $count = $mdl_relgoods->count_relgoods($supplier['supplier_id'], $filter);
        $this->pagedata['count'] = $count;
        $this->pagedata['limit'] = $limit;
        $this->pagedata['page'] = $page;
        $rel_products = $mdl_relgoods->get_products($supplier['supplier_id'], 'p.*', $filter, ($page - 1) * $limit, $limit);
        $sku_bn_arr = array_keys(utils::array_change_key($rel_products, 'bn'));
        $stock_list = $mdl_stock->getList('stock_id,sku_bn,quantity', array('sku_bn' => $sku_bn_arr));
        $stock_list = utils::array_change_key($stock_list, 'sku_bn');
        $this->pagedata['product_list'] = $rel_products;
        $this->pagedata['stock_list'] = $stock_list;
        $pager_url = $this->gen_url(array(
            'app' => 'supplier',
            'ctl' => 'site_supplier',
            'act' => 'stock',
            'args' => array(
                ($token = time()),
                $limit,
            ),
        ));
        if (isset($_GET['filter'])) {
            $pager_url .= '?filter='.$_GET['filter'];
        }
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit) ,
            'current' => $page,
            'link' => $pager_url,
            'token' => $token,
        );
        $this->pagedata['totalpage'] = ceil($count / $limit);
        $this->page('site/supplier/stock.html');
    }
    /**
     * 库存管理 - 手动更新库存.
     */
    public function update_stock()
    {
        $mdl_relgoods = $this->app->model('relgoods');
        $mdl_product = app::get('b2c')->model('products');
        $mdl_stock = app::get('b2c')->model('stock');
        $_POST = utils::_filter_input($_POST);
        $sku_bn = $_POST['sku_bn'];
        $quantity = $_POST['quantity'];
        $stock_id = $_POST['stock_id'];
        $product = $mdl_product->getRow('product_id', array('bn' => $sku_bn));
        if ($mdl_relgoods->count(array('product_id' => $product['product_id'], 'supplier_id' => $this->supplier['supplier_id']))) {
            if ($mdl_stock->save($_POST)) {
                $this->splash('success', null, '库存更新成功');
            } else {
                $this->splash('error', null, '库存更新失败');
            }
        } else {
            $this->splash('error', null, '非法操作'.$product['product_id']);
        }
    }
    /**
     * 库存管理 - 导出库存报表csv.
     */
    public function export_stock()
    {
        $supplier = $this->supplier;
        $supplier_id = $supplier['supplier_id'];
        $filename = $_GET['filename'] ? $_GET['filename'] : ('stock-'.date('YmdHis'));
        $filename .= '.csv';
        $mdl_relgoods = $this->app->model('relgoods');
        $relgoods_product_id_arr = $mdl_relgoods->getColumn('product_id', array('supplier_id' => $supplier_id));
        if (!$relgoods_product_id_arr) {
            die('没有数据可以导出');
        }
        $product_bn_arr = app::get('b2c')->model('products')->getColumn('bn', array('product_id' => $relgoods_product_id_arr));
        if (!$product_bn_arr) {
            die('没有数据可以导出');
        }
        $stock_list = app::get('b2c')->model('stock')->getList('title,sku_bn,barcode,quantity', array('sku_bn' => $product_bn_arr));
        if (!$stock_list) {
            die('没有数据可以导出');
        }
        $csv_str = "名称,货号,条码,库存量\n";
        $csv_str = iconv('utf-8', 'gb2312', $csv_str);
        foreach ($stock_list as $row) {
            $line = implode(',', $row)."\n";
            $line = iconv('utf-8', 'gb2312//IGNORE', $line);
            $csv_str .= $line;
        }
        header('Content-type:text/csv');
        header('Content-Disposition:attachment;filename='.$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $csv_str;
    }
    /**
     * 库存管理 - 从csv批量导入库存.
     */
    public function import_stock()
    {
        $this->begin();
        $csv_file = $_FILES['stockcsv'];
        $file_type = substr(strstr($csv_file['name'],'.'),1);
        if ($file_type != 'csv') {
            $this->end(false, '导入文件格式必须为.csv格式');
        }
        if ($csv_file['error']) {
            logger::warning('代理商csv库存文件上传失败:'.$csv_file['error'].';'.var_export($csv_file, true));
            $this->end(false, '文件上传失败');
        }
        if (($csv_file['size'] / 1024 / 1024) > 3) {
            $this->end(false, 'csv文件大小不能大于3MB');
        }
        $tmp_csvfile = $csv_file['tmp_name'];
        $file = fopen($tmp_csvfile, 'r');
        $mdl_product = app::get('b2c')->model('products');
        $mdl_relgoods = $this->app->model('relgoods');
        while ($data = fgetcsv($file)) {
            $sku_bn = $data[0];
            $quantity = $data[1];
            if (!is_numeric($quantity)) {
                $this->end(false, '存在非法库存数量:'.$sku_bn.','.$quantity);
            }
            $product = $mdl_product->getRow('product_id', array('bn' => $sku_bn));
            if (!$mdl_relgoods->count(array('supplier_id' => $this->supplier['supplier_id'], 'product_id' => $product['product_id']))) {
                $this->end(false, '存在非法货号信息:'.$sku_bn);
            }
            if (!app::get('b2c')->model('stock')->update(array('quantity' => $quantity), array('sku_bn' => $sku_bn))) {
                $this->end(false, '库存更新失败:'.$sku_bn);
            }
        }
        $this->end(true, '库存批量更新成功.');
    }
    public function order($page = 1, $limit = 20, $type = 'all')
    {
        if ($limit > 500) {
            $limit = 500;
        }
        $mdl_reldelivery = $this->app->model('reldelivery');
        $filter = array();
        switch ($type) {
            case 'all':
                //$filter['status'] = array('ready','executing','succ');
                break;
            case 'ready':
                $filter['status'] = 'ready';
                break;
            case 'succ':
                $filter['status'] = 'succ';
                break;
        }
        $this->pagedata['type'] = $type;
        $this->pagedata['page'] = $page;
        $_GET = utils::_filter_input($_GET);
        if ($_GET['filter']) {
            $filter_kv = explode('::', $_GET['filter']);
        }
        if (!empty($filter_kv[0]) && !empty($filter_kv[1])) {
            $filter[$filter_kv[0]] = $filter_kv[1];
        }

        $delivery_list = $mdl_reldelivery->getDeliveryList($this->supplier['supplier_id'], 'd.*', $filter, ($page - 1) * $limit, $limit, 'd.createtime DESC', $count);
        $this->pagedata['delivery_list'] = $delivery_list;
        $pager_url = $this->gen_url(array(
            'app' => 'supplier',
            'ctl' => 'site_supplier',
            'act' => 'order',
            'args' => array(
                ($token = time()),
                $limit,
            ),
        ));
        if (isset($_GET['filter'])) {
            $pager_url .= '?filter='.$_GET['filter'];
        }
        $this->pagedata['count'] = $count;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit) ,
            'current' => $page,
            'link' => $pager_url,
            'token' => $token,
        );
        $this->pagedata['totalpage'] = ceil($count / $limit);
        $this->page('site/supplier/order.html');
    }

    public function order_detail($delivery_id)
    {
        $mdl_reldelivery = $this->app->model('reldelivery');
        if (!$mdl_reldelivery->count(array('delivery_id' => $delivery_id, 'supplier_id' => $this->supplier['supplier_id']))) {
            die('非法操作');
        }
        $mdl_delivery = app::get('b2c')->model('delivery');
        $mdl_product = app::get('b2c')->model('products');
        $delivery = $mdl_delivery->dump($delivery_id, '*', 'delivery_items');
        $delivery_items = $delivery['delivery_items'];
        $product_id_arr = array_keys(utils::array_change_key($delivery_items, 'product_id'));
        $product_list = $mdl_product->getList('*', array('product_id' => $product_id_arr));
        $this->pagedata['product_list'] = utils::array_change_key($product_list, 'product_id');
        $this->pagedata['dlycorp_list'] = app::get('b2c')->model('dlycorp')->getList('*', array(
            'disabled' => 'false',
        ));
        $this->pagedata['delivery'] = $delivery;
        if (!empty($this->pagedata['delivery']['logistics_no']) && $this->pagedata['delivery']['logistics_no'] != '') {
            $this->pagedata['logistics_tracker'] = vmc::singleton('logisticstrack_puller')->pull($delivery_id, $msg);
        }
        $this->page('site/supplier/order_edit.html');
    }

    public function update_order()
    {
        $delivery_id = $_POST['delivery_id'];
        $mdl_reldelivery = $this->app->model('reldelivery');
        if (!$mdl_reldelivery->count(array('delivery_id' => $delivery_id, 'supplier_id' => $this->supplier['supplier_id']))) {
            die('非法操作');
        }
        $this->begin();
        $mdl_delivery = app::get('b2c')->model('delivery');
        $delivery = $mdl_delivery->dump($delivery_id);
        $data = $_POST;
        $data['memo'] = $data['memo'] ? $data['memo'] : '更新操作';
        $data['memo'] = '['.date('Y-m-d H:i:s').']'.$this->supplier['supplier_name'].'：'.$data['memo'];
        if ($delivery['memo']) {
            $delivery['memo'] = $delivery['memo'].'<br>'.$data['memo'];
        } else {
            $delivery['memo'] = $data['memo'];
        }
        if ($delivery['status'] != 'succ') {
            $delivery['status'] = 'succ';
        } else {
            unset($delivery['status']);
        }
        $delivery['logistics_no'] = $data['logistics_no'];
        $delivery['dlycorp_id'] = $data['dlycorp_id'];
        $flag = vmc::singleton('b2c_order_delivery')->update($delivery, $msg);
        $this->end($flag, $msg ? $msg : '保存成功');
    }

    public function update_order_bycsv()
    {
        $this->begin();
        $csv_file = $_FILES['ordercsv'];
        $file_type = substr(strstr($csv_file['name'],'.'),1);
        if ($file_type != 'csv') {
            $this->end(false, '导入文件格式必须为.csv格式');
        }
        if ($csv_file['error']) {
            logger::warning('代理商订单更新csv文件上传失败:'.$csv_file['error'].';'.var_export($csv_file, true));
            $this->end(false, '文件上传失败');
        }
        if (($csv_file['size'] / 1024 / 1024) > 3) {
            $this->end(false, 'csv文件大小不能大于3MB');
        }
        $tmp_csvfile = $csv_file['tmp_name'];
        $file = fopen($tmp_csvfile, 'r');
        $mdl_reldelivery = $this->app->model('reldelivery');
        $mdl_delivery = app::get('b2c')->model('delivery');
        $mdl_dlycorp = app::get('b2c')->model('dlycorp');
        while ($data = fgetcsv($file)) {
            $delivery_id = $data[0];
            $logistics_code = $data[1];
            $logistics_no = $data[2];
            if (empty($data[2])) {
                $this->end(false, '物流单号不能为空');
            }
            if (!$mdl_reldelivery->count(array('delivery_id' => $delivery_id, 'supplier_id' => $this->supplier['supplier_id']))) {
                $this->end(false, '不存在的单号:'.$delivery_id);
            }
            $dlycorp = $mdl_dlycorp->getRow('*', array('corp_code' => $data[1]));
            if (!$dlycorp) {
                $this->end(false, '错误的物流公司代码:'.$data[1]);
            } else {
                $dlycorp_id = $dlycorp['corp_id'];
            }
            $delivery = $mdl_delivery->dump($delivery_id);
            $new_memo = '['.date('Y-m-d H:i:s').']'.$this->supplier['supplier_name'].'：csv导入方式,批量发货确认';
            if ($delivery['memo']) {
                $delivery['memo'] = $delivery['memo'].'<br>'.$new_memo;
            } else {
                $delivery['memo'] = $new_memo;
            }
            if ($delivery['status'] != 'succ') {
                $delivery['status'] = 'succ';
            } else {
                unset($delivery['status']);
            }
            $delivery['logistics_no'] = $logistics_no;
            $delivery['dlycorp_id'] = $dlycorp_id;
            $flag = vmc::singleton('b2c_order_delivery')->update($delivery, $msg);
            if (!$flag) {
                $this->end(false, '单据:'.$delivery_id.'更新失败!更新任务已回滚');
            }
        }
        $this->end(true, '单据批量更新成功.');
    }

    public function voucher($page = 1, $limit = 10)
    {
        if ($limit > 100) {
            $limit = 100;
        }
        $mdl_voucher = $this->app->model('voucher');
        $_GET = utils::_filter_input($_GET);
        $filter = $_GET['filter'];
        if (!is_array($filter)) {
            $filter = array();
        }
        $filter['supplier_id'] = $this->supplier['supplier_id'];
        $this->pagedata['count_subprice'] = $mdl_voucher->count_subprice($this->supplier['supplier_id'], $filter['from'], $filter['to'], $filter['status']);

        foreach ($filter as $key => $value) {
            if ($value == '') {
                unset($filter[$key]);
                continue;
            }
            if ($key == 'from') {
                $filter['createtime|bthan'] = strtotime($value);
                unset($filter[$key]);
            }
            if ($key == 'to') {
                $filter['createtime|lthan'] = strtotime($value);
                unset($filter[$key]);
            }
        }
        $voucher_list = $mdl_voucher->getList('*', $filter, ($page - 1) * $limit, $limit);

        if ($voucher_list) {
            $voucher_id_arr = array_keys(utils::array_change_key($voucher_list, 'voucher_id'));
            $mdl_voucher_items = $this->app->model('voucher_items');
            $voucher_items = $mdl_voucher_items->getList('*', array('voucher_id' => $voucher_id_arr));
            $this->pagedata['voucher_items'] = utils::array_change_key($voucher_items, 'voucher_id', true);
            $count = $mdl_voucher->count($filter);
            $this->pagedata['voucher_list'] = $voucher_list;
            $this->pagedata['count'] = $count;
            $this->pagedata['page'] = $page;
            $this->pagedata['totalpage'] = ceil($count / $limit);
            $pager_url = $this->gen_url(array(
                'app' => 'supplier',
                'ctl' => 'site_supplier',
                'act' => 'voucher',
                'args' => array(
                    ($token = time()),
                    $limit,
                ),
            ));
            $pager_url .= '?'.http_build_query($_GET);
            $this->pagedata['count'] = $count;
            $this->pagedata['limit'] = $limit;
            $this->pagedata['pager'] = array(
                'total' => $this->pagedata['totalpage'] ,
                'current' => $page,
                'link' => $pager_url,
                'token' => $token,
            );
        }
        $this->page('site/supplier/voucher.html');
    }

    public function statement($page = 1, $limit = 20)
    {
        if ($limit > 100) {
            $limit = 100;
        }
        $mdl_statement = $this->app->model('statement');
        $_GET = utils::_filter_input($_GET);
        $filter = $_GET['filter'];
        if (!is_array($filter)) {
            $filter = array();
        }
        $filter['supplier_id'] = $this->supplier['supplier_id'];
        $filter['status'] = 'succ';
        $statement_list = $mdl_statement->getList('*', $filter, ($page - 1) * $limit, $limit);

        if ($statement_list) {
            $count = $mdl_statement->count($filter);
            $this->pagedata['statement_list'] = $statement_list;
            $this->pagedata['page'] = $page;
            $this->pagedata['totalpage'] = ceil($count / $limit);
            $pager_url = $this->gen_url(array(
                'app' => 'supplier',
                'ctl' => 'site_supplier',
                'act' => 'statement',
                'args' => array(
                    ($token = time()),
                    $limit,
                ),
            ));
            $this->pagedata['count'] = $count;
            $this->pagedata['limit'] = $limit;
            $this->pagedata['pager'] = array(
                'total' => $this->pagedata['totalpage'] ,
                'current' => $page,
                'link' => $pager_url,
                'token' => $token,
            );
        }
        $this->page('site/supplier/statement.html');
    }

    public function statement_detail($statement_id)
    {
        $mdl_statement = $this->app->model('statement');
        $filter['supplier_id'] = $this->supplier['supplier_id'];
        $filter['status'] = 'succ';
        $filter['statement_id'] = $statement_id;
        $statement = $mdl_statement->getRow('*', $filter);
        if ($statement) {
            $mdl_statement_index = $this->app->model('statement_index');
            $mdl_voucher = $this->app->model('voucher');
            $mdl_voucher_items = $this->app->model('voucher_items');
            $voucher_id_arr = $mdl_statement_index->getColumn('voucher_id', array('statement_id' => $statement_id));
            $voucher_list = $mdl_voucher->getList('*', array('voucher_id' => $voucher_id_arr));
            $voucher_list_items = $mdl_voucher_items->getList('*', array('voucher_id' => $voucher_id_arr));
            $voucher_list_items = utils::array_change_key($voucher_list_items, 'voucher_id', true);
            $this->pagedata['statement'] = $statement;
            $this->pagedata['voucher_list'] = $voucher_list;
            $this->pagedata['voucher_list_items'] = $voucher_list_items;
        }
        $this->page('site/supplier/statement_detail.html');
    }

    public function profile($action = 'eidt')
    {
        $supplier = $this->supplier;
        if ($action == 'save') {
            $supplier_save_data = array(
                'supplier_manager' => $_POST['supplier']['supplier_manager'],
                'supplier_address' => $_POST['supplier']['supplier_address'],
                'supplier_region' => $_POST['supplier']['supplier_region'],
                'supplier_link1' => $_POST['supplier']['supplier_link1'],
                'supplier_link2' => $_POST['supplier']['supplier_link2'],
            );
            $supplier = array_merge($supplier, $supplier_save_data);
            $mdl_supplier = $this->app->model('supplier');
            if ($mdl_supplier->save($supplier)) {
                $this->splash('success', array('app' => 'supplier', 'ctl' => 'site_supplier', 'act' => 'profile'), '保存成功');
            } else {
                $this->splash('success', array('app' => 'supplier', 'ctl' => 'site_supplier', 'act' => 'profile'), '保存失败');
            }
        } else {
            $this->page('site/supplier/profile.html');
        }
    }

    /**
     * 物流公司代码查询.
     */
    public function logistics_bn_list()
    {
        $mdl_dlycorp = app::get('b2c')->model('dlycorp');
        $dlycorp_list = $mdl_dlycorp->getList('corp_code,name');
        $this->pagedata['dlycorp_list'] = $dlycorp_list;
        $this->page('site/supplier/dlycorp_list.html');
    }
     /**
      * 导出功能.
      */
     public function export_list()
     {
         $supplier_id = $this->supplier['supplier_id'];
         $_POST = utils::_filter_input($_POST);
         $object = $_POST['export_object'];
         switch ($object) {
             case 'order':
             case 'statement':
             case 'voucher':
                 break;
             default:
                die('ERROR EXPORT OBJECT');
                 break;
         }
         $filter = $_POST['filter'];
         $filter['supplier_id'] = $this->supplier['supplier_id'];
         foreach ($filter as $key => $value) {
             if (!$value || $value == '') {
                 unset($filter[$key]);
             }
             switch ($key) {
                 case 'createtime|lthan':
                 case 'createtime|bthan':
                     if (!is_numeric($value)) {
                         $filter[$key] = strtotime($value);
                     }
                     break;
                 default:
                     # code...
                     break;
             }
         }
         header("Content-type: text/html; charset=utf-8");
         if (empty($filter['createtime|lthan']) || empty($filter['createtime|bthan'])) {
             die('无效的时间段1[必须限定时间段,并且时间跨度不能大于3个月]');
         }
         if ($filter['createtime|bthan'] > $filter['createtime|lthan']) {
             die('无效的时间段2[必须限定时间段,并且时间跨度不能大于3个月]');
         }
         if (($filter['createtime|lthan'] - $filter['createtime|bthan']) > (3600 * 24 * 100)) {
             die('无效的时间段3[必须限定时间段,并且时间跨度不能大于3个月]');
         }
         vmc::singleton('supplier_export_'.$object)->doexport($filter);
     }
}
