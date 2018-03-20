<?php
/**
 * This file is protected by copyright law & provided under license. Copyright(C) 2005-2009 www.chinapay.com, All rights
 */
define('DES_KEY', 'SCUBEPGW');
define('HASH_PAD', '0001ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff003021300906052b0e03021a05000414');
bcscale(0);
$private_key = array();
function hex2bin2($hexdata) {
         $bindata = '';
         if (strlen($hexdata) % 2 == 1) {
             $hexdata = '0' . $hexdata;
         }
        for ($i = 0; $i < strlen($hexdata); $i+=2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
}


function padstr($src, $len = 256, $chr = '0', $d = 'L')
{
    $ret = trim($src);
    $padlen = $len - strlen($ret);
    if ($padlen > 0) {
        $pad = str_repeat($chr, $padlen);
        if (strtoupper($d) == 'L') {
            $ret = $pad.$ret;
        } else {
            $ret = $ret.$pad;
        }
    }

    return $ret;
}

function bin2int($bindata)
{
    $hexdata = bin2hex($bindata);

    return bchexdec($hexdata);
}

function bchexdec($hexdata)
{
    $ret = '0';
    $len = strlen($hexdata);
    for ($i = 0; $i < $len; $i++) {
        $hex = substr($hexdata, $i, 1);
        $dec = hexdec($hex);
        $exp = $len - $i - 1;
        $pow = bcpow('16', $exp);
        $tmp = bcmul($dec, $pow);
        $ret = bcadd($ret, $tmp);
    }

    return $ret;
}

function bcdechex($decdata)
{
    $s = $decdata;
    $ret = '';
    while ($s != '0') {
        $m = bcmod($s, '16');
        $s = bcdiv($s, '16');
        $hex = dechex($m);
        $ret = $hex.$ret;
    }

    return $ret;
}

function sha1_128($string)
{

    $hash = sha1($string);

    $sha_bin = hex2bin2($hash);
    $sha_pad = hex2bin2(HASH_PAD);
    return $sha_pad.$sha_bin;
}

function mybcpowmod($num, $pow, $mod)
{
    if (function_exists('bcpowmod')) {
        return bcpowmod($num, $pow, $mod);
    }

    return emubcpowmod($num, $pow, $mod);
}

function emubcpowmod($num, $pow, $mod)
{
    $result = '1';
    do {
        if (!bccomp(bcmod($pow, '2'), '1')) {
            $result = bcmod(bcmul($result, $num), $mod);
        }
        $num = bcmod(bcpow($num, '2'), $mod);
        $pow = bcdiv($pow, '2');
    } while (bccomp($pow, '0'));

    return $result;
}

function rsa_encrypt($private_key, $input)
{
    $p = bin2int($private_key['prime1']);
    $q = bin2int($private_key['prime2']);
    $u = bin2int($private_key['coefficient']);
    $dP = bin2int($private_key['prime_exponent1']);
    $dQ = bin2int($private_key['prime_exponent2']);
    $c = bin2int($input);
    $cp = bcmod($c, $p);
    $cq = bcmod($c, $q);
    $a = mybcpowmod($cp, $dP, $p);
    $b = mybcpowmod($cq, $dQ, $q);
    if (bccomp($a, $b) >= 0) {
        $result = bcsub($a, $b);
    } else {
        $result = bcsub($b, $a);
        $result = bcsub($p, $result);
    }
    $result = bcmod($result, $p);
    $result = bcmul($result, $u);
    $result = bcmod($result, $p);
    $result = bcmul($result, $q);
    $result = bcadd($result, $b);
    $ret = bcdechex($result);
    $ret = strtoupper(padstr($ret));

    return (strlen($ret) == 256) ? $ret : false;
}

function rsa_decrypt($input)
{
    global $private_key;
    $check = bchexdec($input);
    $modulus = bin2int($private_key['modulus']);
    $exponent = bchexdec('010001');
    $result = bcpowmod($check, $exponent, $modulus);
    $rb = bcdechex($result);

    return strtoupper(padstr($rb));
}

function buildKey($key)
{
    global $private_key;
    if (count($private_key) > 0) {
        foreach ($private_key as $name => $value) {
            unset($private_key[$name]);
        }
    }
    $ret = false;
    $key_file = parse_ini_file($key);
    if (!$key_file) {
        return $ret;
    }
    $hex = '';
    if (array_key_exists('MERID', $key_file)) {
        $ret = $key_file['MERID'];
        $private_key['MERID'] = $ret;
        $hex = substr($key_file['prikeyS'], 80);
    } elseif (array_key_exists('PGID', $key_file)) {
        $ret = $key_file['PGID'];
        $private_key['PGID'] = $ret;
        $hex = substr($key_file['pubkeyS'], 48);
    } else {
        return $ret;
    }
    $bin = hex2bin2($hex);
    $private_key['modulus'] = substr($bin, 0, 128);
    $cipher = MCRYPT_DES;
    $iv = str_repeat("\x00", 8);
    $prime1 = substr($bin, 384, 64);
    $enc = mcrypt_cbc($cipher, DES_KEY, $prime1, MCRYPT_DECRYPT, $iv);
    $private_key['prime1'] = $enc;
    $prime2 = substr($bin, 448, 64);
    $enc = mcrypt_cbc($cipher, DES_KEY, $prime2, MCRYPT_DECRYPT, $iv);
    $private_key['prime2'] = $enc;
    $prime_exponent1 = substr($bin, 512, 64);
    $enc = mcrypt_cbc($cipher, DES_KEY, $prime_exponent1, MCRYPT_DECRYPT, $iv);
    $private_key['prime_exponent1'] = $enc;
    $prime_exponent2 = substr($bin, 576, 64);
    $enc = mcrypt_cbc($cipher, DES_KEY, $prime_exponent2, MCRYPT_DECRYPT, $iv);
    $private_key['prime_exponent2'] = $enc;
    $coefficient = substr($bin, 640, 64);
    $enc = mcrypt_cbc($cipher, DES_KEY, $coefficient, MCRYPT_DECRYPT, $iv);
    $private_key['coefficient'] = $enc;

    return $ret;
}

function sign($msg)
{
    global $private_key;
    if (!array_key_exists('MERID', $private_key)) {
        return false;
    }
    $hb = sha1_128($msg);

    return rsa_encrypt($private_key, $hb);
}

function signOrder($merid, $ordno, $amount, $curyid, $transdate, $transtype)
{
    if (strlen($merid) != 15) {
        return false;
    }
    if (strlen($ordno) != 16) {
        return false;
    }
    if (strlen($amount) != 12) {
        return false;
    }
    if (strlen($curyid) != 3) {
        return false;
    }
    if (strlen($transdate) != 8) {
        return false;
    }
    if (strlen($transtype) != 4) {
        return false;
    }
    $plain = $merid.$ordno.$amount.$curyid.$transdate.$transtype;
    return sign($plain);
}

function verify($plain, $check)
{
    global $private_key;
    if (!array_key_exists('PGID', $private_key)) {
        return false;
    }
    if (strlen($check) != 256) {
        return false;
    }
    $hb = sha1_128($plain);
    $hbhex = strtoupper(bin2hex($hb));
    $rbhex = rsa_decrypt($check);

    return $hbhex == $rbhex ? true : false;
}

function verifyTransResponse($merid, $ordno, $amount, $curyid, $transdate, $transtype, $ordstatus, $check)
{
    if (strlen($merid) != 15) {
        return false;
    }
    if (strlen($ordno) != 16) {
        return false;
    }
    if (strlen($amount) != 12) {
        return false;
    }
    if (strlen($curyid) != 3) {
        return false;
    }
    if (strlen($transdate) != 8) {
        return false;
    }
    if (strlen($transtype) != 4) {
        return false;
    }
    if (strlen($ordstatus) != 4) {
        return false;
    }
    if (strlen($check) != 256) {
        return false;
    }
    $plain = $merid.$ordno.$amount.$curyid.$transdate.$transtype.$ordstatus;

    return verify($plain, $check);
}
