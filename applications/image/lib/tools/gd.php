<?php

class image_tools_gd implements image_interface_tool
{
    public function resize($src_file, $target_file, $width, $height, $type, $new_width, $new_height,$x=false,$y=false)
    {
        $quality  = 100;
        $image_p = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($image_p,true);
        if($new_width>$width && $new_height>$height)
        {
            $background_color = imagecolorallocate($image_p, 255, 255, 255);
            imagefilledrectangle ( $image_p, 0, 0, $new_width, $new_height, $background_color );
        }
        // imagealphablending($image_p,true);
        switch($type){
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($src_file);
            $func = 'imagejpeg';
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($src_file);
            $func = 'imagegif';
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($src_file);
            imagealphablending($image,false);
            imagesavealpha($image,true);
            $func = 'imagepng';
            $quality  = 8;
            break;
        }
        imagesavealpha($image_p,true);
        if($new_width>$width && $new_height>$height){
            imagecopyresampled($image_p, $image, $x?$x:($new_width - $width) /2, $y?$y:($new_height - $height) /2, 0, 0, $width, $height,$width, $height);
        }else{
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        }
        if($func) $func($image_p, $target_file, $quality);
        imagedestroy($image_p);
        imagedestroy($image);
    }
    public function watermark($file, $mark_image, $set)
    {
        $sourceimage = self::imagecreatefrom($file);
        $watermark = self::imagecreatefrom($mark_image);
        imagecolortransparent($watermark, imagecolorat($watermark,0,0));
        imagealphablending($watermark,1);
        $opacity = intval($set['wm_opacity']);

        imagecopymerge($sourceimage, $watermark, $set['dest_x'], $set['dest_y'], 0,
                       0, $set['watermark_width'], $set['watermark_height'], $opacity);

        imagejpeg($sourceimage,$file);
        imagedestroy($sourceimage);
        imagedestroy($watermark);
    }

    /**
     * 通过gd库的方法生成image
     * @param string filename
     * @return resource 文件源对象
     */
    static function imagecreatefrom($file){
        list($w,$h,$type) = getimagesize($file);

        switch($type){
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($file);
        case IMAGETYPE_GIF:
            return imagecreatefromgif($file);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($file);
        }
    }

}
