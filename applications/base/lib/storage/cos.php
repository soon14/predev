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


require_once APP_DIR.'/qcloud/cos/include.php';
use QCloud\Cos\Api;

class base_storage_cos implements base_interface_storager
{
    private $_tmpfiles = array();
    public function __construct()
    {
        $this->COS_service = new Api(array(
            'app_id' => COS_APPID,
            'secret_id' => COS_SECRETID,
            'secret_key' => COS_SECRETKEY,
            'region' => COS_REGION,
            'timeout' => 60
        ));
    } //End Function
    //上传文件到COS
    public function save($file, &$url, $type, $addons, $ext_name = '')
    {
        if (defined('COS_BUCKET') && defined('COS_REGION')) {
            $bucket = COS_BUCKET;
        } else {
            return false;
        }
        $filename = $this->_get_ident($file, $type, $addons, $url, $path, $ext_name);
        $object = $path.$filename;
        try {
            $response = $this->COS_service->upload($bucket, $file, $object);
        } catch (COSException $e) {
            logger::error('COS ERROR:'.$e->getMessage());

            return false;
        }
        if (!empty($response) && $response['message'] == 'SUCCESS') {
            if (!defined('COS_HOST_MIRROR')) {
                $url = '//'.COS_BUCKET.'-'.COS_APPID.'.file.myqcloud.com/'.$object;
            } else {
                $url = COS_HOST_MIRROR.'/'.$object;
            }
        } else {
            logger::error('COS ERROR:');
            logger::error($response);
        }

        return $object.'@'.$bucket;
    } //End Function
    // 生成object名字
    public function _get_ident($file, $type, $addons, $url, &$path, $ext_name = '')
    {
        $ident = md5(rand(0, 9999).microtime());
        // 路径
        if (isset($addons['path']) && $addons['path']) {
            $path = $addons['path'];
        } else {
            $path = $ident{0}
            .$ident{5}
            .'/'.$ident{3}
            .$ident{1}
            .'/'.substr($ident, 3, 6);
        }
        // 文件名
        if (isset($addons['name']) && $addons['name']) {
            $fname = $addons['name'];
        } else {
            $fname = substr(md5(($addons ? $addons : $file).microtime()), 3, 6);
        }
        // 后缀
        if ($ext_name) {
            if (strrpos($fname, '.')) {
                $fname = substr($fname, 0, strrpos($fname, '.')).$ext_name;
            } else {
                $fname .= $ext_name;
            }
        }

        return $fname;
    } // end function _get_ident
    //图片直接缩略服务
    public static function image_rebuild($url, $size_k = false, $Q = 100, $ext = '.jpg')
    {
        if (!$size_k) {
            return false;
        }
        $image_conf = app::get('image')->getConf('size');

        $size = $image_conf[strtoupper($size_k) ];
        if (!$size) {
            $arr = explode('x', $size_k);
            if (is_numeric($arr[0]) && $arr[0] > 1 && is_numeric($arr[1]) && $arr[1] > 0) {
                $size['width'] = $arr[0];
                $size['height'] = $arr[1];
            }
        }
        $url = str_replace('file.myqcloud','image.myqcloud',$url);
        return $url.'?imageView2/0/'.implode('/', array(
            'w',
            ($size['width'] ? $size['width'] : 100),
            'h',
            ($size['height'] ? $size['height'] : 100)
        )).'/q/100/';
    }
    //图片占位符处理
    public static function modifier_process($url, $size_k)
    {
        return self::image_rebuild($url, $size_k);
    }
    //替换指定object@bucket
    public function replace($file, $id)
    {
        $o = explode('@', $id);
        $object = $o[0];
        $bucket = $o[1];
        if (!$object || !$bucket) {
            return false;
        }
        try {
            $this->COS_service->upload($bucket, $file, $object);
        } catch (COSException $e) {
            logger::error('COS ERROR:'.$e->getMessage());

            return false;
        }

        return true;
    } //End Function
    //删除指定object@bucket
    public function remove($id)
    {
        if ($id) {
            $o = explode('@', $id);
            $object = $o[0];
            $bucket = $o[1];
            if (!$object || !$bucket) {
                return false;
            }
            try {
                $this->COS_service->delFile($bucket, $object);
            } catch (COSException $e) {
                logger::error('COS ERROR:'.$e->getMessage());

                return false;
            }

            return true;
        } else {
            return false;
        }
    } //End Function
    //拉取指定bucket 指定object
    public function getFile($id, $type)
    {
        $o = explode('@', $id);
        $object = $o[0];
        $bucket = $o[1];
        if (!$object || !$bucket) {
            return false;
        }
        $tmpfile = tempnam(TMP_DIR, 'COSsystem');
        array_push($this->_tmpfiles, $tmpfile);
        try {
            $this->COS_service->download($bucket, $tmpfile, $object);
        } catch (COSException $e) {
            logger::error('COS ERROR:'.$e->getMessage());

            return false;
        }

        return $tmpfile;
    } //End Function
    //析构函数
    public function __destruct()
    {
        foreach ($this->_tmpfiles as $tmpfile) {
            @unlink($tmpfile);
        } //todo unlink tmpfiles;
    } //End Function
} //End Class
