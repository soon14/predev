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


class ectools_mdl_country extends dbeav_model
{
    public $idColumn = 'ct_abbreviation';
    public $textColumn = 'ct_name';
    
    public function __construct(&$app)
    {
        parent::__construct($app);
    }//End Function

    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null)
    {
        $this->monitor_txt();
        $list = parent::getList($cols,$filter,$offset,$limit,$orderType);
        // foreach ($list as $key => &$value) {
        //     $value['flag'] = $this->app->res_url.'/misc/'.strtolower($value['ct_abbreviation']).'.png';
        // }
        return $list;
    }

    private function monitor_txt(){
        $country_txt_file = $this->app->app_dir.'/country.txt';
        $last_modify_time = filemtime($country_txt_file);
        if (!$last_modify_time) {
            return false;
        }
        $exist_last_time = $this->app->getConf('country_txt_last_modify');
        $exist_last_time = $exist_last_time ? $exist_last_time : 0;

        if ($exist_last_time - $last_modify_time == 0) {
            return true;
        }
        $this->app->setConf('country_txt_last_modify', $last_modify_time);
        //使用文件方式 读取一定格式文件
        foreach (file($country_txt_file) as $row) {
            list($abbreviation, $name, $name_en, $code) = explode("\t", trim($row));
            $data = array(
                'ct_name'=>$name,
                'ct_name_en'=>$name_en,
                'ct_abbreviation'=>$abbreviation,
                'ct_code'=>$code,
            );
            if(!$this->save($data)){
                return false;
                break;
            }
        }
        return true;
    }
}
