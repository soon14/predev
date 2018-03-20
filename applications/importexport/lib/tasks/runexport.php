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


class importexport_tasks_runexport extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_task = app::get ('importexport')->model ('task');
        //执行队列，更新队列状态
        $mdl_task->update (array('status' => '1'), array('key' => $params['key']));
        $export = vmc::singleton ('importexport_export');

        //-------一下为ftp相关
        $FTP_policy = vmc::singleton ('importexport_policy');
        if (!$FTP_policy->local_io ()) {
            $msg = '本地文件创建失败，请检查' . TMP_DIR . '文件夹权限';
            $mdl_task->update (array('status' => 3, 'complete_date' => time (), 'message' => $msg),
                array('key' => $params['key']));

            return false;
        }
        //实例化数据类
        $data_getter = vmc::singleton('importexport_data_object', $params['model']);
        //实例化导入文件类型类
        $file_type_obj = vmc::singleton('importexport_type_' . $params['filetype']);

        //------生成文件begin
        $export->set_file_name ($FTP_policy->local_file);
        $export_object = $data_getter ->get_export_object($params['filter']);
        $export->create_file ($export_object ,$data_getter ,$file_type_obj);
        //------生成文件end
        $ret = $FTP_policy->connect ();
        //连接FTP服务失败
        if ($ret !== true) {
            $mdl_task->update (array('status' => 3, 'complete_date' => time (), 'message' => $ret),
                array('key' => $params['key']));

            return false;
        }

        $remote_file_name = $params['key'];
        if (!$FTP_policy->push ($remote_file_name, $FTP_policy->local_file, 0, $msg)) {
            $msg = 'FTP传输失败:' . $msg;
            $mdl_task->update (array('status' => 3, 'complete_date' => time (), 'message' => $msg),
                array('key' => $params['key']));

            return;
        }
        if (!$sizeof_remote_file = $FTP_policy->size ($remote_file_name)) {
            $FTP_policy->local_clean ();
            $msg = 'FTP传输异常:sizeof_remote_file is ' . $sizeof_remote_file;
            $mdl_task->update (array('status' => 3, 'complete_date' => time (), 'message' => $msg),
                array('key' => $params['key']));

            return;
        } else {
            $success_msg = '导出到FTP服务器成功！文件大小:' . number_format (($sizeof_remote_file / 1024), 3) . 'KB';
        }
        //导出结束
        $mdl_task->update (array('status' => 2, 'complete_date' => time (), 'message' => $success_msg), array('key' => $params['key']));
        //清除本地临时文件
        $FTP_policy->local_clean ();

        return true;
    }
}
