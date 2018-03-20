<?php

class vmcocean_member_create
{

    public function __construct($app){
        $this->app = $app;
    }

    public function create_after($member_id){
        $sa_stage = vmc::singleton('vmcocean_stage');
        if($_COOKIE['_VMC_UID']){
            $sa_stage->track_sign($_COOKIE['_VMC_UID'],$member_id);
        }
        $member_info = vmc::singleton('b2c_user_object')->get_member_info($member_id);
        $props = array(
            'MemberId'=>$member_id,
            'MemberName'=>$member_info['name'],
            'MemberUname'=>$member_info['uname'],
            'MemberEmail'=>$member_info['email'],
            'MemberMobile'=>$member_info['mobile'],
            'UTM_SOURCE' => $_COOKIE['UTM_SOURCE'] ? urldecode($_COOKIE['UTM_SOURCE']) : '',
            'UTM_MEDIUM' => $_COOKIE['UTM_MEDIUM'] ? urldecode($_COOKIE['UTM_MEDIUM']) : '',
            'UTM_TERM' => $_COOKIE['UTM_TERM'] ? urldecode($_COOKIE['UTM_TERM']) : '',
            'UTM_CONTENT' => $_COOKIE['UTM_CONTENT'] ? urldecode($_COOKIE['UTM_CONTENT']) : '',
            'UTM_CAMPAIGN' => $_COOKIE['UTM_CAMPAIGN'] ? urldecode($_COOKIE['UTM_CAMPAIGN']) : '',
        );
        $sa_stage->profile_set_once($member_id,$props);
    }

}//End Class
