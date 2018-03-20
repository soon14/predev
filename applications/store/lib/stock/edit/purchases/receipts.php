<?php

class store_stock_edit_purchases_receipts
{

    /**
     * 输入的进货单数据
     *
     * @var array
     */
    private $input_purchases_receipts_data = [];
    /**
     * 可以保存到数据库的进货单数据
     *
     * @var array
     */
    private $purchases_receipts_data = [];
    /**
     * 进货单细单数据
     *
     * @var array
     */
    private $purchases_receipts_items = [];
    /**
     * 错误信息
     *
     * @var string
     */
    private $msg = '';

    public function __construct() {
        $this->model_purchases_receipts = app::get('store')->model('purchases_receipts');
        $this->model_purchases_receipts_item = app::get('store')->model('purchases_receipts_item');

        $this->obj_edit_stock = vmc::singleton('store_stock_edit_stock');
    }

    /**
     * 添加进货单,并且根据进货单修改店铺商品库存
     *
     * @param array $input_purchases_receipts_data 用户输入的进货单数据
     *
     * @return bool
     */
    public function add_purchases_receipts($input_purchases_receipts_data)
    {
        $this->input_purchases_receipts_data = $input_purchases_receipts_data;

        //检查进货单数据
        $checkResult = $this->checkinput_purchases_receipts_data();
        if($checkResult === false){

            return false;
        }

        //构造进货单数据
        $this->pre_purchases_receipts_data();

        //保存进货单数据
        $insertResult = $this->model_purchases_receipts->insert($this->purchases_receipts_data);
        if(is_numeric($insertResult) === false || $insertResult <= 0){
            $this->msg = '保存进货单数据失败';

            return false;
        }

        //构造进货细单数据
        $this->pre_purchases_receipts_item_data();

        //保存进货细单数据
        $insert_purchases_receipts_item_result = $this->insert_purchases_receipts_items();
        if($insert_purchases_receipts_item_result === false){

            return false;
        }

        //根据进货单修改店铺商品库存
        $plus_store_stock_result = $this->obj_edit_stock->plus_stock($this->purchases_receipts_data);
        if($plus_store_stock_result === false){
            $this->msg = $this->obj_edit_stock->get_msg();

            return false;
        }

        return true;
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function get_msg(){

        return $this->msg;
    }

    /**
     * 保存进货单细单数据
     *
     * @return bool
     */
    private function insert_purchases_receipts_items(){

        foreach($this->purchases_receipts_items as $purchases_receipts_item){
            $insert_purchases_receipts_item_result = $this->model_purchases_receipts_item->insert($purchases_receipts_item);
            if(is_numeric($insert_purchases_receipts_item_result) === false || $insert_purchases_receipts_item_result <= 0){
                $this->msg = '保存进货单细单数据失败';

                return false;
            }
        }

        return true;
    }

    /**
     * 构造进货单数据
     */
    private function pre_purchases_receipts_data(){
        $this->purchases_receipts_data = [
            'purchases_receipts_bn' => $this->model_purchases_receipts->get_unique_purchases_receipts_bn(),
            'store_id' => $this->input_purchases_receipts_data['store_id'],
            'purchases_sku_num' => $this->get_purchases_receipts_sku_num(),
            'purchases_total_num' => $this->get_purchases_receipts_goods_num(),
            'purchases_total_money' => $this->get_purchases_receipts_goods_total_money(),
            'purchases_date' => time(),
            'purchaser_name' => $this->input_purchases_receipts_data['purchaser_name'],
            'purchaser_id' => $this->input_purchases_receipts_data['purchaser_id'],
            'purchases_receipts_remark' => $this->input_purchases_receipts_data['purchases_receipts_remark']
        ];
    }

    /**
     * 构造进货细单数据
     */
    private function pre_purchases_receipts_item_data(){
        $temp_purchases_receipts_item_data = [];
        foreach($this->purchases_receipts_items as $purchases_receipts_item){
            $temp_purchases_receipts_item_data[] = [
                'purchases_receipts_bn' => $this->purchases_receipts_data['purchases_receipts_bn'],
                'goods_id' => $purchases_receipts_item['goods_id'],
                'product_id' => $purchases_receipts_item['product_id'],
                'goods_name' => $purchases_receipts_item['goods_name'],
                'goods_spec' => $purchases_receipts_item['goods_spec'],
                'goods_bn' => $purchases_receipts_item['goods_bn'],
                'goods_barcode' => $purchases_receipts_item['goods_barcode'],
                'goods_num' => $purchases_receipts_item['goods_num'],
                'purchases_goods_money' => $purchases_receipts_item['purchases_goods_money'],
                'purchases_goods_total_money' => bcmul($purchases_receipts_item['purchases_goods_money'], $purchases_receipts_item['goods_num'], 3),
                'purchases_remark' => $purchases_receipts_item['purchases_remark'],
            ];
        }

        $this->purchases_receipts_items = $temp_purchases_receipts_item_data;
        $this->purchases_receipts_data['purchases_receipts_items'] = $this->purchases_receipts_items;
    }

    /**
     * 获取进货单sku数量
     *
     * @return int
     */
    private function get_purchases_receipts_sku_num(){
        $skuNum = count($this->purchases_receipts_items);

        return $skuNum;
    }

    /**
     * 获取进货单商品总数量
     *
     * @return int
     */
    private function get_purchases_receipts_goods_num(){
        $purchases_receipts_goods_num = 0;
        foreach($this->purchases_receipts_items as $purchases_receipts_item){
            $purchases_receipts_goods_num += $purchases_receipts_item['goods_num'];
        }

        return $purchases_receipts_goods_num;
    }

    /**
     * 获取进货单商品总金额
     *
     * @return float
     */
    private function get_purchases_receipts_goods_total_money(){
        $purchases_receipts_goods_total_money = 0;
        foreach($this->purchases_receipts_items as $purchases_receipts_item){
            $goodsMoney = bcmul($purchases_receipts_item['goods_num'], $purchases_receipts_item['purchases_goods_money'], 3);
            $purchases_receipts_goods_total_money = bcadd($purchases_receipts_goods_total_money, $goodsMoney, 3);
        }

        return $purchases_receipts_goods_total_money;
    }

    /**
     * 检查进货单数据
     *
     * @return bool
     */
    private function checkinput_purchases_receipts_data(){
        //过滤输入
        $this->input_purchases_receipts_data = utils::_filter_input($this->input_purchases_receipts_data);

        if(is_numeric($this->input_purchases_receipts_data['purchaser_id']) === false || $this->input_purchases_receipts_data['purchaser_id'] <= 0){
            $this->msg = '进货人id错误';

            return false;
        }

        if(is_numeric($this->input_purchases_receipts_data['store_id']) === false || $this->input_purchases_receipts_data['store_id'] <= 0){
            $this->msg = '店铺信息错误';

            return false;
        }

        if(empty($this->input_purchases_receipts_data['purchaser_name']) === true){
            $this->msg = '请输入进货人姓名';

            return false;
        }

        if(empty($this->input_purchases_receipts_data['purchases_receipts_remark']) === true){
            $this->msg = '请输入进货单备注';

            return false;
        }

        if(isset($this->input_purchases_receipts_data['item']) === false || is_array($this->input_purchases_receipts_data['item']) === false || count($this->input_purchases_receipts_data['item']) === 0){
            $this->msg = '没有进货商品信息';

            return false;
        }

        foreach($this->input_purchases_receipts_data['item'] as $purchases_receipts_key => $purchases_receipts_detail){
            foreach($purchases_receipts_detail as $key => $purchases_receipts_value){
                if($purchases_receipts_key === 'product_id' || $purchases_receipts_key === 'goods_id'){
                    if(is_numeric($purchases_receipts_value) === false || $purchases_receipts_value <= 0){
                        $this->msg = '商品id错误';

                        return false;
                    }
                }else if($purchases_receipts_key === 'goods_name'){
                    if(empty($purchases_receipts_value) === true){
                        $this->msg = '商品名错误';

                        return false;
                    }
                }else if($purchases_receipts_key === 'goods_bn'){
                    if(empty($purchases_receipts_value) === true){
                        $this->msg = '商品货号错误';

                        return false;
                    }
                } else if($purchases_receipts_key === 'goods_barcode'){
                    if(empty($purchases_receipts_value) === true || is_numeric($purchases_receipts_value) === false){
                        $this->msg = '商品条码错误';

                        return false;
                    }
                } else if($purchases_receipts_key === 'goods_num'){
                    if(is_numeric($purchases_receipts_value) === false || $purchases_receipts_value <= 0){
                        $this->msg = '商品数量错误';

                        return false;
                    }
                }
                $this->purchases_receipts_items[$key][$purchases_receipts_key] = $purchases_receipts_value;
            }
        }

        return true;
    }
}