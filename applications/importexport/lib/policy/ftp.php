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


class importexport_policy_ftp implements importexport_interface_policy
{
    public $mode = FTP_BINARY;
    public $ftp_extension = true;

    public function __construct()
    {
        $this->extension_loaded_ftp();
    }

    /**
     * 判断php是否安装了FTP扩展.
     *
     * @return string
     */
    public function extension_loaded_ftp()
    {
        $this->ftp_extension = extension_loaded('ftp') ? true : false;
        if (!$this->ftp_extension) {
            $this->ftpclient = vmc::singleton('importexport_policy_FTPClient_client');
            $this->mode = 'MODE_BINARY';
        }

        return $this->ftp_extension;
    }

    /**
     * 连接FTP服务器,并且登录.
     *
     * @params array $params ftp服务器配置信息
     */
    public function connect($params, &$msg)
    {
        if (!$params['host']) {
            $msg = 'FTP地址必填';

            return false;
        }

        $params['port'] = $params['port'] ? $params['port'] : 21;
        $params['timeout'] = $params['timeout'] ? $params['timeout'] : 30;
        if ($this->ftp_extension) {
            try{
                $connect = ftp_connect($params['host'], $params['port'], $params['timeout']);
            }catch(Exception $e){
                $msg = $e->getMessage();
                return false;
            }

            $this->conn = $connect;
        } else {
            $connect = $this->ftpclient->connect($params['host'], $params['port'], $params['timeout']);
        }

        if (!$connect) {
            $msg = '连接FTP失败，请检查FTP地址或FTP端口';

            return false;
        }

        if (!$this->_login($params, $msg)) {
            return false;
        }

        $this->changeDirectory($params['dir']);

        return true;
    }

    /**
     * 登录到FTP服务器.
     *
     * @params array $params FTP用户名和密码
     *
     * @return bool 登录成返回true 登录失败则返回异常错误
     */
    private function _login($params, &$msg)
    {
        if (!$params['name'] || !$params['pass']) {
            $msg = '登录到FTP失败，请检查用户名和密码';

            return false;
        }

        if ($this->ftp_extension) {
            $flag = @ftp_login($this->conn, $params['name'], $params['pass']);
            if($params['pasv'] == 'Y'){
                ftp_pasv($this->conn,TRUE);//开启被动模式
            }
        } else {
            $flag = $this->ftpclient->login($params['name'], $params['pass']);
        }

        if (!$flag) {
            $msg = '登录到FTP失败，请检查用户名和密码';

            return false;
        }

        return true;
    }

    /**
     * 检查FTP配置.
     *
     * @params array $params FTP配置信息参数
     *
     * @return bool 成功返回true 失败则返回 false
     */
    public function check($params)
    {
        $params['timeout'] = 5;//5秒连接失败则检查不通过

        if (!$this->connect($params, $msg)) {
            trigger_error($msg, E_USER_ERROR);

            return false;
        }

        $tmpFile = tempnam(TMP_DIR, 'importExportTest');
        file_put_contents($tmpFile, 'This is test file');
        $params['remote'] = 'importExportTest';
        $params['local'] = $tmpFile;
        $params['resume'] = 0;
        //检查上传文件
        if (!$this->push($params, $msg)) {
            trigger_error($msg, E_USER_ERROR);

            return false;
        }

        //检查下载文件
        if (!$this->pull($params, $msg)) {
            trigger_error($msg, E_USER_ERROR);

            return false;
        }

        return true;
    }//end function

    /**
     * 更改目录，如果配置为空，则将文件存储到FTP根目录下.
     *
     * @params string $dir 目录
     *
     * @return bool 返回true 如果配置的目录不存在则忽略错误
     */
    public function changeDirectory($dir = null)
    {
        if ($this->ftp_extension) {
            @ftp_chdir($this->conn, $dir); //目录错误会返回警告，屏蔽
        } else {
            $this->ftpclient->changeDirectory($dir);
        }

        return true;
    }

    /**
     * 将本地文件上传到FTP.
     *
     * @params array $params 参数 array('local'=>'本地文件路径','remote'=>'远程文件路径','resume'=>'文件指针位置')
     * @params string $msg
     *
     * @return bool
     */
    public function push($params, &$msg)
    {
        if ($this->ftp_extension) {
            try {

                $ret = ftp_nb_put($this->conn, $params['remote'], $params['local'], $this->mode, $params['resume']);
                while ($ret == FTP_MOREDATA) {
                    $ret = ftp_nb_continue($this->conn);
                }
            } catch (Exception $e) {
                $ret = $e->getMessage();
            }
        } else {
            $ret = $this->ftpclient->upload($params['local'], $params['remote'], $this->mode, $params['resume']);
        }
        if ($ret == FTP_FAILED || !$ret) {

            $msg = ('上传失败返回信息 '.var_export($ret, 1));
            return false;
        }

        return true;
    }

    /**
     * FTP中文件下载到本地.
     *
     * @params array $params 参数 array('local'=>'本地文件路径','remote'=>'远程文件路径','resume'=>'文件指针位置'
     * @params string $msg
     *
     * @return bool
     */
    public function pull($params, &$msg)
    {
        if ($this->ftp_extension) {
            $ret = ftp_nb_get($this->conn, $params['local'], $params['remote'], $this->mode, $params['resume']);
            while ($ret == FTP_MOREDATA) {
                $ret = ftp_nb_continue($this->conn);
            }
        } else {
            $ret = $this->ftpclient->download($params['remote'], $params['local'], $this->mode, $params['resume']);
        }

        if ($ret == FTP_FAILED || $ret === false) {
            $msg = 'FTP下载文件失败';

            return false;
        }

        return true;
    }

    /**
     * 获取FTP文件大小.
     */
    public function size($filename)
    {
        if ($this->ftp_extension) {
            return ftp_size($this->conn, $filename);
        } else {
            return $this->ftpclient->getFileSize($filename);
        }
    }

    /**
     * 删除FTP文件.
     */
    public function delete($filename)
    {
        if ($this->ftp_extension) {
            $size = $this->size($filename);
            if (!$size || $size == -1) {
                return true;
            }

            return ftp_delete($this->conn, $filename);
        } else {
            return $this->ftpclient->removeFile($filename);
        }
    }
}
