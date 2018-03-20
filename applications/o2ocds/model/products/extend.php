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
class o2ocds_mdl_products_extend extends dbeav_model
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
    public function get_o2ocds_value($goods_id ,$type='o2ocds_value')
    {
        $good_type = app::get('b2c')->model('goods')->getRow('type_id', array('goods_id' => $goods_id));
        $type_o2ocds = $this->app->model('type_extend')->getRow('o2ocds_value',
            array('type_id' => $good_type['type_id']));

        return $type_o2ocds[$type];
    }

    /*
     * 获取类型分佣设置，适用不同等级会员
     */
    public function get_lv_o2ocds_value($goods_id){
        return $this ->get_o2ocds_value($goods_id ,'lv_o2ocds_value');
    }
    /*
     * 分享商品，预计可得佣金
     */
    public function get_expect_o2ocds($product_id ,$member){
        $product = $this ->getRow("*" ,array('product_id' => $product_id));
        if($this ->app->getConf('mode') == 1){//与会员等级无关
            $o2ocds = $product['o2ocds_value']['first'] ? $product['o2ocds_value']['first'] :0;
            if(!$o2ocds){
                $type_o2ocds = $this ->get_o2ocds_value($product['goods_id']);
                $o2ocds = $type_o2ocds['first'] ? $type_o2ocds['first'] :0;
            }
            if(!$o2ocds){
                $o2ocds = $this->app->getConf('enterprise_ration') ? $this->app->getConf('enterprise_ration') :0;
            }
            if(!$o2ocds){
                return 0;
            }else{
                $product = app::get('b2c') ->model('products') ->getRow('price' ,array('product_id' => $product_id));
                return $o2ocds <1 ?$o2ocds*$product['price'] :$o2ocds;
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
            $o2ocds = $product['lv_o2ocds_value']['lv'.$step] ? $product['lv_o2ocds_value']['lv'.$step] :0;
            if(!$o2ocds){
                $type_o2ocds = $this ->get_lv_o2ocds_value($product['goods_id']);
                $o2ocds = $type_o2ocds['lv'.$step] ? $type_o2ocds['lv'.$step]:0;
            }
            if(!$o2ocds){
                $o2ocds = $this->app->getConf('lv'.$step) ? $this->app->getConf('lv'.$step) :0;
            }
            if(!$o2ocds){
                return 0;
            }else{
                $product = app::get('b2c') ->model('products') ->getRow('price' ,array('product_id' => $product_id));
                return $o2ocds <1 ?$o2ocds*$product['price'] :$o2ocds;
            }
        }

    }
}