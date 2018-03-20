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


class codebuy_mdl_code extends dbeav_model
{
    public function generateCode($activity_id,$nums = 50,$op_name,$remark=''){
        $mdl_activity = $this->app->model('activity');
        $activity = $mdl_activity->getRow('id,batch,code_number',array('id'=>$activity_id));
        if(!empty($activity)){
            for ($i = 1; $i <= $nums; $i++) {
                if ($code = $this->_makeCode($activity['code_number'] + $i, $activity['batch'])) {
                    $saveData = array(
                            'activity_id'=>$activity['id'],
                            'code'=>$code,
                            'op_name'=>$op_name,
                            'createtime'=>time(),
                            'remark'=>$remark
                    );
                    $this->save($saveData);
                    $_return[] = $code;
                } else {
                    return false;
                }
            }
            return $_return;
        }else{
            return false;
        }
    }

    public function dec2b36($int){
        $b36 = array(0 => '0',1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',6 => '6',7 => '7',8 => '8',9 => '9',10 => 'A',11 => 'B',12 => 'C',13 => 'D',14 => 'E',15 => 'F',16 => 'G',17 => 'H',18 => 'I',19 => 'J',20 => 'K',21 => 'L',22 => 'M',23 => 'N',24 => 'O',25 => 'P',26 => 'Q',27 => 'R',28 => 'S',29 => 'T',30 => 'U',31 => 'V',32 => 'W',33 => 'X',34 => 'Y',35 => 'Z');
        $retstr = '';
        if ($int > 0) {
            while ($int > 0) {
                $retstr = $b36[($int % 36)].$retstr;
                $int = floor($int / 36);
            }
        } else {
            $retstr = '0';
        }

        return $retstr;
    }

    private function _makeCode($code_number, $batch)
    {
        $cout_len = 5;
        $encrypt_len = 10;
        if ($cout_len >= strlen(strval($code_number))) {
            $code_number = str_pad($this->dec2b36($code_number), $cout_len, '0', STR_PAD_LEFT);
            $checkCode = md5($key.$code_number.$batch);
            $checkCode = strtoupper(substr($checkCode, 0, $encrypt_len));
            $code = $batch.$checkCode.$code_number;

            return $code;
        } else {
            return false;
        }
    }
}
