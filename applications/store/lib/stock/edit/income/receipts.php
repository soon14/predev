<?php

class store_stock_edit_income_receipts
{
    /**
     * 输入的损益单数据
     *
     * @var array
     */
    private $input_income_receipts_data = [];
    /**
     * 可以保存到数据的损益单数据
     *
     * @var array
     */
    private $income_receipts_data = [];
    /**
     * 损益单细单数据
     *
     * @var array
     */
    private $income_receipts_items = [];
    /**
     * 错误信息
     *
     * @var string
     */
    private $msg = '';

    public function __construct() {
        $this->model_income_receipts = app::get('store')->model('income_receipts');
        $this->model_income_receipts_item = app::get('store')->model('income_receipts_item');

        $this->obj_edit_stock = vmc::singleton('store_stock_edit_stock');
    }

    public function add_income_receipts($input_income_receipts_data)
    {
        $this->input_income_receipts_data = $input_income_receipts_data;

        //检查损益单数据
        $checkResult = $this->checkinput_income_receipts_data();
        if($checkResult === false){

            return false;
        }

        //构造损益单数据
        $this->pre_income_receipts_data();

        //保存损益单数据
        $insertResult = $this->model_income_receipts->insert($this->income_receipts_data);
        if(is_numeric($insertResult) === false || $insertResult <= 0){
            $this->msg = '保存损益单数据失败';

            return false;
        }

        //构造损益单细单数据
        $this->pre_income_receipts_item_data();

        //保存损益单细单数据
        $insert_income_receipts_item_result = $this->insert_income_receipts_items();
        if($insert_income_receipts_item_result === false){

            return false;
        }

        //根据损益单修改店铺商品库存
        $minus_store_stock_result = $this->obj_edit_stock->minus_stock($this->income_receipts_data);
        if($minus_store_stock_result === false){
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
     * 保存损益单细单数据
     *
     * @return bool
     */
    private function insert_income_receipts_items(){

        foreach($this->income_receipts_items as $income_receipts_item){
            $insert_income_receipts_item_result = $this->model_income_receipts_item->insert($income_receipts_item);
            if(is_numeric($insert_income_receipts_item_result) === false || $insert_income_receipts_item_result <= 0){
                $this->msg = '保存损益单细单数据失败';

                return false;
            }
        }

        return true;
    }

    /**
     * 构造损益单数据
     */
    private function pre_income_receipts_data(){
        $this->income_receipts_data = [
            'income_receipts_bn' => $this->model_income_receipts->get_unique_income_receipts_bn(),
            'store_id' => $this->input_income_receipts_data['store_id'],
            'check_profit_num' => $this->get_check_profit_num(),
            'check_loss_num' => $this->get_check_loss_num(),
            'check_profit_money' => $this->get_check_profit_total_money(),
            'check_loss_money' => $this->get_check_loss_total_money(),
            'income_date' => time(),
            'incomer_name' => $this->input_income_receipts_data['incomer_name'],
            'incomer_id' => $this->input_income_receipts_data['incomer_id'],
            'income_receipts_remark' => $this->input_income_receipts_data['income_receipts_remark']
        ];
    }

    /**
     * 构造损益单细单数据
     */
    private function pre_income_receipts_item_data(){
        $temp_income_receipts_item_data = [];
        foreach($this->income_receipts_items as $income_receipts_item){
            $temp_income_receipts_item_data[] = [
                'income_receipts_bn' => $this->income_receipts_data['income_receipts_bn'],
                'goods_id' => $income_receipts_item['goods_id'],
                'product_id' => $income_receipts_item['product_id'],
                'goods_name' => $income_receipts_item['goods_name'],
                'goods_spec' => $income_receipts_item['goods_spec'],
                'goods_bn' => $income_receipts_item['goods_bn'],
                'goods_barcode' => $income_receipts_item['goods_barcode'],
                'goods_old_store' => $income_receipts_item['goods_old_store'],
                'goods_real_store' => $income_receipts_item['goods_real_store'],
                'goods_check_profit_num' => $income_receipts_item['goods_check_profit_num'],
                'goods_check_loss_num' => $income_receipts_item['goods_check_loss_num'],
                'income_goods_money' => $income_receipts_item['income_goods_money'],
                'goods_check_profit_money' => $income_receipts_item['goods_check_profit_money'],//bcmul($income_receipts_item['income_goods_money'], $income_receipts_item['goods_check_profit_num'], 3),
                'goods_check_loss_money' => $income_receipts_item['goods_check_loss_money'],//bcmul($income_receipts_item['income_goods_money'], $income_receipts_item['goods_check_loss_money'], 3),
                'income_remark' => $income_receipts_item['income_remark'],
            ];
        }

        $this->income_receipts_items = $temp_income_receipts_item_data;
        $this->income_receipts_data['income_receipts_items'] = $this->income_receipts_items;
    }

    /**
     * 获取损益单盘盈商品总数量
     *
     * @return int
     */
    private function get_check_profit_num(){
        $check_profit_num = 0;
        foreach($this->income_receipts_items as $income_receipts_item){
            $check_profit_num += $income_receipts_item['goods_check_profit_num'];
        }

        return $check_profit_num;
    }

    /**
     * 获取损益单盘损商品总数量
     *
     * @return int
     */
    private function get_check_loss_num(){
        $check_lossNum = 0;
        foreach($this->income_receipts_items as $income_receipts_item){
            $check_lossNum += $income_receipts_item['goods_check_loss_num'];
        }

        return $check_lossNum;
    }

    /**
     * 获取盘盈商品总金额
     *
     * @return float
     */
    private function get_check_profit_total_money(){
        $check_profit_total_money = 0;
        foreach($this->income_receipts_items as $income_receipts_item){
            $check_profit_total_money = bcadd($check_profit_total_money, $income_receipts_item['goods_check_profit_money'], 3);
        }

        return $check_profit_total_money;
    }

    /**
     * 获取盘损商品总金额
     *
     * @return float
     */
    private function get_check_loss_total_money(){
        $check_loss_total_money = 0;
        foreach($this->income_receipts_items as $income_receipts_item){
            $check_loss_total_money = bcadd($check_loss_total_money, $income_receipts_item['goods_check_loss_money'], 3);
        }

        return $check_loss_total_money;
    }

    /**
     * 检查损益单数据
     *
     * @return bool
     */
    private function checkinput_income_receipts_data(){
        //过滤输入
        $this->input_income_receipts_data = utils::_filter_input($this->input_income_receipts_data);

        if(is_numeric($this->input_income_receipts_data['incomer_id']) === false || $this->input_income_receipts_data['incomer_id'] <= 0){
            $this->msg = '操作员id错误';

            return false;
        }

        if(is_numeric($this->input_income_receipts_data['store_id']) === false || $this->input_income_receipts_data['store_id'] <= 0){
            $this->msg = '店铺信息错误';

            return false;
        }

        if(empty($this->input_income_receipts_data['incomer_name']) === true){
            $this->msg = '请输入盘点人姓名';

            return false;
        }

        if(empty($this->input_income_receipts_data['income_receipts_remark']) === true){
            $this->msg = '请输入损益单备注';

            return false;
        }

        if(isset($this->input_income_receipts_data['item']) === false || is_array($this->input_income_receipts_data['item']) === false || count($this->input_income_receipts_data['item']) === 0){
            $this->msg = '没有损益商品信息';

            return false;
        }

        foreach($this->input_income_receipts_data['item'] as $income_receipts_key => $income_receipts_detail){
            foreach($income_receipts_detail as $key => $income_receiptsValue){
                if($income_receipts_key === 'product_id' || $income_receipts_key === 'goods_id'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue <= 0){
                        $this->msg = '商品id错误';

                        return false;
                    }
                }else if($income_receipts_key === 'goods_name'){
                    if(empty($income_receiptsValue) === true){
                        $this->msg = '商品名错误';

                        return false;
                    }
                }else if($income_receipts_key === 'goods_spec'){
                    if(empty($income_receiptsValue) === true){
                        $this->msg = '商品规格错误';

                        return false;
                    }
                }else if($income_receipts_key === 'goods_bn'){
                    if(empty($income_receiptsValue) === true){
                        $this->msg = '商品货号错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_barcode'){
                    if(empty($income_receiptsValue) === true || is_numeric($income_receiptsValue) === false){
                        $this->msg = '商品条码错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_old_store'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue == 0){
                        $this->msg = '商品库存错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_real_store'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue == 0){
                        $this->msg = '商品实际库存错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_check_profit_num'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue < 0){
                        $this->msg = '商品盘盈件数错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_check_loss_num'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue < 0){
                        $this->msg = '商品盘损件数错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_check_profit_money'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue < 0){
                        $this->msg = '商品盘盈金额错误';

                        return false;
                    }
                } else if($income_receipts_key === 'goods_check_loss_money'){
                    if(is_numeric($income_receiptsValue) === false || $income_receiptsValue < 0){
                        $this->msg = '商品盘损金额错误';

                        return false;
                    }
                }
                $this->income_receipts_items[$key][$income_receipts_key] = $income_receiptsValue;
            }
        }

        return true;
    }
}