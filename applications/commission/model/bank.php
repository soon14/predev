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
class commission_mdl_bank
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function get_bank_list($bank_code = ''){
        $result =array();
        foreach(file($this->app->app_dir.'/payment') as $row){
            if($row){
                $row = explode(":" ,$row );
                foreach($row as $k=>$v){
                    $row[$k] = trim($v);
                }
                if($bank_code &&  $bank_code== $row[1]){
                    return $row[0];
                }
                if($row[1] == 'alipay'){
                    $result['alipay'] = $row[0];
                }else{
                    $result['bank_list'][] = array(
                        'bank_code' => $row[1],
                        'bank_name' => $row[0]

                    );
                }

            }
        }
        return $result;
    }

    public function get_bank_name($bank_code){
        return $this ->get_bank_list($bank_code);
    }
}