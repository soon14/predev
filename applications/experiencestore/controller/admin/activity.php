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

class experiencestore_ctl_admin_activity extends desktop_controller
{
    public function subject()
    {
        $this->finder('experiencestore_mdl_activity_subject', array(
            'title' => ('活动主题列表'),
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'actions' => array(
                array(
                    'label' => ('新建活动主题'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=experiencestore&ctl=admin_activity&act=edit_subject',
                ),
            ),
        ));
    }
    public function schedule()
    {
        $this->finder('experiencestore_mdl_activity_schedule', array(
            'title' => ('活动场次列表'),
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'actions' => array(
                array(
                    'label' => ('新建活动场次'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=experiencestore&ctl=admin_activity&act=edit_schedule',
                ),
            ),
        ));
    }
    public function order()
    {
        $this->finder('experiencestore_mdl_activity_order', array(
            'title' => ('活动预约列表'),
            // 'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'use_buildin_export' => true,
            'actions' => array(

            ),
        ));
    }

    public function edit_subject($id)
    {
        if ($id) {
            $mdl_subject = $this->app->model('activity_subject');
            $subject = $mdl_subject->dump($id);
            $this->pagedata['subject'] = $subject;
        }

        $this->page('admin/activity/subject/edit.html');
    }
    public function load_subject($id)
    {
        $subject = $this->app->model('activity_subject')->dump($id);
        $subject['image'] = base_storager::image_path($subject['default_image_id']);
        $this->splash('success', null, 'success', 'echo', array('subject' => $subject));
    }
    public function save_subject()
    {
        $this->begin('index.php?app=experiencestore&ctl=admin_activity&act=subject');
        $data = $_POST;
        $mdl_subject = $this->app->model('activity_subject');
        $subject = $data['subject'];
        if ($mdl_subject->save($subject)) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }
     /**
      *
      */
     public function edit_schedule($id)
     {
         if ($id) {
             $mdl_schedule = $this->app->model('activity_schedule');
             $schedule = $mdl_schedule->dump($id);
             $this->pagedata['schedule'] = $schedule;
             $mdl_ticket = $this->app->model('activity_ticket');
             $this->pagedata['tickets'] = $mdl_ticket->getList('*', array('schedule_id' => $id));
         }
         $this->page('admin/activity/schedule/edit.html');
     }
    public function save_schedule()
    {
        $this->begin('index.php?app=experiencestore&ctl=admin_activity&act=schedule');
        $data = $_POST;
        $mdl_schedule = $this->app->model('activity_schedule');
        $schedule = $data['schedule'];
        if (!$schedule['id'] && $mdl_schedule->count(array('code' => trim($schedule['code'])))) {
            $this->end(false, '活动场次编码重复');
        }
        if ($schedule['id'] && $mdl_schedule->count(array('code' => trim($schedule['code']), 'id|noequal' => $schedule['id']))) {
            $this->end(false, '活动场次编码重复');
        }
        $schedule['from_time'] = strtotime($schedule['from_time']);
        $schedule['to_time'] = strtotime($schedule['to_time']);
        if ($schedule['from_time'] > $schedule['to_time']) {
            $this->end(false, '活动时间设置不对');
        }
        $schedule['begin_time'] = $schedule['begin_time'] ? strtotime($schedule['begin_time']) : time();
        $schedule['end_time'] = $schedule['end_time'] ? strtotime($schedule['end_time']) : $schedule['to_time'];
        if ($schedule['begin_time'] > $schedule['end_time']) {
            $this->end(false, '活动预约时间设置不对');
        }
        $store = $this->app->model('store')->dump($schedule['store_id']);
        if (!$store) {
            $this->end(false, '门店设置不对');
        }
        $schedule['limit'] = $schedule['limit'] > 0 ? $schedule['limit'] : 0;
        if ($schedule['need_ticket'] == 'false' && !$schedule['limit']) {
            $this->end(false, '请填写该场次的人数限定');
        }
        $schedule['ticket_amount'] = $schedule['limit'];
        $schedule['reserve'] = 0;
        if ($mdl_schedule->save($schedule)) {
            $ticket = $data['ticket'];
            $mdl_ticket = $this->app->model('activity_ticket');
            $ticket_list = $mdl_ticket->getList('*', array('schedule_id' => $schedule['id']));
            $ticket_list = utils::array_change_key($ticket_list, 'id');
            $mdl_ticket->delete(array('schedule_id' => $schedule['id']));
            if (is_array($ticket) && !empty($ticket)) {
                $schedule['ticket_amount'] = 0;
                foreach ($ticket as $key => $row) {
                    $row['schedule_id'] = $schedule['id'];
                    if (stripos($key, 'new_') === false) {
                        $row['id'] = $key;
                    }
                    $row['sale_nums'] = $ticket_list[$row['id']]['sale_nums'];
                    $sn = substr(strlen($key) > 1 ? $key : '0'.$key, -2);
                    $row['batch_no'] = $ticket_list[$row['id']]['batch_no'] ? $ticket_list[$row['id']]['batch_no'] : date('ymd').$store['loc_id'].$sn;
                    $schedule['reserve'] += $row['reserve'];
                    $schedule['ticket_amount'] += ($row['max'] + $row['reserve']);
                    if (!$mdl_ticket->save($row)) {
                        $this->end(false, '票券信息保存失败');
                    }
                }
                $schedule['limit'] = $schedule['ticket_amount'];
            }
            if ($schedule['need_ticket'] == 'true') {
                $mdl_schedule->save($schedule);
            }
            foreach (vmc::servicelist('experiencestore.activity_schedule.save') as $obj) {
                if (method_exists($obj, 'exec')) {
                    $obj->exec($schedule);
                }
            }
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }
     /*
      *
      */
}
