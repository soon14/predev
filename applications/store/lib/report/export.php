<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/2/3
 * Time: 20:53
 */
class store_report_export{
    private $file_dir;
    private $file_name;
    public function set_file_dir($file_dir){
        if(!is_dir($file_dir)){
            mkdir($file_dir ,'0755');
        }
        $this ->file_dir =$file_dir;
    }

    public function set_file_name($file_name){
        $this ->file_name =$file_name;
    }
    public function create_csv($sql){
        $db = vmc::database();
        $count = $db ->select("SELECT count(*) as count FROM {$sql['table']} {$sql['where']} ");
        $count = $count[0]['count'];
        if(!$count){
            return false;
        }
        $base_sql = "SELECT {$sql['field']} FROM {$sql['table']} {$sql['where']} {$sql['orderby']}";
        $step = 500;
        $i_max= ceil($count/$step);
        $file_name = $sql['type'].'-report'.time().'.csv';
        $this ->set_file_name($file_name);
        $fp = fopen($this ->file_dir.DIRECTORY_SEPARATOR.$file_name ,'w');
        //写入表头
        if($sql['title']){
            fputcsv($fp ,$sql['title']);
        }

        for($i=0; $i<$i_max ;$i++){
            $query_sql =$base_sql." LIMIT ".$i*$step ."," .$step;
            $data = $db ->select($query_sql);
            if(empty($data)){
                break;
            }
            foreach($data as $v){
                fputcsv($fp ,array_values($v));
            }
        }
        fclose($fp);
        return $file_name;
    }

    public function delete_file(){
        $file_path = $this ->file_dir.DIRECTORY_SEPARATOR.$this->file_name;
        unlink($file_path);
    }

    public function download(){
        $file_path = $this ->file_dir.DIRECTORY_SEPARATOR.$this->file_name;
        if (!file_exists($file_path)) { //检查文件是否存在
            return false;
        } else {
            $fp=fopen($file_path,"r");
            $file_size=filesize($file_path);
            //下载文件需要用到的头
            Header('Content-Encoding: none');
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:".$file_size);
            Header("Content-Disposition: attachment; filename=".$this->file_name);
            Header('Pragma: no-cache');
            Header('Expires: 0');
            $buffer=1024;
            $file_count=0;
            //向浏览器返回数据
            while(!feof($fp) && $file_count<$file_size){
                $file_con=fread($fp,$buffer);
                $file_count+=$buffer;
                echo $file_con;
            }
            fclose($fp);
            exit;
        }
    }
}