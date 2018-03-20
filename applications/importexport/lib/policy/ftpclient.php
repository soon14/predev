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


class importexport_policy_ftpclient
{
    public $mode = FTP_BINARY;
    public function __construct()
    {
        $this->ftp_server = app::get('importexport')->getConf('ftp_server_setting');
        if (!$this->conn) {
            $this->ftp_server['port'] = $this->ftp_server['port'] ? $this->ftp_server['port'] : 21;
            $this->conn = new importexport_policy_FTPClient_FTPClient($this->ftp_server['host'], $this->ftp_server['port']);
            if (!$this->conn->login($this->ftp_server['name'], $this->ftp_server['pass'])) {
                echo 'Cannot login!';
            }
        }
        $this->cd($this->ftp_server['dir']);
    }

    /*
     * 检查FTP配置
     * */
    public function check_login($params)
    {
        $params['timeout'] = 5;
        $conn = new importexport_policy_FTPClient_FTPClient($params['host'], $params['port']);
        $msg = true;
        if (!$conn) {
            $msg = 'FTP连接失败，地址或端口错误，或者连接超时，请检查';

            return $msg;
        }

        if ($params['name']) {
            $login = $conn->login($params['name'], $params['pass']);
            if (!$login) {
                $msg = 'FTP登陆失败，用户名或密码错误，请检查';

                return $msg;
            }
        }

        return $msg;
    }

    public function cd($dir)
    {
        return $this->conn->changeDirectory($dir);
    }

    public function size($remote_file)
    {
        return $this->conn->getFileSize($remote_file);
    }

    public function nb_continue()
    {
        return FTP_FINISHED;
    }

    public function nb_get($local, $remote, $resume = 0)
    {
        $ret = $this->conn->download($remote, $local, importexport_policy_FTPClient_FTPClient::MODE_BINARY);
        if ($ret === false) {
            return FTP_FAILED;
        }

        return FTP_MOREDATA;
    }

    public function nb_put($remote, $local, $resume = 0)
    {
        $ret = $this->conn->upload($local, $remote, importexport_policy_FTPClient_FTPClient::MODE_BINARY);
        if ($ret === false) {
            trigger_error('文件上传到FTP服务器错误');

            return FTP_FAILED;
        }

        return FTP_MOREDATA;
    }

    public function delete_file($file)
    {
        return $this->conn->removeFile($file);
    }
}
