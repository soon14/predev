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




class wechat_mdl_bind extends dbeav_model
{

    public function modifier_qr($col){
        $img_src = base_storager::image_path($col);
        if (!$img_src) {
            return '';
        }
        return "<a href='$img_src' target='_blank'><img class='img-thumbnail' src='$img_src' style='height:30px;'></a>";
    }

    public function modifier_avatar($col){
        $img_src = base_storager::image_path($col);
        if (!$img_src) {
            return '';
        }

        return "<a href='$img_src' target='_blank'><img class='img-thumbnail' src='$img_src' style='height:30px;'></a>";
    }

    /**
     * 删除公众账号前判断
     */
    public function pre_recycle($rows){

        return true;
    }

}
