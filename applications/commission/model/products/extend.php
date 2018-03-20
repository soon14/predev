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
class commission_mdl_products_extend extends dbeav_model
{
    var $defaultOrder = array('product_id','DESC');
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
    }

    /*
     * 获取类型分佣设置，适用不同分佣关系
     */
    public function get_commission_value($goods_id ,$type='commission_value')
    {
        $good_type = app::get('b2c')->model('goods')->getRow('type_id', array('goods_id' => $goods_id));
        $type_commission = $this->app->model('type_extend')->getRow('commission_value',
            array('type_id' => $good_type['type_id']));

        return $type_commission[$type];
    }

    /*
     * 获取类型分佣设置，适用不同等级会员
     */
    public function get_lv_commission_value($goods_id){
        return $this ->get_commission_value($goods_id ,'lv_commission_value');
    }
    /*
     * 分享商品，预计可得佣金
     */
    public function get_expect_commission($product_id ,$member){
        $product = $this ->getRow("*" ,array('product_id' => $product_id));
        if($this ->app->getConf('mode') == 1){//与会员等级无关
            $commission = $product['commission_value']['first'] ? $product['commission_value']['first'] :0;
            if(!$commission){
                $type_commission = $this ->get_commission_value($product['goods_id']);
                $commission = $type_commission['first'] ? $type_commission['first'] :0;
            }
            if(!$commission){
                $commission = $this->app->getConf('first_ratio') ? $this->app->getConf('first_ratio') :0;
            }
            if(!$commission){
                return 0;
            }else{
                $product = app::get('b2c') ->model('products') ->getRow('price' ,array('product_id' => $product_id));
                return $commission <1 ?$commission*$product['price'] :$commission;
            }
        }else{//与会员等级有关
            $lv = app::get('b2c')->model('member_lv')->getList("member_lv_id", array('disabled' => 'false'), 0, -1,
                'experience ASC');
            $step = 0;
            foreach($lv as $k=>$v){
                if($v['member_lv_id'] == $member['member_lv']){
                    $step = $k;break;
                }
            }
            $commission = $product['lv_commission_value']['lv'.$step] ? $product['lv_commission_value']['lv'.$step] :0;
            if(!$commission){
                $type_commission = $this ->get_lv_commission_value($product['goods_id']);
                $commission = $type_commission['lv'.$step] ? $type_commission['lv'.$step]:0;
            }
            if(!$commission){
                $commission = $this->app->getConf('lv'.$step) ? $this->app->getConf('lv'.$step) :0;
            }
            if(!$commission){
                return 0;
            }else{
                $product = app::get('b2c') ->model('products') ->getRow('price' ,array('product_id' => $product_id));
                return $commission <1 ?$commission*$product['price'] :$commission;
            }
        }

    }
}