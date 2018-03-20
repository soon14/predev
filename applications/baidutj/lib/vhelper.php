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


class baidutj_vhelper
{


    public function function_SYSTEM_FOOTER($params, &$smarty)
    {
        $req_obj = $smarty->_request;
        $ctl_name = $req_obj->get_ctl_name();
        $act_name = $req_obj->get_act_name();
        $req_params = $req_obj->get_params();
        $hook = 'site_checkout-payment-1';
        $html = "<div style='display:none' data-rel='Baidu Analytics'>";
        $html.= app::get('baidutj')->getConf('baidutj_code');
        if($hook == ($ctl_name.'-'.$act_name.'-'.$req_params[1])){
            $order_id = $req_params[0];
            $html.=$this->_ecommerce_js($order_id);
        }
        $html.="</div>\n";
        return $html;
    }//End Function

    public function function_SYSTEM_FOOTER_M($params, &$smarty)
    {
        $req_obj = $smarty->_request;
        $ctl_name = $req_obj->get_ctl_name();
        $act_name = $req_obj->get_act_name();
        $req_params = $req_obj->get_params();
        $hook = 'mobile_checkout-payment-1';
        $html = "<div style='display:none' data-rel='Baidu Analytics'>";
        $html.= app::get('baidutj')->getConf('baidutj_code');
        if($hook == ($ctl_name.'-'.$act_name.'-'.$req_params[1])){
            $order_id = $req_params[0];
            $html.=$this->_ecommerce_js($order_id);
        }
        $html.="</div>\n";
        return $html;
    }//End Function

    private function _ecommerce_js($order_id){
        if(!$order_id || !$_SERVER['HTTP_REFERER'] || app::get('baidutj')->getConf('ecommerce_tj') != 'true'){
            return "";
        }
        $order = app::get('b2c')->model('orders')->dump($order_id,'*',array(
            'items' => array(
                '*',
            )
        ));
        if(!$order || empty($order['items'])){
            return '';
        }
        $order_info = array(
            'orderId'=>$order['order_id'],
            'orderTotal'=>number_format($order['order_total'],2,'.','')
        );
        foreach ($order['items'] as $key=>$item) {
            $goods_id_arr[] = $item['goods_id'];
            $order_info['item'][$key.'-'.$item['goods_id']] = array(
                'skuId'=>$item['bn'],
                'skuName'=>$item['name'].($item['spec_info']?'('.$item['spec_info'].')':''),
                'category'=>'',
                'Price'=>number_format($item['buy_price'],2,'.',''),
                'Quantity'=>$item['nums'],
            );
        }
        //计算分类路径
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_goods_cat = app::get('b2c')->model('goods_cat');
        $goods_list = $mdl_goods->getList('goods_id,cat_id',array('goods_id'=>$goods_id_arr));
        $goods_list_bycat = utils::array_change_key($goods_list,'cat_id');
        $cat_id_arr = array_keys($goods_list_bycat);
        $cat_path = $mdl_goods_cat->getList('cat_id,cat_path',array('cat_id'=>$cat_id_arr));
        foreach ($cat_path as $k=>$row) {
            $cat_path_name = $mdl_goods_cat->getColumn('cat_name',array('cat_id'=>explode(',',$row['cat_path'])));
            $goods_list_bycat[$row['cat_id']]['cat_path'] = implode(' > ',$cat_path_name);
        }
        $goods_list = utils::array_change_key($goods_list_bycat,'goods_id');
        foreach ($order_info['item'] as $key => $value) {
            $goods_id = explode('-',$key);
            $goods_id = intval($goods_id[1]);
            $order_info['item'][$key]['category'] = $goods_list[$goods_id]['cat_path'];
        }
        $order_info['item'] = array_values($order_info['item']);
        $order_info_json = json_encode($order_info);
        $return_html = '<script>';
        $return_html.= " _hmt.push(['_trackOrder',$order_info_json]);";
        $return_html.='</script>';
        return $return_html;
    }

}//End Class
