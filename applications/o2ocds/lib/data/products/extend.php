<?php

class o2ocds_data_products_extend
{

    public function get_title($title = array())
    {
        //定义表头
        return array(
            'product_id' => '货品id(product_id)',
            'goods_id' => '商品id(goods_id)',
            'sku_bn' => 'SKU货号(sku_bn)',
            'title' => '商品名称(title)',
            'o2ocds_value' => '分佣比例或金额',
        );
    }


    public function get_extra_title(&$row){
        unset($row['o2ocds_value']);
        return array(
            'o2ocds_enterprise' => '企业分佣',
            'o2ocds_store' => '店铺分佣',
        );
    }
    /**
     * 导出订单数据，修改数据库已有数据
     * @return array $row 修改过后的数据
     */
    public function get_content_row($row){
        $row['o2ocds_enterprise'] = $row['o2ocds_value']['enterprise'];
        $row['o2ocds_store'] = $row['o2ocds_value']['store'];
        unset($row['o2ocds_value']);
        $data[0] = $row;
        return $data;
    }//end function


    /*-----------------------以下为导入函数-----------------------*/
    public function get_import_title() {
        return array(
            'product_id' => '货品id(product_id)',
            'goods_id' => '商品id(goods_id)',
            'sku_bn' => 'SKU货号(sku_bn)',
            'title' => '商品名称(title)',
            'o2ocds_enterprise' => '企业分佣',
            'o2ocds_store' => '店铺分佣',
        );
    }
    public function check_continue(&$contents, $line)
    {
        if (count($contents) == 1) {
            return true;
        } else {
            array_pop($contents);

            return false;
        }
    }

    /**
     * 导入之前数据处理
     */
    public function pre_import_data($contents, $line)
    {
        $oneline = current($contents);

        $title = $this->get_import_title();

        foreach ($title as $group_val => $title_row) {
            //如果第一条记录为标题
            if ($title_row == current($oneline)) {
                //将导入的标题对应到定义的字段中
                $title_row_flip = array_flip($title);
                $this->current_title = array();
                foreach ($oneline as $key => $label) {
                    $col = $title_row_flip[$label];
                    $this->current_title[] = $col;
                }
                //end
                array_shift($contents);

            }
        }

        if (!$this->current_title) {
            return false;
        }
        foreach ((array)$contents[$line] as $key => $value) {
            $col = $this->current_title[$key];
            if ($col) {
                $contents[$line][$col] = $value;
            }
            unset($contents[$line][$key]);
        }

        return $contents;
    }

    /**
     *将导入的数据转换为sdf.
     *
     * @param array $contents 导入的一条库存数据
     * @param string $msg 传引用传出错误信息
     *
     * @return mixed
     */
    public function dataToSdf($contents, &$msg)
    {
        $content = current($contents);
        $o2ocds_value = array(
            'enterprise' => $content['o2ocds_enterprise'],
            'store' => $content['o2ocds_store'],
        );
        $content['o2ocds_value'] = $o2ocds_value;
        return $content;
    }



}
