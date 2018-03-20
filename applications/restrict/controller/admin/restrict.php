<?php
class restrict_ctl_admin_restrict extends desktop_controller {

    /**
     * 构造方法.
     *
     * @params object app object
     */
    public function __construct(&$app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        $this->finder('restrict_mdl_restrict',array(
            'title' => '限购列表',
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'actions' => [[
                'label' => '添加限购',
                'href' => 'index.php?app=restrict&ctl=admin_restrict&act=edit',
                'target' =>'_ACTION_MODAL_'
            ]]
        //    'base_filter' => array('filter_sql'=>'project_id NOT IN (SELECT project_id FROM `vmc_shop_shop_project`)')
        ));
    }
    public function edit($res_id) {
        $mdl_restrict = $this->app->model('restrict');
        //////////////////////////// 会员等级 //////////////////////////////
        $mMemberLevel = app::get('b2c')->model('member_lv');
        $this->pagedata['member_level'] = $mMemberLevel->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');
        if($res_id){
            $restrict = $mdl_restrict->getRow('*',array('res_id'=>$res_id));
            $member_lv_ids = $this->app->model('member_lv')->getList('*',array('res_id'=>$restrict['res_id']));
            if($member_lv_ids) {
                $restrict['member_lv_ids'] = array_keys(utils::array_change_key($member_lv_ids,'member_lv_id'));
            }
            //处理选择商品/选择货品前台显示格式json数据
            if($goods_limit = $this->app->model('goods')->getList('*',array('res_id'=>$res_id))) {
                $goods_limit = utils::array_change_key($goods_limit,'goods_id');
                $goods_ids = json_encode(array_keys($goods_limit));
                $this->pagedata['goods_ids'] = $goods_ids;
                $this->pagedata['goods_limit'] = json_encode($goods_limit);
            };
            if($product_limit = $this->app->model('products')->getList('*',array('res_id'=>$res_id))) {
                $product_limit = utils::array_change_key($product_limit,'product_id');
                $product_ids = json_encode(array_keys($product_limit));
                $this->pagedata['product_ids'] = $product_ids;
                $this->pagedata['product_limit'] = json_encode($product_limit);
            };
            $this->pagedata['restrict'] = $restrict;
        }

        $this->page('admin/edit.html');
    }

    public function save()
    {
        $mdl_restrict = $this->app->model('restrict');
        $restrict = $_POST['restrict'];
        $this->begin('index.php?app=restrict&ctl=admin_restrict&act=index');
            //验证货品的数量是否超出商品的数量
            if(is_array($_POST['product_limit'])) {
                $product_list = app::get('b2c')->model('products')->getList('goods_id,product_id,name', array('product_id' => array_keys($_POST['product_limit'])));
                $product_list = utils::array_change_key($product_list, 'product_id');
                $goods_sum = array();
                foreach ($_POST['product_limit'] as $k => $product) {
                    $goods_id = $product_list[$k]['goods_id'];
                    if (!$_POST['goods_limit'][$goods_id]) {
                        continue;
                    }
                    $goods_sum[$goods_id]['order_limit'] += $product['order_limit'];
                    $goods_sum[$goods_id]['member_limit'] += $product['member_limit'];
                    $goods_sum[$goods_id]['day_times_limit'] += $product['day_times_limit'];
                    $goods_sum[$goods_id]['day_member_limit'] += $product['day_member_limit'];
                    $goods_sum[$goods_id]['sum'] += $product['sum'];
                    if ($_POST['goods_limit'][$goods_id]['order_limit'] < $goods_sum[$goods_id]['order_limit']) {
                        $this->end(false, '订单最多购买数量超出：' . $product_list[$k]['name']);
                    }
                    if ($_POST['goods_limit'][$goods_id]['day_times_limit'] < $goods_sum[$goods_id]['day_times_limit']) {
                        $this->end(false, '用户每天限购次数	：' . $product_list[$k]['name']);
                    }
                    if ($_POST['goods_limit'][$goods_id]['day_member_limit'] < $goods_sum[$goods_id]['day_member_limit']) {
                        $this->end(false, '用户每天限购数量	：' . $product_list[$k]['name']);
                    }
                    if ($_POST['goods_limit'][$goods_id]['member_limit'] < $goods_sum[$goods_id]['member_limit']) {
                        $this->end(false, '用户最多购买数量	：' . $product_list[$k]['name']);
                    }
                    if ($_POST['goods_limit'][$goods_id]['sum'] < $goods_sum[$goods_id]['sum']) {
                        $this->end(false, '总数量超出：' . $product_list[$k]['name']);
                    }
                }
            }

            $restrict['from_time'] = strtotime($restrict['from_time']);
            $restrict['to_time'] = strtotime($restrict['to_time']);
            if(empty($restrict['to_time']) || empty($restrict['from_time']) || ($restrict['to_time']<$restrict['from_time'])){
                $this->end(false,'结束时间不能小于开始时间');
            }
            if( empty($restrict['goods_id']) &&  empty($restrict['product_id'])){
                $this->end(false,'商品或货品不能为空');
            }
            if( empty($restrict['res_id']) ){
                $restrict['createtime'] = time();
            }
            $member_lv_ids = $restrict['member_lv_ids'];
            if( !$mdl_restrict->save($restrict) ){
                $this->end(false,'限购信息保存失败');
            }

            if(!$this->save_goods($restrict['goods_id'],$_POST['goods_limit'],$restrict['res_id'],$msg)) {
                $this->end(false,$msg?:'限购商品保存失败');
            }

            if(!$this->save_product($restrict['product_id'],$_POST['product_limit'],$restrict['res_id'],$msg)) {
                $this->end(false,$msg?:'限购货品保存失败');
            }


            if(is_array($member_lv_ids)) {
                $mdl_member_lv = $this->app->model('member_lv');
                if(!$mdl_member_lv->delete(array('res_id'=>$restrict['res_id']))) {
                    $this->end(false,'会员等级清除操作失败');
                };
                foreach($member_lv_ids as $v) {
                    $member_lv_data = array(
                        'member_lv_id' => $v,
                        'res_id' => $restrict['res_id']
                    );
                    if(!$mdl_member_lv->insert($member_lv_data)) {
                        $this->end(false,'会员等级操作失败');
                    };
                }
            }


        $this->end(true,'保存成功');
    }

    private function save_goods($goods_ids,$goods_limit,$res_id,&$msg) {
        if(!$res_id) {
            $msg = '未知限购';
            return false;
        }
        $mdl_restrict_goods = $this->app->model('goods');
        if(!$mdl_restrict_goods->delete(array('res_id'=>$res_id))) {
            $msg = '操作失败';
            return false;
        };
        if($goods_ids) {
            foreach($goods_ids as $id) {
                $data = $goods_limit[$id];
                $data['goods_id'] = $id;
                $data['res_id'] = $res_id;
                if(!$mdl_restrict_goods->insert($data)) {
                    $msg = '保存商品失败';
                    return false;
                };
            }
        }
        return true;
    }

    private function save_product($product_ids,$product_limit,$res_id,&$msg) {
        if(!$res_id) {
            $msg = '未知限购';
            return false;
        }
        $mdl_restrict_products = $this->app->model('products');
        if(!$mdl_restrict_products->delete(array('res_id'=>$res_id))) {
            $msg = '操作失败';
            return false;
        };
        if($product_ids) {
            foreach($product_ids as $id) {
                $data = $product_limit[$id];
                $data['product_id'] = $id;
                $data['res_id'] = $res_id;
                if(!$mdl_restrict_products->insert($data)) {
                    $msg = '保存货品失败';
                    return false;
                };
            }
        }
        return true;
    }

}

