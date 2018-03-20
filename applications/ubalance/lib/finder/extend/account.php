<?php

/**
 * Created by PhpStorm.
 * User: cp
 * Date: 2016/6/13
 * Time: 17:00
 */
class ubalance_finder_extend_account
{
    function get_extend_colums(){
        $member_lv = app::get('b2c')->model('member_lv')->getList('member_lv_id,name');
        $member_lv_type = array();
        foreach($member_lv as $v) {
            $member_lv_type[$v['member_lv_id']] = $v['name'];
        }
        $db['account']=array (
            'columns' =>
                array (
                    'member_lv_id' =>
                        array (
                            'label' => '会员等级',
                            'type' => $member_lv_type,
                            'filtertype' => 'normal',
                        ),
                    'tag_name' =>
                        array (
                            'label' => '标签',
                            'filtertype' => 'yes',
                        ),
                ),
        );
        return $db;
    }
}