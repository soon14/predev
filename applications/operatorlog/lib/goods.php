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



#商品
class operatorlog_goods
{
    function __construct(){
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
    }


    function brand_log($newdata,$olddata){
        if(empty($newdata['brand_id'])){
            $this->objlog->logs('goods', '添加商品品牌', '添加商品品牌 '.$_POST['brand_name']);
        }else{
            $modify_flag = 0;
            $data = array();
            foreach($newdata as $key=>$val){
                if($newdata[$key] != $olddata[$key]){
                    $data['new'][$key] = $val;
                    $data['old'][$key] = $olddata[$key];
                    $modify_flag++;
                }
            }
            if($modify_flag>0){
                $memo  = "serialize".$this->delimiter."编辑商品品牌ID {$_POST['brand_name']}".$this->delimiter.serialize($data);
                $this->objlog->logs('goods', '编辑商品品牌', $memo);
            }
        }
    }


    function goodscat_log($newdata,$olddata){
        if(empty($olddata)){
            $this->objlog->logs('goods', '添加商品分类', '添加商品分类 '.$_POST['cat']['cat_name']);
        }else{
            $modify_flag = 0;
            $data = array();
            foreach($newdata as $key=>$val){
                if($newdata[$key] != $olddata[$key]){
                    $data['new'][$key] = $val;
                    $data['old'][$key] = $olddata[$key];
                    $modify_flag++;
                }
            }
            if($modify_flag>0){
                $memo  = "serialize".$this->delimiter."编辑商品分类 {$_POST['cat']['cat_name']}".$this->delimiter.serialize($data);
                $this->objlog->logs('goods', '编辑商品分类', $memo);
            }
        }
    }


    function virtualcat_log($newdata,$olddata){
        if(empty($_POST['cat']['virtual_cat_id'])){
            $this->objlog->logs('goods', '添加虚拟分类', '添加虚拟分类 '.$_POST['cat']['virtual_cat_name']);
        }else{
            $modify_flag = 0;
            $data = array();
            foreach($newdata as $key=>$val){
                if($newdata[$key] != $olddata[$key]){
                    $data['new'][$key] = $val;
                    $data['old'][$key] = $olddata[$key];
                    $modify_flag++;
                }
            }
            if($modify_flag>0){
                $memo  = "serialize".$this->delimiter."编辑虚拟分类 {$_POST['cat']['virtual_cat_name']}".$this->delimiter.serialize($data);
                $this->objlog->logs('goods', '编辑虚拟分类', $memo);
            }
        }
    }



    function batchUpdateText( $goods_id, $updateType , $updateName , $updateValue){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志,商品名称和商品简介@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        $arr = array('name'=>'商品名称','brief'=>'商品简介');
        $goodsbn = implode(',',$goods_id);
        switch($updateType){
            case 'name':
            $memo = '商品ID为('.$goodsbn.')的'.$arr[$updateName].'全部修改为('.$updateValue.')';
            break;
            case 'add':
            $memo = '商品ID为('.$goodsbn.')的'.$arr[$updateName].'增加前缀('.$updateValue['front'].')后缀('.$updateValue['after'].')';
            break;
            case 'replace':
            $memo = '商品ID为('.$goodsbn.')的'.$arr[$updateName].'查找名称中有('.$updateValue['front'].')的替换为('.$updateValue['after'].')';
            break;
        }

        $operate_type = '批量编辑 '.$arr[$updateName];
        $this->objlog->logs('goods', $operate_type, $memo);
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志,商品名称和商品简介@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }

    function batchUpdateInt( $goods_id, $updateName, $updateValue , $tableName = '' ){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志,商品排序和分类转换@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        $arr = array('d_order'=>'商品排序','cat_id'=>'分类转换');
        $goodsbn = implode(',',$goods_id);
        if($updateName == 'cat_id'){
          $catName = app::get('b2c')->model('products')->db->selectrow('SELECT cat_name FROM vmc_b2c_goods_cat WHERE cat_id = '.intval($updateValue) );
            $upvalue = $catName['cat_name'];
        }else{
            $upvalue = $updateValue;
        }
        $memo = '商品ID为('.$goodsbn.')的('.$arr[$updateName].')全部修改为('.$upvalue.')';

        $operate_type = '批量编辑 '.$arr[$updateName];
        $this->objlog->logs('goods', $operate_type, $memo);
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志,商品排序和分类转换@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }


    function batchUpdateArray( $goods_id , $tableName, $updateName, $updateValue ){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志,商品品牌@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        $goodsbn = implode(',',$goods_id);
        $memo = '商品ID为('.$goodsbn.')的(商品品牌)全部修改为('.$updateValue['1'].')';

        $this->objlog->logs('goods', '批量编辑 商品品牌', $memo);
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志,商品品牌@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }


    function batchUpdateByOperator( $goods_id, $tableName, $updateName , $updateValue, $operator=null , $fromName = null ){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志，统一调价，调库存，调质量@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        if($tableName=='vmc_b2c_products'){
            $arr_name=array('mktprice'=>'市场价','price'=>'销售价','cost'=>'成本价','store'=>'库存','weight'=>'重量');
            $basicinfo = app::get('b2c')->model('products')->getList('mktprice,price,cost,store,weight,bn',array('goods_id'=>$goods_id));
            $v2tmp = '';
            foreach($basicinfo as $key=>$val){
                $v2tmp .='('.$val['bn'].' 改为 '.$val[$updateName].'),';
            }
            $productsbn=rtrim($v2tmp,',');
            $memo = '商品货号'.$productsbn;
            $operate_key = '统一修改商品'.$arr_name[$updateName];

            $operate_type = '批量编辑 '.$arr_name[$updateName];
            $this->objlog->logs('goods', $operate_type, $memo);
        }
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志，统一调价，调库存，调质量@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }

    function batchUpdateStore($store){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志,分别调整商品库存@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        $memo_tmps='';
        foreach( $store as $goods ){
            $memo_tmp='';
            foreach( $goods as $proId => $pstore ){
                $pstore = trim($pstore);
                $probn = app::get('b2c')->model('products')->dump(array('product_id'=>$proId),'bn');
                if($pstore === '0'){
                    $id_store = array('probn'=>$probn['bn'],'pstore'=>'0');
                }elseif(empty($pstore)){
                    $id_store = array('probn'=>$probn['bn'],'pstore'=>'空');
                }else{
                    $id_store = array('probn'=>$probn['bn'],'pstore'=>(intval($pstore)<0?0:intval($pstore)));
                }
                $memo_tmp .='货号：'.$id_store['probn'].',库存:'.$id_store['pstore'].'; ';
            }
            $memo_tmps.= $memo_tmp;
        }
        $memo = '批量修改('.$memo_tmps.')';

        $this->objlog->logs('goods', '批量编辑 分别调库存', $memo);
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志,分别调整商品库存@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }


    function batchUpdatePrice($pricedata){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志，分别调价@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        $obj_prt = app::get('b2c')->model('products');
        $obj_lv = app::get('b2c')->model('member_lv');
        $arr_mapname = array('price'=>'销售价','cost'=>'成本价','mktprice'=>'市场价');
        $memo='';
        foreach( $pricedata as $updateName => $data ){
            $m1='';
            $m2='';
            if( in_array( $updateName , array( 'price', 'cost','mktprice' ) ) ) {
                foreach( $data as $goodsId => $goodsItem ){
                    $memo_tmp1='';
                    foreach( $goodsItem as $proId => $price ){
                        #$probn = $obj_prt->dump(array('product_id'=>$proId),'bn');
                        $probn = $obj_prt->getRow('bn',array('product_id'=>$proId));
                        $memo_tmp1 .= '修改货号为('.$probn['bn'].')的('.$arr_mapname[$updateName].')为('.$price.');<br> ';
                    }
                    $m1.=$memo_tmp1;
                }
            }
            else{
                #$lv_name = $obj_lv->dump(array('member_lv_id'=>$updateName),'name,dis_count');
                $lv_name = $obj_lv->getRow('name,dis_count',array('member_lv_id'=>$updateName));
                foreach( $data as $goodsId => $goodsItem ){
                    $memo_tmp2='';
                    foreach( $goodsItem as $proId => $price ){
                        #$probn = $obj_prt->dump(array('product_id'=>$proId),'bn');
                        $probn = $obj_prt->getRow('bn',array('product_id'=>$proId));
                        if( $price == null || $price == '' ){
                            $price= $pricedata['price'][$goodsId][$proId]*$lv_name['dis_count'];
                            eval("\$price=$price;");
                        }
                        $memo_tmp2 .= '修改货号为('.$probn['bn'].')的('.$lv_name['name'].')价为('.$price.');<br> ';
                    }
                    $m2.=$memo_tmp2;
                }
            }
            $memo .= $m1.$m2;
        }

        $this->objlog->logs('goods', '批量编辑 分别调价', $memo);
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志，分别调价@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }


    function batchUpdateMemberPriceByOperator( $goods_id, $updateLvId, $updateValue, $operator, $fromName){
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志，统一调会员价@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        if($operator && $updateValue != null && $updateValue !='' ){
            $opesql = 'SELECT  distinct sbglp.goods_id,sbml.name,sbglp.price
                       FROM vmc_b2c_goods_lv_price AS sbglp, vmc_b2c_member_lv AS sbml
                       WHERE sbglp.goods_id IN(' . implode(',',$goods_id) . ") AND sbglp.level_id={$updateLvId} AND sbml.member_lv_id={$updateLvId}";
            $probninfo = vmc::database()->select($opesql);
            $bntmp = '';
            foreach($probninfo as $key=>$val){
                $bntmp .='商品ID为 '.$val['goods_id'].' 的'.$val['name'].'价改为 '.$val['price'].',';
            }
            $memo=rtrim($bntmp,',');

            $this->objlog->logs('goods', '统一调会员价', $memo);
        }
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志，统一调会员价@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }

    function new_goods($gName){
        $memo = '添加新商品 '.$gName;
        $this->objlog->logs('goods', '添加新商品', $memo);
    }


    function goods_log($newdata,$olddata){
        $modify_flag = 0;
        $data = array();
        foreach($newdata as $key=>$val){
            if($newdata[$key] != $olddata[$key]){
                $data['new'][$key] = $val;
                $data['old'][$key] = $olddata[$key];
                $modify_flag++;
            }
        }
        if($modify_flag>0){
            $memo  = "serialize".$this->delimiter."编辑商品ID{$newdata['goods_id']}".$this->delimiter.serialize($data);
            $this->objlog->logs('goods', '编辑商品', $memo);
        }
    }


    function logproducts($product){
        //echo "<pre>";print_r($product);exit;
        $member_lvinfo = app::get('b2c')->model('member_lv')->getList('member_lv_id,name,dis_count');
        $lv_name=array();
        $lv_discount=array();
        foreach($member_lvinfo as $keylv=>$vallv){
            $lv_name[$vallv['member_lv_id']]=$vallv['name'];//会员名称
            $lv_discount[$vallv['member_lv_id']]=$vallv['dis_count'];//会员价折扣
        }
        $mkt_name = array('true'=>'是','false'=>'否');
        $memo  = '修改货品ID为('.$product['product_id'].')的,';
        $memo .= '货品编码为('.$product['bn'].'),';
        $memo .= '货品库存为('.$product['store'].'),';
        $memo .= '货品销售价为('.$product['price']['price']['price'].'),';
        $memo .= '货品成本价为('.$product['price']['cost']['price'].'),';
        $memo .= '货品市场价为('.$product['price']['mktprice']['price'].'),';
        $memo .= '货品上下架状态为('.$mkt_name[$product['status']].'),';
        $memo .= '货品重量为('.$product['weight'].'),';
        $memo_lv='';
        foreach($product['price']['member_lv_price'] as $key=>$val){
            if($val['price']==''){
                $val['price'] = $product['price']['price']['price']*$lv_discount[$val['level_id']];
            }
            $memo_lv .= $lv_name[$val['level_id']].'价改为('.$val['price'].'),';
        }
        $memo=rtrim($memo.$memo_lv,',');

        $this->objlog->logs('goods', '修改货品', $memo);
    }

    function products_log($newdata,$olddata){
        foreach ($newdata as $k => $v) {
            $modify_flag = 0;
            $data = array();
            foreach($v as $key=>$val){
                if($v[$key] != $olddata[$k][$key]){
                    $data['new'][$key] = $val;
                    $data['old'][$key] = $olddata[$k][$key];
                    $modify_flag++;
                }
            }
            if($modify_flag>0){
                $memo  = "serialize".$this->delimiter."编辑货品ID {$k}".$this->delimiter.serialize($data);
                $this->objlog->logs('goods', '编辑货品', $memo);
            }
        }
    }

    function all_marketable($status){
        $arr = array('true'=>'上架','false'=>'下架');
        $this->objlog->logs('goods', '批量'.$arr[$status].'所有商品','批量'.$arr[$status].'所有商品');
    }


}//End Class
