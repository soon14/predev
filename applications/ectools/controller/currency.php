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



class ectools_ctl_currency extends desktop_controller{



	public function __construct($app)
	{
		parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
	}

    function index(){
        $params = array(

            'title'=>'货币管理',

            'actions'=>array(
                array('label'=>'添加货币','href'=>'index.php?app=ectools&ctl=currency&act=addnew'
                      ,'target'=>'dialog::{title:\''.'添加货币'.'\',width:500,height:290}')
                ),
            );

		$this->finder('ectools_mdl_currency',$params);
    }


    /**
     * This is method addnew
     * 新增货币
	 * @params null
     * @return mixed This is the return value description
     *
     */
    public function addnew()
	{
        $currency = $this->app->model('currency');
        $this->ui = new base_component_ui($this);

        if($_POST)
		{
            $this->begin();
			// 如果默认值已经存在，出去其他的默认值
			if (isset($_POST['cur_default']) && $_POST['cur_default'] == 'true')
			{
				$currency->set_currency_default();
				// 设置默认货币汇率为1
				$_POST['cur_rate'] = '1.000';
			}
            $result = $currency->save($_POST); //使用save组织post的数据
			if (isset($_GET['action']) && $_GET['action'] && $_GET['action'] == 'edit')
				$this->end($result, '货币修改成功！'.'货币名称：'.$_POST['cur_name']); //使用恩德方法来处理保存的结果
			else
			{
				$this->end($result, '货币添加成功！'.'货币名称：'.$_POST['cur_name']);
			}
        }
		else
		{
			$arrCurs = array_merge(array(''=>'---请选择货币---'),$currency->getSysCur(true));

			$this->pagedata['curs'] = $arrCurs;
			$this->display('currency/add_cur.html');
        }
    }

	/**
	 * 修改货币
	 * @params null
     * @return mixed This is the return value description
     *
     */
	public function edit_save()
	{
		$this->begin();

		if (!$_POST)
			$this->end(false, '货币修改失败！');

		$currency = $this->app->model('currency');
		// 如果默认值已经存在，出去其他的默认值
		if (isset($_POST['cur_default']) && $_POST['cur_default'] == 'true')
		{
			$currency->set_currency_default();
			// 设置默认货币汇率为1
			$_POST['cur_rate'] = '1.000';
		}

		$result = $currency->update($_POST,array('cur_id'=>$_POST['cur_id'])); //使用save组织post的数据
		$this->end($result, '货币修改成功！'.'货币名称：'.$_POST['cur_name']); //使用恩德方法来处理保存的结果}
	}


    function seldefault()
	{
        $currency = $this->app->model('currency');
        if($_POST['default_cur']){
            $this->begin();
            $this->app->setConf('system.currency.defalt_currency',$_POST['default_cur']);
            $this->end();
        }else{
            $this->pagedata['defalt_currency'] = $this->app->getConf('system.currency.defalt_currency');
            $this->pagedata['currency'] = $currency->getList('*');
            $this->display('system/curlist.html');
        }
    }

	public function showEdit()
	{
		$currency = $this->app->model('currency');
		$rows = $currency->getList('*', array('cur_id'=>$_GET['cur_id']));
		$cur = $rows[0];
		$this->pagedata['curs'] = $currency->getSysCur(true,$cur['cur_code']);
		$this->pagedata['cur_id'] = $_GET['cur_id'];
		$this->pagedata['cur_code'] = $cur['cur_code'];
		$this->pagedata['cur_name'] = $cur['cur_name'];
		$this->pagedata['cur_sign'] = $cur['cur_sign'];
		$this->pagedata['cur_rate'] = $cur['cur_rate'];
		$this->pagedata['cur_default'] = $cur['cur_default'];

		$this->display('currency/edit_cur.html');
	}
}
