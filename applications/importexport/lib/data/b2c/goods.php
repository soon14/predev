<?php

class importexport_data_b2c_goods
{
    private $gid = array();

    /*-----------------------以下为导入函数-----------------------*/
    public function get_import_title(){
        return array(
            'gid' => '商品编号(gid)',
            'name' => '商品名称(name)',
            'cat_id' => '分类(cat_id)',
            'props' => '扩展分类(props)',
            'type_id' => '类型(type_id)',
            'brand_id' => '品牌(brand_id)',
            'brief' => '商品简介(brief)',
            'image' => '相册(image)',
            'intro' => '图文介绍(intro)',
            'spec'=>'规格(spec)',
            'bn'=>'货号(bn)',
            'barcode'=>'条码(barcode)',
            'stock'=>'库存(stock)',
            'price'=>'销售价(price)',
            'mktprice' => '市场价(mktprice)',
            'maketable' => '上架(maketable)',
            'weight' => '重量(weight)',
            'unit' => '单位(unit)',
        );

    }

    public function need_continue($current ,&$rows ){
        if(!empty($rows) && $current['gid'] !=$rows[0]['gid']){
            return false;
        }
        $rows[] = $current;
        return true;
    }

    public function dataToSdf($contents, &$msg)
    {
        $goods_mdl = app::get('b2c')->model('goods');
        $goods = current($contents);
        try{
            $this ->_check_column($goods);
            //goods表基础数据
            $goodsData = array(
                'gid' => $goods['gid'],
                'name' => $goods['name'],
                'marketable' => trim($goods['maketable'])=="是" ?'true' :'false',
                'brief' => $goods['brief'],
                'description' => $goods['intro'],
            );
            $goodsData['category']['cat_id'] = $this ->_get_cat_id($goods);
            $goodsData['type']['type_id'] =$this ->_get_type_id($goods);
            $goodsData['brand']['brand_id'] =$this ->_get_brand_id($goods);
            $image = $this ->_get_images($goods);
            if(!empty($image) ){
                $goodsData['images'] = $image['images'];
                $goodsData['image_default_id'] = $image['image_default_id'];
            }
            //products表数据
            $product_info =$this ->_get_product($contents);
            $goodsData['product'] = $product_info['product'];

            if(!empty($product_info['spec_desc'])){
                $goodsData['spec_desc'] = $this ->_get_spec_desc($product_info['spec_desc']);
            }
            if( $goodsRow = $goods_mdl->getRow('goods_id,spec_desc', array('bn|tequal'=>$goods['gid'])) ){
                //如果gid已存在
                //    return '';
            }
            $this ->gid[] = $goods['gid'];
            return $goodsData;
        }catch (Exception $e){
            $msg = $e ->getMessage();
        }
    }


    public function import_end(&$msg){
        //更新库存表
        return vmc::singleton('b2c_goods_stock')->refresh($msg);
    }


    //商品主信息校验
    private function _check_column($goods){
        if( !$goods['gid'] ){
            throw new Exception(app::get('importexport')->_('商品名称为：').$goods['name'].app::get('importexport')->_(' 的商品编号必填'));
        }

        if( $this->gid && in_array($goods['bn'],$this->gid) ){
            throw new Exception(app::get('importexport')->_('商品编号重复：').$goods['bn']);
        }
        //商品名称不能为空
        if(!$goods['name']){
            throw new Exception(app::get('importexport')->_('商品编号为：').$goods['bn'].app::get('importexport')->_('的商品名称不能为空'));
        }

        if($goods['brief']&&strlen($goods['brief'])>210){
            //简短的商品介绍,请不要超过70个字！
            throw new Exception(app::get('importexport')->_('商品编号为'.$goods['bn'].'的商品介绍,请不要超过70个字!'));
        }
    }

    //商品分类
    private function _get_cat_id($goods){
        if($goods['cat_id']){
            $cat = app::get('b2c') ->model('goods_cat') ->getRow('cat_id' ,array('cat_name' => trim($goods['cat_id'])));
            if($cat){
                return $cat['cat_id'];
            }else{
                $cat =array(
                    'cat_name' =>$goods['cat_id'],
                );
                if(!app::get('b2c') ->model('goods_cat')->save($cat)){
                    throw new Exception(app::get('importexport')->_($goods['cat_id'].'分类创建失败'));
                }
                return $cat['cat_id'];
            }
        }
    }

    //商品类型
    private function _get_type_id($goods){
        if($goods['type_id']){
            $type = app::get('b2c') ->model('goods_type') ->getRow('type_id' ,array('name' => trim($goods['type_id'])));
            if($type){
                return $type['type_id'];
            }else{
                throw new Exception(app::get('importexport')->_($goods['type_id'].'类型不存在'));
            }
        }
    }

    //商品品牌
    private function _get_brand_id($goods){
        if($goods['brand_id']){
            $brand = app::get('b2c') ->model('brand') ->getRow('brand_id' ,array('brand_name' => trim($goods['brand_id'])));
            if($brand){
                return $brand['brand_id'];
            }else{
                logger::info(app::get('importexport')->_($goods['brand_id'].'品牌不存在'));
                $data = array(
                    'brand_name' => trim($goods['brand_id']),
                );
                if(app::get('b2c') ->model('brand') ->save($data)){
                    return $data['brand_id'];
                }else{
                    throw new Exception(app::get('importexport')->_($goods['brand_id'].'品牌添加失败'));
                }
            }
        }
    }


    //获取products和spec
    private function _get_product($contents){
        $products_mdl = app::get('b2c') ->model('products');
        $product = array();
        $spec_desc = array();
        $i = 0;
        foreach($contents as $k => $row){
            $is_exist = $products_mdl ->count(array('bn' => $row['bn']));
            if($is_exist){
                logger::warning('导入的商品('.$row['name'].')货号已存在');
            }else{
                $product['new_'.$i] = array(
                    'bn' => $row['bn'],
                    'barcode' => $row['barcode'],
                    'price' => $row['price']?$row['price']:null,
                    'mktprice' => $row['mktprice']?$row['mktprice']:null,
                    'weight' => $row['weight']?$row['weight']:null,
                    'unit' => $row['unit'],
                    'maketable' => trim($row['maketable'])=="是" ?'true' :'false',
                    'is_default' => $i==0 ? 'true' :'false',
                );
                $product_spec = array();
                $spec = explode("," , str_replace("，" ,',' ,$row['spec']));
                if(is_array($spec)){
                    foreach($spec as $kk =>$vv){
                        $tv = explode("=" ,$vv);
                        //计算总规格
                        if($tv[0] && $tv[1]){
                            if(!isset($spec_desc[$tv[0]])){
                                $spec_desc[$tv[0]][] = $tv[1];
                            }else{
                                if(!in_array($tv[1] , $spec_desc[$tv[0]])){
                                    $spec_desc[$tv[0]][] = $tv[1];
                                }
                            }
                            //当前商品规格
                            $product_spec[] = $tv[1];
                        }
                    }
                }
                if(!empty($product_spec)){
                    $product['new_'.$i]['spec_info'] = implode("/" , $product_spec);
                    $product['new_'.$i]['spec_desc'] = implode(":::" , $product_spec);
                }
                $i++;
            }
        }
        if(empty($product)){
            throw new Exception('该商品下没有合法的货品数据');
        }
        return array('product' => $product ,'spec_desc' => $spec_desc);
    }

    //重新组装 spec_desc
    //参数格式 array(array('颜色' =>array('xxx' ,'xxx')) ,array('尺码' => array('xxx' ,'xxx')))...
    private function _get_spec_desc($spec_desc)
    {
        $result = array();
        $i = 0;
        foreach ($spec_desc as $k => $v) {
            $t_value[$i] = $k;
            $v_value[$i] = implode(",", $v);
            $i++;
        }
        if($t_value && $v_value ){
            $result['t'] = $t_value;
            $result['v'] = $v_value;
        }
        return $result;
    }

    //图片处理
    private function _get_images($goods){
        $oImage = app::get('image') ->model('image');
        $return = array();
        if($goods['image']){
            $images = explode("&&" ,$goods['image']);
            foreach($images as $key =>$url){
                if( substr($url,0,4 ) == 'http' ){
                    $image = $url;
                }else{
                    $image = ROOT_DIR.'/'.$url;
                }
                $imageId = $oImage ->gen_id();
                $imageId = $oImage->store($image,$imageId,null);

                //商品批量上传图片大中小的处理
                $oImage->rebuild($imageId,array('L','M','S'));
                $return['images'][] = array(
                    'target_type'=>'goods',
                    'image_id'=>$imageId
                );
                if( $key== 0 ){
                    $return['image_default_id'] = $imageId;
                }
            }
        }
        return $return;
    }
}
