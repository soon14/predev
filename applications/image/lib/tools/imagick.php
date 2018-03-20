<?php

class image_tools_imagick implements image_interface_tool
{
    public function resize($src_file, $target_file, $width, $height, $type, $new_width, $new_height,$x=false,$y=false)
    {
        $thumb = new Imagick($src_file);
        $thumb->thumbnailImage($new_width, $new_height);
        $thumb->writeImage($target_file);
        $thumb->clear();
        $thumb->destroy();
    }
    public function watermark($file, $mark_image, $set)
    {
        $image = new Imagick();
        $image->readImage($file);
        $watermark = new Imagick();
        $watermark->readImage($mark_image);
        $opacity = isset($set['wm_opacity']) ? (int) $set['wm_opacity'] : 100;
        $watermark->setImageOpacity( $opacity/100 );
        $image->compositeImage($watermark, imagick::COMPOSITE_DEFAULT, $set['dest_x'], $set['dest_y']);
        $image->writeImage();
        $image->clear();
        $image->destroy();
        $watermark->clear();
        $watermark->destroy();
    }
}
