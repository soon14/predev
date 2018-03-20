<?php

/**
 * 店铺增减库存
 *
 * Class store_stock_editkucun
 */
class store_stock_edit_stock
{
    /**
     * 商品库存信息
     *
     * @var array
     */
    //private $goodsStockInfo = [];
    /**
     * 错误信息
     *
     * @var string
     */
    private $msg = '';

    public function __construct()
    {
        $this->db = vmc::database();
    }

    /**
     * 根据盘点损益单减去库存
     *
     * @param array $income_receipts_data 损益单数据
     *
     * @return bool
     */
    public function minus_stock($income_receipts_data)
    {
        //根据损益单细单循环修改商品库存
        foreach($income_receipts_data['income_receipts_items'] as $income_receipts_item){
            $updateStockNum = $this->getUpdateStockNum($income_receipts_item);
            $minusResult = $this->update_stock($income_receipts_item['goods_bn'], $updateStockNum);
            if($minusResult === false){

                return false;
            }
        }

        //记录日志
        $this->writeLog();

        return true;

        /**
         * $income_receipts_item数据如下:
            'income_receipts_bn' => $this->income_receiptsData['income_receipts_bn'],
            'goods_id' => $income_receipts_item['goods_id'],
            'product_id' => $income_receipts_item['product_id'],
            'goods_name' => $income_receipts_item['goods_name'],
            'goods_spec' => $income_receipts_item['goods_guige'],
            'goods_bn' => $income_receipts_item['goods_bn'],
            'goods_barcode' => $income_receipts_item['goods_tiaoma'],
            'goods_old_store' => $income_receipts_item['goods_old_store'],
            'goods_real_store' => $income_receipts_item['goods_real_store'],
            'goods_check_profit_num' => $income_receipts_item['goods_check_profit_num'],
            'goods_check_loss_num' => $income_receipts_item['goods_check_loss_num'],
            'income_goods_money' => $income_receipts_item['income_goods_money'],
            'goods_check_profit_money' => $income_receipts_item['goods_check_profit_money'],//bcmul($income_receipts_item['income_goods_money'], $income_receipts_item['goods_check_profit_num'], 3),
            'goods_check_loss_money' => $income_receipts_item['goods_check_loss_money'],//bcmul($income_receipts_item['income_goods_money'], $income_receipts_item['goods_check_loss_money'], 3),
            'income_remark' => $income_receipts_item['income_remark'],
         */
    }

    /**
     * 根据进货单增加库存
     *
     * @param array $purchases_receipts_data 进货单数据
     *
     * @return bool
     */
    public function plus_stock($purchases_receipts_data)
    {
        //循环修改库存
        foreach ($purchases_receipts_data['purchases_receipts_items'] as $purchases_receipts_item) {
            $plusResult = $this->update_stock($purchases_receipts_item['goods_bn'], $purchases_receipts_item['goods_num']);
            if($plusResult === false){

                return false;
            }
        }

        //记录日志
        $this->writeLog();

        return true;
    }

    /**
     * 更新库存
     *
     * @param string $skuBn 商品sku货号
     * @param int $updateStockNum 库存修改量
     *
     * @return bool
     */
    private function update_stock($skuBn, $updateStockNum){
        $update_stockSql = "UPDATE
                      vmc_b2c_stock
                  SET
                      quantity = quantity + ({$updateStockNum})
                  WHERE
                      sku_bn = '{$skuBn}'";

        $updateResult = $this->db->exec($update_stockSql);
        if(!$updateResult){
            $this->msg = "{$skuBn} 库存更新失败";

            return false;
        }

        return true;
    }

    /**
     * 根据盘点损益单减去库存时,计算最终应该减去多少库存
     *
     * @param array $income_receipts_item 盘点损益单细单数据
     *
     * @return int
     */
    private function getUpdateStockNum($income_receipts_item){
        $updateStockNum = $income_receipts_item['goods_check_profit_num'] - $income_receipts_item['goods_check_loss_num'];

        return $updateStockNum;
    }

    /**
     * 返回错误信息
     *
     * @return string
     */
    public function get_msg(){

        return $this->msg;
    }

    /**
     * 写入系统日志
     */
    private function writeLog(){

    }
}