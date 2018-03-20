<?php
class importexport_type_csv implements importexport_interface_type{

    public function __construct(){
        setlocale(LC_ALL, "zh_CN");
        $this->charset = vmc::singleton('base_charset');
    }

    public function fileHeader()
    {
        return ;
    }

    public function fileFoot()
    {
        return ;
    }

    /*
     * 将导出的数组转换为csv格式，
     * 约定：在每次转换后最后在此函数换行(循环调用此函数进行写文件)
     *       在将转换后的字符串写到文件中则不进行换行操作
     *
     * @params $data array 需要导入的数组，一维数组
     * @return $rs string 转换后csv格式的字符串
     * */
    public function arrToExportType($data){
        $rs = '';
        if( is_array($data) ){
            foreach( (array)$data as $val ){
                $exportData[] = '"'.implode('","',$val).'"';
            }
        }else{
            $exportData[0] = $data;
        }
        $rs = implode("\n",(array)$exportData)."\n";

        $rs = iconv('utf-8', 'gb2312//IGNORE', $rs);

        return $rs;
    }


    /**
     * 获取文件中每行数据
     *
     * @param $handle 打开的文件句柄
     * @param $contents 获取到的数据
     * @param $line 行数
     */
    public function fgethandle(&$handle,&$contents,$line){
        $row = fgetcsv($handle);

        if ( !$row ) return false;

        foreach( $row as $num => $col )
        {
            if ($line==0 && $num==0) {
                // 判断下文档的字符集.
                if (!$this->charset->is_utf8($col)){
                    $this->is_utf8 = false;
                }else{
                    $this->is_utf8 = true;
                    if ($col_tmp = $this->charset->replace_utf8bom($col)){
                        // 替换两个双引号
                        $col = substr($col_tmp, 1, -1);
                    }
                }
            }
            if (!$this->is_utf8){
                $contents[$line][$num] = $this->charset->local2utf( (string) $col);
            }
            else{
                $contents[$line][$num] = (string) $col;
            }
        }
        return true;
    }

    public function setBom()
    {
        return $out = "\xEF\xBB\xBF";// 加上bom头，系统自动默认为UTF-8编码
    }

    /**
     *下载文件支持断点续传header
     *
     * @params string $filename 下载文件名称
     */
    public function set_queue_header($filename,$size=null){
        header("Cache-Control: public");
        header("Content-Type: application/force-download");
        header("Accept-Ranges: bytes");
        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $iefilename = preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1);
            header("Content-Disposition: attachment; filename=\"$iefilename\"");
        } else {
            header("Content-Disposition: attachment; filename=\"$filename\"");
        }
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');


        if( $size !== null ){
            if(isset($_SERVER['HTTP_RANGE'])) {
                list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']);
                str_replace($range, "-", $range);
                $size2=$size-1;
                $new_length=$size2-$range+3;
                header("HTTP/1.1 206 Partial Content");
                header("Content-Length: $new_length");
                header("Content-Range: bytes $range$size2/$size");
            } else {
                $range = 0;
                $size2=$size-1;
                $size3=$size+3;
                header("Content-Range: bytes 0-$size2/$size");
                header("Content-Length: ".$size3);
            }
        }
        return $range;
    }

}
