<?php
class importexport_export{

    private $file_name;

    private $step=50;

    public function set_file_name($file_name){
        $this ->file_name =$file_name;
    }

    public function create_file($export_object  ,$data_getter ,$file_type_obj){
        $db = vmc::database();
        $count = $db ->selectrow("SELECT COUNT(*) AS rows_count FROM {$export_object['tables']} WHERE {$export_object['where']} ");
        $count = $count['rows_count'];
        if(!$count){
            return false;
        }
        $base_sql = "SELECT {$export_object['fields']} FROM {$export_object['tables']} WHERE {$export_object['where']}";
        $base_sql .= $export_object['order_by'] ? "ORDER BY {$export_object['order_by']}" :'';
        $i_max= ceil($count/$this->step);
        $fp = fopen($this ->file_name ,'w');
        fwrite($fp ,$file_type_obj ->fileHeader());
        //写入表头
        if($export_object['title']){
            $data_getter ->handle_title($export_object['title']);
            fwrite($fp ,$file_type_obj->arrToExportType(array($export_object['title'])));
        }
        for($i=0; $i<$i_max ;$i++){
            $query_sql =$base_sql." LIMIT ".$i*$this->step ."," .$this->step;
            $data = $db ->select($query_sql);
            if(empty($data)){
                break;
            }
            $data_getter ->handle_rows($data);
            fwrite($fp ,$file_type_obj->arrToExportType($data));
        }
        fwrite($fp ,$file_type_obj ->fileFoot());
        fclose($fp);
        return $this ->file_name;
    }
}