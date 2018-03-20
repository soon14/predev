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


class universalform_mdl_form_module extends dbeav_model {

    public $defaultOrder = array(
        'm_order DESC,module_id DESC',
    );

    public function insert(&$data)
    {
        if(!$data['name']) {
            $tb = $this->table_name(1);
            do{
                $name = $this->getRandomStr(8);
                $row = $this->db->selectrow('SELECT `name` from '.$tb.' where form_id = '.$data['form_id'].' AND name ='.$name);
            }while($row);
            $data['name'] = $name;
        }
        return parent::insert($data);
    }

    /**
     * 随机生成字符串.
     *
     * @return string 生成的字符串
     */
    public function getRandomStr($len)
    {
        $str = '';
        $str_pol = 'abcdefghijklmnopqrstuvwxyz';
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < $len; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }


}
