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


class ectools_math
{
    /**
     * @var int 运算精度
     */
    public $operationDecimals = 1;   //运算精度
    /**
     * @var int 运算进位方式
     */
    public $operationCarryset = 1;   //运算进位方式
    /**
     * @var int 运算显示精度
     */
    public $goodsShowDecimals = 1;   //运算显示精度
    /**
     * @var int 运算显示进位方式
     */
    public $goodsShowCarryset = 1;     //运算显示进位方式
    /**
     * @var string 运算function
     */
    public $operationFunc = null;    //运算function
    /**
     * @var string 显示function
     */
    public $displayFunc = null;   //显示function

    /**
     * 构造方法.
     *
     * @param null
     */
    public function __construct()
    {
        $this->operationDecimals = app::get('ectools')->getConf('site_decimal_digit_count');              //运算精度
        $this->operationCarryset = app::get('ectools')->getConf('site_decimal_type_count');              //运算进位方式
        $this->goodsShowDecimals = app::get('ectools')->getConf('site_decimal_digit_display');            //运算显示精度
        $this->goodsShowCarryset = app::get('ectools')->getConf('site_decimal_type_display');            //运算显示进位方式

        switch ($this->operationCarryset) {
            case '1':          //四舍五入
                $this->operationFunc = 'round';
                break;
            case '2':          //向上取整
                $this->operationFunc = 'ceil';
                break;
            case '3':          //向下取整
                $this->operationFunc = 'floor';
                break;
            default:          //四舍五入
                $this->operationFunc = 'round';
                break;
        }

        switch ($this->goodsShowCarryset) {
            case '1':          //四舍五入
                $this->displayFunc = 'round';
                break;
            case '2':          //向上取整
                $this->displayFunc = 'ceil';
                break;
            case '3':          //向下取整
                $this->displayFunc = 'floor';
                break;
            default:          //四舍五入
                $this->displayFunc = 'round';
                break;
        }
    }

    /**
     * 相加运算.
     *
     * @params array - or string
     *
     * @return 运算结果
     */
    public function number_plus($numbers = '')
    {
        // 异常处理
        if ($numbers === null) {
            trigger_error('参数不能为空！', E_USER_ERROR);
            exit;
        }
        // 开始运算
        if (!is_array($numbers)) {
            return $this->getOperationNumber($numbers, true);
        }

        $rs = 0;
        foreach ($numbers as $n) {
            $n = trim($n);
            if (function_exists('bcadd')) {
                $rs = bcadd(strval($rs), strval($n), 3);
            } else {
                if (is_null($rs) || $rs === '' || $rs === 0) {
                    $rs = '0';
                }
                if (is_null($n) || $n === '' || $n === 0) {
                    $n = '0';
                }

                $sql = 'SELECT '.strval($rs).' + '.strval($n).' AS nums';

                try {
                    $row = vmc::database()->select($sql);
                    $rs = $row[0]['nums'];
                } catch (Exception $e) {
                    trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                    exit;
                }
            }
        }

        return $this->getOperationNumber($rs, true);
    }

    /**
     * 相减运算.
     *
     * @params 需要运算的数据，数组、数值等
     *
     * @return 运算结果 string
     */
    public function number_minus($numbers = '')
    {
        // 异常处理
        if ($numbers === null) {
            trigger_error('参数不能为空！', E_USER_ERROR);
            exit;
        }

        if (!is_array($numbers)) {
            return $this->getOperationNumber($numbers, true);
        }

        $rs = strval($numbers[0]);
        for ($i = 1; $i < count($numbers); $i++) {
            $numbers[$i] = trim($numbers[$i]);
            if (function_exists('bcsub')) {
                $rs = bcsub(strval($rs), strval($numbers[$i]), 3);
            } else {
                if (is_null($rs) || $rs === '' || $rs === 0) {
                    $rs = '0';
                }
                if (is_null($numbers[$i]) || $numbers[$i] === '' || $numbers[$i] === 0) {
                    $numbers[$i] = '0';
                }
                $sql = 'SELECT '.strval($rs).' - '.strval($numbers[$i]).' AS nums';

                try {
                    $row = vmc::database()->select($sql);
                    $rs = $row[0]['nums'];
                } catch (Exception $e) {
                    trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                    exit;
                }
            }
        }

        return $this->getOperationNumber($rs, true);
    }

    /**
     * 乘运算.
     *
     * @params 需要运算的数据，数组、数值等
     *
     * @return 运算结果 string
     */
    public function number_multiple($numbers = '')
    {
        $rs = $this->mutiple_none_format($numbers);

        return $this->getOperationNumber($rs, true);
    }

    /**
     * 除运算.
     *
     * @params 需要运算的数据，数组、数值等
     *
     * @return 运算结果 string
     */
    public function number_div($numbers = '')
    {
        // 异常处理
        if ($numbers === '' || $numbers === 0 || $numbers === null) {
            trigger_error('参数异常！', E_USER_ERROR);
            exit;
        }

        if (!is_array($numbers)) {
            return $this->getOperationNumber($numbers, true);
        }

        $rs = $numbers[0];
        for ($i = 1; $i < count($numbers); $i++) {
            $numbers[$i] = trim($numbers[$i]);
            if (function_exists('bcdiv')) {
                $rs = bcdiv(strval($rs), strval($numbers[$i]), 3);
            } else {
                if (is_null($rs) || $rs === '' || $rs === 0) {
                    $rs = '0';
                }

                $sql = 'SELECT '.strval($rs).' / '.strval($numbers[$i]).' AS nums';

                try {
                    $row = vmc::database()->select($sql);
                    $rs = $row[0]['nums'];
                } catch (Exception $e) {
                    trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                    exit;
                }
            }
        }

        return $this->getOperationNumber($rs, true);
    }

    /**
     * 取得系统设定的商品价格进位方式后的数值 - 向下取整.
     *
     * @params 需要处理的数据，string
     * @params 需要的精度,string
     *
     * @return 进位后商品价格 string
     */
    public function get($number = '', $decimals)
    {
        if ($number === null) {
            $money = intval($money);
        }

        $result = call_user_func_array($this->displayFunc, array($number * pow(10, $decimals)));
        if (function_exists('bcdiv')) {
            $result = bcdiv(strval($result), strval(pow(10, $decimals)), $this->operationDecimals);
        } else {
            if (is_null($result) || $result === '' || $result === 0) {
                $result = '0';
            }

            $sql = 'SELECT '.strval($result).' / '.strval(pow(10, $decimals)).' AS nums';

            try {
                $row = vmc::database()->select($sql);
                $result = $row[0]['nums'];
            } catch (Exception $e) {
                trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                exit;
            }
        }

        return $result;
    }

    /**
     * getOperationNumber 取得系统设定的运算价格进位方式后的数值.
     *
     * @params 需要精确的位数
     * @params boolean is_display 是显示还是不是显示
     *
     * @return 进位后 运算 价格
     */
    public function getOperationNumber($number = '', $is_count = true)
    {
        if ($number === null) {
            $number = intval($number);
        }

        if ($is_count) {
            $result = call_user_func_array($this->operationFunc, array($this->mutiple_none_format(array($number, pow(10, $this->operationDecimals)))));
            if (function_exists('bcdiv')) {
                $result = bcdiv(strval($result), strval(pow(10, $this->operationDecimals)), $this->operationDecimals);
            } else {
                if (is_null($result) || $result === '' || $result === 0) {
                    $result = '0';
                }

                $sql = 'SELECT '.strval($result).' / '.strval(pow(10, $this->operationDecimals)).' AS nums';

                try {
                    $row = vmc::database()->select($sql);
                    $result = $row[0]['nums'];
                } catch (Exception $e) {
                    trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                    exit;
                }

                if ($this->operationFunc != 'round') {
                    $result = number_format(call_user_func_array($this->operationFunc, array($result)), $this->operationDecimals, '.', '');
                } else {
                    $result = number_format(call_user_func_array($this->operationFunc, array($result, $this->operationDecimals)), $this->operationDecimals, '.', '');
                }
            }

            return $result;
        } else {
            $result = call_user_func_array($this->displayFunc, array($this->mutiple_none_format(array($number, pow(10, $this->goodsShowDecimals)))));
            if (function_exists('bcdiv')) {
                $result = bcdiv(strval($result), strval(pow(10, $this->goodsShowDecimals)), $this->goodsShowDecimals);
            } else {
                if (is_null($result) || $result === '' || $result === 0) {
                    $result = '0';
                }

                $sql = 'SELECT '.strval($result).' / '.strval(pow(10, $this->goodsShowDecimals)).' AS nums';

                try {
                    $row = vmc::database()->select($sql);
                    $result = $row[0]['nums'];
                } catch (Exception $e) {
                    trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                    exit;
                }

                if ($this->operationFunc != 'round') {
                    $result = number_format(call_user_func_array($this->operationFunc, array($result)), $this->goodsShowDecimals, '.', '');
                } else {
                    $result = number_format(call_user_func_array($this->operationFunc, array($result, $this->goodsShowDecimals)), $this->goodsShowDecimals, '.', '');
                }
            }

            return $result;
        }
    }

    public function formatNumber($number = '', $display_decimals = '2', $operation_carryset = '0')
    {
        switch ($operation_carryset) {
            case '0':          //四舍五入
                $operationFunc = 'round';
                break;
            case '1':          //向上取整
                $operationFunc = 'ceil';
                break;
            case '2':          //向下取整
                $operationFunc = 'floor';
                break;
            default:          //四舍五入
                $operationFunc = 'round';
                break;
        }

        $result = call_user_func_array($operationFunc, array($this->mutiple_none_format(array($number, pow(10, $display_decimals)))));
        if (function_exists('bcdiv')) {
            $result = bcdiv(strval($result), strval(pow(10, $display_decimals)), $display_decimals);
        } else {
            if (is_null($result) || $result === '' || $result === 0) {
                $result = '0';
            }

            $sql = 'SELECT '.strval($result).' / '.strval(pow(10, $display_decimals)).' AS nums';

            try {
                $row = vmc::database()->select($sql);
                $result = $row[0]['nums'];
            } catch (Exception $e) {
                trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                exit;
            }

            if ($this->operationFunc != 'round') {
                $result = number_format(call_user_func_array($this->operationFunc, array($result)), $display_decimals, '.', '');
            } else {
                $result = number_format(call_user_func_array($this->operationFunc, array($result, $display_decimals)), $display_decimals, '.', '');
            }
        }

        return $result;
    }

    private function mutiple_none_format($numbers = '')
    {
        // 异常处理
        if ($numbers === null) {
            trigger_error('参数不能为空！', E_USER_ERROR);
            exit;
        }

        if (!is_array($numbers)) {
            return $numbers;
        }

        $rs = 1;
        foreach ($numbers as $n) {
            $n = trim($n);
            if (function_exists('bcmul')) {
                $rs = bcmul(strval($rs), strval($n), 3);
            } else {
                if (is_null($rs) || $rs === '' || $rs === 0) {
                    $rs = '0';
                }
                if (is_null($n) || $n === '' || $n === 0) {
                    $n = '0';
                }

                $sql = 'SELECT '.strval($rs).' * '.strval($n).' AS nums';

                try {
                    $row = vmc::database()->select($sql);
                    $rs = $row[0]['nums'];
                } catch (Exception $e) {
                    trigger_error($sql.':'.$e->getMessage(), E_USER_WARNING);
                    exit;
                }
            }
        }

        return $rs;
    }
}
