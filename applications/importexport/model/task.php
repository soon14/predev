<?php

class importexport_mdl_task extends dbeav_model{

    public function create_task($type='export',$params){
        $data = array(
            'name' => $params['name'],
            'key' => $params['key'],
            'filetype' => $params['filetype'],
            'create_date' => time(),
            'type' => $type,
            'status' => $params['status'] ? $params['status'] : 0,
            'is_display' => 1,
        );

        if( $this->save($data) ){
            return true;
        }else{
            return false;
        }
    }//end function


    public function pre_recycle($data){
        $policyObj= vmc::singleton('importexport_policy');
        $policyObj->connect();
        foreach($data as $row){
            if($row['type'] == 'import' || ($row['type'] == 'export' && $row['status'] == 2) ){
                $policyObj->delete($row['key']);
            }
        }
        return true;
    }

}
