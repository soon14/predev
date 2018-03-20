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


class restrict_tasks_restrict extends base_task_abstract implements base_interface_task
{
    public function __construct($app)
    {
        $this->mdl_restrict = app::get('restrict')->model('restrict');
    }

    public function exec($params = null)
    {
        $filter_begin = array(
            'status' => 'false',
            'state' => '0',
            'from_time|sthan' => time(),//sthan 小于等于
            //'from_time|bthan' => time(),//bthan 大于等于
            //'to_time|sthan' => time(),//sthan 小于等于
            );
        $rows_restrict_begin = $this->mdl_restrict->getList('*',$filter_begin);
        foreach($rows_restrict_begin as $begin){
            if( empty($begin['to_time'])||$begin['to_time']>time() ){
                $this->res_begin($begin);
            }
        }
        $filter_end = array(
            'status' => 'true',
            'state' => '1',
            //'to_time|sthan' => time(),
            );
        $rows_restrict_end = $this->mdl_restrict->getList('*',$filter_end);
        foreach($rows_restrict_end as $end){
            if( !empty($end['to_time'])&&$end['to_time']<time() ){
                $this->res_end($end);
            }
        }
        return true;
    }

    private function res_begin($data)
    {
        $data['state'] = '1';
        $data['status'] = 'true';
        $data['out_time'] = time();
        return $this->mdl_restrict->save($data);
    }
    private function res_end($data)
    {
        $data['state'] = '2';
        $data['status'] = 'false';
        return $this->mdl_restrict->save($data);
    }

}
?>