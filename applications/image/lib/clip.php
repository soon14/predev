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



class image_clip{
    static $tool;
    function set_tool() {
        if(self::$tool) return;
        if(defined("IMAGE_TOOL") && in_array(IMAGE_TOOL, array('magickwand', 'gd', 'imagick'))) {
            self::$tool = vmc::singleton('image_tools_'.IMAGE_TOOL);
        } else {
            self::$tool = vmc::singleton('image_tools_gd');
        }
    }
    /**
     * 生成指定宽度和高度的图片
     * @param object image model object
     * @param string source file directory
     * @param mixed 临时数据源
     * @param string 宽度
     * @param string 高度
     * @return null
     */
    function image_resize(&$imgmdl,$src_file,$target_file,$new_width,$new_height){
        self::set_tool();
        if(isset($src_file)&&is_file($src_file)){
            list($width, $height,$type) = getimagesize($src_file);
            $size = self::get_image_size($new_width,$new_height,$width,$height);
            $new_width = $size[0];
            $new_height = $size[1];
            self::$tool->resize($src_file, $target_file, $width, $height, $type, $new_width, $new_height);
        }
    }

    /**
     * 得到修改后的图片长度和宽度
     * @param string 图片新的宽度
     * @param string 图片新的高度
     * @param string 图片原来的宽度
     * @param string 图片原来的高度
     * @return array 目前长宽
     */
    function get_image_size($new_width,$new_height,$org_width,$org_height){
        $dest_width = $new_width;
        $dest_height = $new_height;
        if($org_width>$org_height){
            if($org_width>=$new_width){
                $dest_width = $new_width;
                $dest_height = round(($org_height/$org_width)*$new_width);
            }
        }else{
            if($org_height>=$new_height){
                $dest_height = $new_height;
                $dest_width = round(($org_width/$org_height)*$new_height);
            }
        }


        if(defined('WITHOUT_AUTOPADDINGIMAGE')&&WITHOUT_AUTOPADDINGIMAGE){

            if($dest_width>$org_width){
                $dest_width = $org_width;
            }

            if($dest_height>$org_height){
                $dest_height = $org_height;
            }

        }



        return array($dest_width,$dest_height);
    }

    /**
     * 设置图片水印
     * @param object image 实体对象
     * @param string 文件路径
     * @param array 设置的集合
     * @return null
     */
    function image_watermark(&$imgmdl,$file,$set){
        self::set_tool();
        switch($set['wm_type']){
        case 'text':
            $mark_image = $set['wm_text_image'];
            break;
        case 'image':
            $mark_image = $set['wm_image'];
            break;
        default:
            return;
        }
        if($set['wm_text_preview']){
            $mark_image = $set['wm_text_image'];
        }else{
            $mark_image = $imgmdl->fetch($mark_image);
        }

        list($watermark_width,$watermark_height,$type) = getimagesize($mark_image);
        list($src_width,$src_height) = getimagesize($file);
        list($dest_x, $dest_y ) = self::get_watermark_dest($src_width,$src_height,$watermark_width,$watermark_height,$set['wm_loc']);

        $set['watermark_width'] = $watermark_width;
        $set['watermark_height'] = $watermark_height;
        $set['type'] = $type;
        $set['src_width'] = $src_width;
        $set['src_height'] = $src_height;
        $set['dest_x'] = $dest_x;
        $set['dest_y'] = $dest_y;

        self::$tool->watermark($file, $mark_image, $set);

        @unlink($mark_image);
    }

    /**
     * 得到目标水印的规格（长和宽）
     * @param string 原图的宽度
     * @param string 原图的高度
     * @param string 水印图片的宽度
     * @param string 水印图片的高度
     * @param string 水印图片的位置
     * @return array 目标图片的规格（长和宽）
     */
    static function get_watermark_dest($src_w,$src_h,$wm_w,$wm_h,$loc){
        switch($loc{0}){
        case 't':
            $dest_y = ($src_h - 5 >$wm_h)?5:0;
            break;
        case 'm':
            $dest_y = floor(($src_h - $wm_h)/2);
            break;
        default:
            $dest_y = ($src_h - 5 >$wm_h)?($src_h - $wm_h - 5):0;
        }

        switch($loc{1}){
        case 'l':
            $dest_x = ($src_w - 5 >$wm_w)?5:0;
            break;
        case 'c':
            $dest_x = floor(($src_w - $wm_w)/2);
            break;
        default:
            $dest_x = ($src_w - 5 >$wm_w)?($src_w - $wm_w - 5):0;
        }

        return array($dest_x,$dest_y);

    }


}
