<?php

class image_tools_magickwand implements image_interface_tool
{
    public function resize($src_file, $target_file, $width, $height, $type, $new_width, $new_height,$x=false,$y=false) 
    {
        $rs = NewMagickWand();
        if(MagickReadImage($rs,$src_file)){
            MagickResizeImage($rs,$new_width,$new_height,MW_QuadraticFilter,0.3);
            MagickSetImageFormat($rs,'image/jpeg');
            MagickWriteImage($rs, $target_file);
        }
        return true;
    }
    public function watermark($file, $mark_image, $set)
    {
        $sourceWand = NewMagickWand();
        $compositeWand = NewMagickWand();
        MagickReadImage($compositeWand, $mark_image);
        MagickReadImage($sourceWand, $file);
        MagickSetImageIndex($compositeWand, 0);
        MagickSetImageType($compositeWand, MW_TrueColorMatteType);
        MagickEvaluateImage($compositeWand, MW_SubtractEvaluateOperator, ($set['wm_opacity']?$set['wm_opacity']:50)/100, MW_OpacityChannel) ;
        MagickCompositeImage($sourceWand, $compositeWand, MW_ScreenCompositeOp, $set['dest_x'], $set['dest_y']);
        MagickWriteImage($sourceWand, $file);
    }
}
