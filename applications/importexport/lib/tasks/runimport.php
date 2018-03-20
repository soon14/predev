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


class importexport_tasks_runimport extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $msg = '';
        $mdl_task = app::get('importexport')->model('task');

        //执行队列，更新队列状态
        $mdl_task->update(array('status' => 4), array('key' => $params['key']));

        //连接导入文件上传的服务器
        $FTP_policy = vmc::singleton('importexport_policy');
        $ret = $FTP_policy->connect();
        if ($ret !== true) {
            $mdl_task->update(array('status' => 6, 'complete_date' => time(), 'message' => $ret), array('key' => $params['key']));

            return false;
        }

        $remote_file_name = $params['key'];
        //创建本地临时文件
        if (!$FTP_policy->local_io()) {
            $msg = '本地文件创建失败，请检查' . TMP_DIR . '文件夹权限';
            $mdl_task->update(array('status' => 7, 'message' => $msg), array('key' => $params['key']));

            return false;
        }

        //同步远程文件到本地临时文件
        if (!$FTP_policy->pull($FTP_policy->local_file, $remote_file_name, 0, $msg)) {
            $mdl_task->update(array('status' => 7, 'message' => $msg), array('key' => $params['key']));
            trigger_error('从FTP传输文件到本地失败:' . $msg, E_USER_ERROR);
        }

        //实例化数据类
        $data_getter = vmc::singleton('importexport_data_object', $params['model']);
        try{
            $reader = new importexport_type_reader($FTP_policy->local_file ,false ,false,$params['filetype']);
            $total_rows = $reader->count();
        }catch (Exception $e){
            $msg = '文件读取失败'.$e ->getMessage();
            $mdl_task->update(array('status' => 7, 'message' => $msg), array('key' => $params['key']));
            return false;
        }

        if($total_rows<2){
            $msg = '没有可导入的数据';
            $mdl_task->update(array('status' => 7, 'message' => $msg), array('key' => $params['key']));
            return false;
        }
        $total_rows -=1; //除去表头的数据行数

        $import_title = $reader->current();
        $data_getter ->set_real_title($import_title);

        $ready_mdl = vmc::singleton($params['model']);
        $rows = array();
        $warning = array();
        $line =1;
        $success = 0;
        $db = vmc::database();
        while($total_rows){
            $group_line =$line+1;
            do{
                //首次进入判断不用累计
                empty($rows) ?:$total_rows--;
                empty($rows) ?:$line++;
                $current = $reader->next();
                if(empty($current)){
                    $warning[] ='第'.$line.'行数据为空';
                    continue;
                }
            }while($data_getter ->need_continue($current ,$rows));
            $row_sdf = $data_getter->dataToSdf($rows, $msg);
            $rows= array($data_getter->change_row_keys($current));//重新赋值
            if(empty($row_sdf)){
                $warning[] ='第'.$group_line.'-'.$line.'行数据导入失败:'.$msg;
                continue;
            }
            $trans =$db->beginTransaction();
            $result = $ready_mdl->save($row_sdf);
            if(!$result){
                $db->rollback();
                $warning[] ='第'.$group_line.'-'.$line.'行数据插入数据库保存失败';
                continue;
            }
            $result = $data_getter->import_after($row_sdf, $msg);
            if (!$result) {
                $db->rollback();
                $warning[] = '第'.$group_line.'-'.$line.'行数据导入后的操作失败:'.$msg;
                continue;
            }
            $db->commit();
            $success ++;
        }
        if (!empty($warning)) {
            $mdl_task->update(array('error_content'=>$warning ,'status' => ($success ? 8 : 6), 'message' => '部分数据导入错误'), array('key' => $params['key']));
        }else{
            if (!$data_getter->import_end($msg)) {
                $warning[] = '导入完成后置操作失败' . $msg;
            }
            $mdl_task->update(array('error_content'=>$warning ,'status' => 5, 'complete_date' => time(), 'message' => '成功导入'), array('key' => $params['key']));
        }
        $FTP_policy->local_clean();
        return true;
    }
}
