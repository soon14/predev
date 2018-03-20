<?php

class vmcocean_datafilter
{

    public function __construct($app){
        $this->app = $app;
    }

    public function opinfo($member_sdf,$mdl_name,$function_trace){
    
        $member_save_hook = "b2c_mdl_members:save";
        if($member_save_hook == implode(':',array($mdl_name,$function_trace))){
            // $sa_stage = vmc::singleton('vmcocean_stage');
            // //TODO
            // $update_props = array(
            //
            // );
            // $sa_stage->profile_set_once($member_sdf['member_id'],$update_props);
        }

    }

}//End Class
