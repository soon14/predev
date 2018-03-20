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


class wechat_finder_replyrule
{
    public $detail_media = '回复内容详情';
    public $detail_media_order = COLUMN_IN_HEAD;
    public $column_media = '自动回复内容';
    public $column_media_order = COLUMN_IN_HEAD;
    public $column_edit = '编辑';
    public $column_edit_order = COLUMN_IN_HEAD;

    public function detail_media($id)
    {
        $detail = app::get('wechat')->model('replyrule')->dump($id, '*', 'default');
        $render = app::get('wechat')->render();
        $render->pagedata['detail'] = $detail;
        return $render->fetch('admin/replyrule_detail.html');

    }

    public function column_media($row)
    {
        $detail = app::get('wechat')->model('replyrule')->dump($row['replyrule_id'], '*', 'default');
        switch ($detail['media']['wtype']) {
            case 'text':
                return '<i class="fa  fa-file-word-o "></i> 纯文本';
                break;
            case 'image':
                return '<i class="fa fa-image "></i> 图片';
                break;
            case 'video':
                return '<i class="fa  fa-video-camera "></i> 视频';
                break;
            case 'voice':
                return '<i class="fa fa-file-audio-o "></i> 语音';
                break;
            case 'news':
                return '<i class="fa fa-list-alt"></i> 图文';
                break;
        }
    }

    public function column_edit($row){
        return "<a class='btn btn-xs btn-default' href='index.php?app=wechat&ctl=admin_autoreply&act=edit&p[0]=".$row['replyrule_id']."'><i class='fa fa-edit'></i> 编辑</a>";
    }
}
