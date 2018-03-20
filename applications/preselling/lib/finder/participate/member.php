<?php

/**
 * Created by PhpStorm.
 * User: cp
 * Date: 2017/6/3
 * Time: 10:43
 */
class preselling_finder_participate_member
{
    public $column_activity_name = '预售名称';
    public $column_login_account = '账户';


    public function column_activity_name($row) {
        $activity_name = app::get('preselling')->model('activity')->getRow('name',array('activity_id'=>$row['@row']['activity_id']));
        return $activity_name['name'];
    }

    public function column_login_account($row) {
        $login_account = app::get('pam')->model('members')->getRow('login_account',array('member_id'=>$row['@row']['member_id']));
        return $login_account['login_account'];
    }



    public function row_style($row)
    {
        //$row = $row['@row'];
    }

}