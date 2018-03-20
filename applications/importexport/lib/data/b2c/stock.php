<?php

class importexport_data_b2c_stock
{

    public function get_import_title()
    {
        return array(
            'sku_bn' => '货号(sku)',
            'quantity' => '库存(quantity)',
            'freez_quantity' => '冻结库存(freez_quantity)'
        );
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
        $stock = current($contents);
        $stockData = [];

        try {
            $this->_check_column($stock);
            //构造stock表基础数据
            $stockData['sku_bn'] = $stock['sku_bn'];
            $stockData['quantity'] = $stock['quantity'];
            $stockData['freez_quantity'] = $stock['freez_quantity'];
            $stockData['stock_id'] = $this->get_stock_id($stock);
        } catch (Exception $e) {
            $msg = $e->getMessage();
        }
        return $stockData;
    }

    /**
     * 库存信息校验
     *
     * @param array $stock 库存信息
     *
     * @throws Exception
     */
    private function _check_column($stock)
    {
        if (!$stock['sku_bn']) {
            throw new Exception(app::get('importexport')->_('货号不能为空!'));
        }
        if (!app::get('b2c')->model('stock')->getRow('*', array('sku_bn' => trim($stock['sku_bn'])))) {

            throw new Exception(app::get('importexport')->_($stock['sku_bn'] . '货号不存在'));
        }
    }

    //库存ID
    private function get_stock_id($stock)
    {
        if ($stock['sku_bn']) {
            $stock_id = app::get('b2c')->model('stock')->getRow('stock_id', array('sku_bn' => trim($stock['sku_bn'])));
            if ($stock_id) {
                return $stock_id['stock_id'];
            }
        }
    }


}
