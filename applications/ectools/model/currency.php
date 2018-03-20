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




class ectools_mdl_currency extends dbeav_model {

    var $idColumn = 'cur_code';
    var $textColumn = 'cur_name';

    function __construct(&$app)
    {
        parent::__construct($app);
        $this->oMath = vmc::singleton('ectools_math');
    }//End Function

    /**
    * getSysCur
    *
    * @param  boolean $pass_exist false // 是否过滤掉当前已入库的货币(unqiue index)
    * @param  string  $pass_except ''   // 过滤时 要保留当前的(修改货币设置时使用)
	* @param  boolean $is_sign // 是否还有货币符号
    * @return array
    * @access public
    */
    public function getSysCur($pass_exist = false,$pass_except = '',$is_sign=true)
	{
        $return = array();
        $aFilter = array();
        if($pass_exist) {
            $aFilter = $this->curAll(); //获取已入库的所有货币

            $aFilter = $this->array_change_key($aFilter,'cur_code');

            if(!empty($pass_except) && array_search($pass_except, $aFilter) !== false)
			{
				$key = array_search($pass_except, $aFilter);
				unset($aFilter[$key]);
			}
        }

		//使用文件方式 读取一定格式文件
        foreach (file($this->app->app_dir.'/currency.txt') as $row)
		{
            list($code,$sign,$cnname,$enname) = explode("\t",trim($row));
            if(in_array($code,$aFilter)) continue;
			if ($is_sign)
				$return[$code] = $sign.' '.$cnname;
			else
				$return[$code] = $cnname;
        }

        return $return;
    }



	/**
	 * This is method array_change_key
	 * 使用二维数据内部key代替外部key
	 * @param mixed $aArray This is a description
	 * @param mixed $aKey This is a description
	 * @return mixed This is the return value description
	 *
	 */
	function array_change_key($aArray=array(),$aKey=''){
		$tmpArray= array();
		if( is_array($aArray)){
			foreach($aArray as $subKey => $subVal){
				$tmpArray[$subKey]=@$subVal[$aKey];
			}
		}
		return $tmpArray;
	}

	/**
	 * 获得数据库中的所有数据
	 * @params null
	 * @params array 货币数组
	 */
    public function curAll(){
        $arr_curs = $this->getList('*', '', 0, -1);
		foreach ($arr_curs as $key=>$arr)
		{
			$arr_curs[$key]['cur_name'] = app::get('ectools')->_($arr_curs[$key]['cur_name']);
		}
		return $arr_curs;
    }

	/**
	 * 得到指定currency code 对应的货币信息
	 * @params string currency code
	 * @return array 货币信息数组
	 */
    public function getcur($curCode){
        if (!$this->_in_cur || (isset($this->_in_cur['cur_code']) && $this->_in_cur['cur_code'] != $curCode))
		{
            $filter = array(
                'cur_code' => $curCode
			);

            $aCur = $this->dump($filter, '*');
            if($aCur['cur_code']){
                return $this->_in_cur = $aCur;
            }else{
				if (!$aCur)
				{
					$this->_default_cur = $this->db->selectrow('select * FROM vmc_ectools_currency WHERE cur_default="true"');
					if (!isset($this->_default_cur) || !$this->_default_cur)
					{
						//使用文件方式 读取一定格式文件
						foreach(file($this->app->app_dir.'/currency.txt') as $row){
							list($code,$sign,$cnname,$enname) = explode("\t",trim($row));
							if(!in_array($code,$filter)) continue;
							$this->_default_cur = array(
								'cur_code' => $code,
								'cur_name' => app::get('ectools')->_($cnname),
								'cur_sign' => $sign,
								'cur_rate' => 1,
							);
							return $this->_in_cur = $this->_default_cur;
						}
					}
				}
				$this->_in_cur = $aCur;
				$arrDef = $this->getDefault();
                $this->_in_cur = $arrDef;
				return $this->_in_cur;
            }
        }
		else
		{
			return $this->_in_cur;
		}
    }

	/**
	 * 得到默认货币的信息
	 * @params null
	 * @return array 货币信息的数组
	 */
    public function getDefault(){
        if(!$this->_default_cur){
            $defCur = $this->app->getConf('site.currency.defalt_currency');
            $filter = array(
                'cur_code' => $defCur
                );
            if (!($this->_default_cur = $this->dump($filter, '*'))) {
                $this->_default_cur = $this->db->selectrow('SELECT * FROM `vmc_ectools_currency` WHERE `cur_default`=\'true\'');
				if (!isset($this->_default_cur) || !$this->_default_cur)
				{
					//使用文件方式 读取一定格式文件
					foreach(file($this->app->app_dir.'/currency.txt') as $row){
						list($code,$sign,$cnname,$enname) = explode("\t",trim($row));
						if(!in_array($code,$filter)) continue;
						$this->_default_cur = array(
							'cur_code' => $code,
							'cur_name' => app::get('ectools')->_($cnname),
							'cur_sign' => $sign,
							'cur_rate' => 1,
						);
						return $this->_default_cur;
					}
				}
            }
        }

        return $this->_default_cur;

    }

	/**
	 * 清空默认属性
	 * @params null
	 * @return null
	 */
	public function set_currency_default()
	{
		$arrCurs = $this->curAll();
		if ($arrCurs)
			$this->update(array('cur_default' => 'false'), array());
	}

    //一下方法需要调整
    public function getFormat($cur=null){
        $ret = array();
        if(!$cur) {
            $cursign = $this->getDefault();
        }else{
            $cursign = $this->getcur($cur);
        }

        $ret['sign'] = $cursign['cur_sign'];
        return $ret;
    }

    /**
     * 商店币别金额显示统一调用函数
     * @params float $money 金额
     * @params string $currency 显示的货币种类如：CNY/USD
     * @params bool $is_sign 是否不带货币符号显示
     * @params bool $is_rate 是否按汇率显示
     * @return bool
     */
    public function changer($money, $currency='', $is_sign=false, $is_rate=false)
	{
		// 异常处理
        if ($money === null)
		{
			$money = intval($money);
		}

		// 获取货币金额
        $cur_money = $this->get_cur_money($money, $currency);

        if($is_rate){
            $cur_money = $money;
        }

		// 格式化货币
        if($is_sign)
		{
            return $this->formatNumber($cur_money, false, false);
        }
		else
		{
            return $this->_in_cur['cur_sign'] . $this->formatNumber($cur_money, false);
        }
    }

	/**
     * 商店币别金额显示统一调用函数(订单)
     * @params float $money 金额
     * @params string $currency 显示的货币种类如：CNY/USD
     * @params bool $is_sign 是否不带货币符号显示
     * @params bool $is_rate 是否按汇率显示
     * @return bool
     */
    public function changer_odr($money, $currency='', $is_sign=false, $is_rate=false, $decimals='2', $operation_carryset='0')
	{
		// 异常处理
        if ($money === null)
		{
			$money = intval($money);
		}

		// 获取货币金额
        $cur_money = $this->get_cur_money($money, $currency);

        if($is_rate){
            $cur_money = $money;
        }

		$cur_money = $this->oMath->formatNumber($cur_money, $decimals, $operation_carryset);

		// 格式化货币
        if($is_sign)
		{
            return $cur_money;
        }
		else
		{
            return $this->_in_cur['cur_sign'] . $cur_money;
        }
    }

    /**
     * 读取货币金额
     * @param float $money 金额
     * @param string $currency 货币种类如：CNY/USD
     * @return string 货币兑换后的金额
     */
    public function get_cur_money($money, $currency=''){
		// 异常处理
		if ($money === null)
		{
			$money = intval($money);
		}

        if(empty($currency))
		{
			$arrDef = $this->getDefault();
			$currency = ($this->system->request['cur']) ? $this->system->request['cur'] : $arrDef['cur_code'];
		}
        if($currency){
            $this->_in_cur = $this->getcur($currency);
        }

        return $this->oMath->number_multiple(array($money, ($this->_in_cur['cur_rate'] ? $this->_in_cur['cur_rate'] : 1)));
    }

	/**
	 * 得到货币的中文名字
	 * @params string - currency code
	 * @return string - cur_name
	 */
	public function get_cur_name($currency='')
	{
		if ($currency == '')
			return '';

		$this->_in_cur = $this->getcur($currency, true);

		return $this->_in_cur['cur_name'];
	}

	/**
	 * 商店货币总额显示调整函数 - 默认没有基本格式数据，需要改变费率的
	 * @params int 货币金额
	 * @params string 货币币种
	 * @params boolean 是否为基本数据格式
	 * @params boolean 是否需要改变费率
	 */
    public function amount($money,$currency='',$basicFormat=false,$chgval=true){
		// 异常处理
		if ($money === null)
		{
			$money = intval($money);
		}

        if(empty($currency))
		{
			$arrDef = $this->getDefault();
			$currency = ($this->system->request['cur']) ? $this->system->request['cur'] : $arrDef['cur_code'];
		}
        if($currency)
		{
            $this->_in_cur = $this->getcur($currency);
        }

        if($chgval){
            $money = $this->oMath->number_multiple(array($money, ($this->_in_cur['cur_rate'] ? $this->_in_cur['cur_rate'] : 1)));
        }

        $money = $this->oMath->getOperationNumber($money);
        $money = $this->formatNumber($money);

        if($basicFormat){
            return $money;
        }

        $precision = $this->app->getConf('site_decimal_digit_display');
        /*$decimal_type = $this->app->getConf('site.decimal_type');

        $mul = '1'.str_repeat('0',$precision);
        switch($decimal_type){
            case 0:
                $money = round($money,$precision);
            break;
            case 1:
                $money = ceil(trim($money)*$mul) / $mul;
            break;
            case 2:
                $money = floor(trim($money)*$mul) / $mul;
            break;
        }*/

        return $this->_in_cur['cur_sign'] . $money;
    }

	/**
	 * 商店总额显示调整函数 - 默认没有基本格式数据，需要改变费率的
	 * @params int 货币金额
	 * @params string 货币币种
	 * @params boolean 是否为基本数据格式
	 * @params boolean 是否需要改变费率
	 */
    public function amount_nocur($money,$currency='',$basicFormat=false,$chgval=true){
		// 异常处理
		if ($money === null)
		{
			$money = intval($money);
		}

        if(empty($currency))
		{
			$arrDef = $this->getDefault();
			$currency = ($_REQUEST['cur']) ? $_REQUEST['cur'] : $arrDef['cur_code'];
		}
        if($currency){
            $this->_in_cur = $this->getcur($currency);
        }
        if($chgval){
            $money = $money*($this->_in_cur['cur_rate'] ? $this->_in_cur['cur_rate'] : 1);
        }

        $money = $this->oMath->getOperationNumber($money);
        //$money = $this->formatNumber($money);

        if($basicFormat){
            return $money;
        }

        $precision = $this->app->getConf('site_decimal_digit_display');
        /*$decimal_type = $this->app->getConf('site.decimal_type');

        $mul = '1'.str_repeat('0',$precision);
        switch($decimal_type){
            case 0:
                $money = round($money,$precision);
            break;
            case 1:
                $money = ceil(trim($money)*$mul) / $mul;
            break;
            case 2:
                $money = floor(trim($money)*$mul) / $mul;
            break;
        }*/

        return $money;
    }

	/**
	 * 取回格式化的数据，供运算使用
	 * @params int 货币金额
	 * @params boolean 是否是计算
	 * @params boolean 是否不带货币符号显示
	 * @return string 格式化后的金额
	 */
    public function formatNumber($number, $is_count=true, $is_str=true){
		// 异常处理
		if ($number === null)
		{
			$money = intval($money);
		}

		// 取到格式化后的数值
        $number = $this->oMath->getOperationNumber($number, $is_count);

		if (!$is_count)
			$this->_money_format['decimals'] = $this->oMath->goodsShowDecimals;
		else
			$this->_money_format['decimals'] = $this->oMath->operationDecimals;

        if($is_str)
		{
            return number_format(trim($number),
                $this->_money_format['decimals'],
                $this->_money_format['dec_point'],$this->_money_format['thousands_sep']);
		}
        else
            return number_format(trim($number), $this->_money_format['decimals'], '.', '');
    }
}
?>
