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

class ectools_barcode_show{
    function get($code="1234567890" ,$type='code128' ,$option=array()){

        $fontSize = (int)$option['fontSize']?$option['fontSize'] :6;   // GD1 in px ; GD2 in point
        $marge    = (int)$option['marge']?$option['marge'] :2;   // between barcode and hri in pixel
        $x        = (int)$option['x']?$option['x'] :150;  // barcode center
        $y        = (int)$option['y']?$option['y'] :25;  // barcode center
        $height   = (int)$option['height']?$option['height'] :50;   // barcode height in 1D ; module size in 2D
        $width    = (int)$option['width']?$option['width'] :2;    // barcode height in 1D ; not use in 2D
        $angle    = (int)$option['angle']?$option['angle'] :0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation

        $im     = imagecreatetruecolor(2*$x, $height);
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);
        $red    = ImageColorAllocate($im,0xff,0x00,0x00);
        $blue   = ImageColorAllocate($im,0x00,0x00,0xff);
        imagefilledrectangle($im, 0, 0, 300, 300, $white);
        $data = ectools_barcode_create::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
        if ( isset($font) ){
            $box = imagettfbbox($fontSize, 0, $font, $data['hri']);
            $len = $box[2] - $box[0];
            ectools_barcode_create::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
            imagettftext($im, $fontSize, $angle, $x + $xt, $y + $yt, $blue, $font, $data['hri']);
        }

        //中轴线
//        imageline($im, $x, 0, $x, 250, $red);
//        imageline($im, 0, $y, 250, $y, $red);

        //四脚线
//        for($i=1; $i<5; $i++){
//            $this ->drawCross($im, $blue, $data['p'.$i]['x'], $data['p'.$i]['y']);
//        }
        header('Content-type: image/gif');
        imagegif($im);
        imagedestroy($im);
    }

    function drawCross($im, $color, $x, $y){
        imageline($im, $x - 10, $y, $x + 10, $y, $color);
        imageline($im, $x, $y- 10, $x, $y + 10, $color);
    }
}