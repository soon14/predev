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


class base_request
{
    public $request_params = array();

    public function set_params($request_params)
    {
        $this->request_params = $request_params;
    }

    public static function get_base_url()
    {
        $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
        if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $base_url = $_SERVER['ORIG_SCRIPT_NAME'];
        } elseif (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $base_url = $_SERVER['SCRIPT_NAME'];
        } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
            $base_url = $_SERVER['PHP_SELF'];
        } else {
            $path = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
            $file = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $base_url = '';
            do {
                $seg = $segs[$index];
                $base_url = '/'.$seg.$base_url;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $base_url))) && (0 != $pos));
        }

        $request_uri = self::get_request_uri();
        if (0 === strpos($request_uri, $base_url)) {
            return self::dirname($base_url);
        }
        if (0 === strpos($request_uri, strstr(PHP_OS, 'WIN') ? str_replace('\\', '/', dirname($base_url)) : dirname($base_url))) {
            return self::dirname($base_url);
        }

        $truncatedrequest_uri = $request_uri;
        if (($pos = strpos($request_uri, '?')) !== false) {
            $truncatedrequest_uri = substr($request_uri, 0, $pos);
        }

        $basename = basename($base_url);
        if (empty($basename) || !strpos($truncatedrequest_uri, $basename)) {
            return;
        }

        if ((strlen($request_uri) >= strlen($base_url))
        && ((false !== ($pos = strpos($request_uri, $base_url))) && ($pos !== 0))) {
            $base_url = substr($request_uri, 0, $pos + strlen($base_url));
        }

        return  rtrim(self::dirname($base_url), '/');
    }

    public static function get_path_info()
    {
        $path_info = '';
        if (isset($_SERVER['PATH_INFO'])) {
            $path_info = $_SERVER['PATH_INFO'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $path_info = $_SERVER['ORIG_PATH_INFO'];
            $script_name = self::get_script_name();
            if (substr($script_name, -1, 1) == '/') {
                $path_info = $path_info.'/';
            }
        } else {
            $script_name = self::get_script_name();
            $script_dir = preg_replace('/[^\/]+$/', '', $script_name);
            $request_uri = self::get_request_uri();
            $urlinfo = parse_url($request_uri);
            if (strpos($urlinfo['path'], $script_name) === 0) {
                $path_info = substr($urlinfo['path'], strlen($script_name));
            } elseif (strpos($urlinfo['path'], $script_dir) === 0) {
                $path_info = substr($urlinfo['path'], strlen($script_dir));
            }
        }
        if ($path_info) {
            $path_info = '/'.ltrim($path_info, '/');
        }

        return $path_info;
    }

    public static function get_script_name()
    {
        return isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : '');
    }

    public static function get_request_uri()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            return $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            return $_SERVER['ORIG_PATH_INFO'].(!empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '');
        }
    }

    public static function get_host()
    {
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        //$host = $_SERVER['HTTP_HOST'];
        if (!empty($host)) {
            return $host;
        }

        $scheme = self::get_schema();
        $name = self::get_name();
        $port = self::get_port();

        if (($scheme == 'HTTP' && $port == 80) || ($scheme == 'HTTPS' && $port == 443)) {
            return $name;
        } elseif ($port > 0) {
            return $name.':'.$port;
        } else {
            return $name;
        }
    }

    public static function get_name()
    {
        return $_SERVER['SERVER_NAME'];
    }//End Function

    public static function get_schema()
    {
        if (defined('HTTPS') && HTTPS == true) {
            return 'HTTPS';
        } elseif (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return 'HTTPS';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return 'HTTPS';
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return 'HTTPS';
        }elseif($_SERVER['SERVER_PORT'] == "443"){
            return 'HTTPS';
        }
        return 'HTTP';
    }//End Function

    public static function get_port()
    {
        return $_SERVER['SERVER_PORT'];
    }//End Function

    public static function get_remote_addr()
    {
        if (!isset($GLOBALS['_REMOTE_ADDR_'])) {
            $addrs = array();
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                foreach (array_reverse(explode(',',  $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_f) {
                    $x_f = trim($x_f);
                    if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f)) {
                        $addrs[] = $x_f;
                    }
                }
            }
            $GLOBALS['_REMOTE_ADDR_'] = isset($addrs[0]) ? $addrs[0] : isset($_SERVER) && !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }

        return $GLOBALS['_REMOTE_ADDR_'];
    }

    public static function dirname($dir)
    {
        return substr($dir, 0, strrpos($dir, '/'));
    }

    public static function ip_in_range($ip, $range)
    {
        if ($ip === $range) {
            return true;
        }
        if (strpos($range, '/') !== false) {
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);

                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            } else {
                $x = explode('.', $range);
                while (count($x) < 4) {
                    $x[] = '0';
                }
                list($a, $b, $c, $d) = $x;
                $range = sprintf('%u.%u.%u.%u', empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            if (strpos($range, '*') !== false) {
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float) sprintf('%u', ip2long($lower));
                $upper_dec = (float) sprintf('%u', ip2long($upper));
                $ip_dec = (float) sprintf('%u', ip2long($ip));

                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }

            return false;
        }
    }
}
