<?php
class importexport_type_xls implements importexport_interface_type{

    private $__excelRoot = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>';

    private function __excelXmlHeader()
    {
        $excelXmlHeaderStr = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:x="urn:schemas-microsoft-com:office:excel"
            xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
            xmlns:html="http://www.w3.org/TR/REC-html40">
            <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
            <Author>VMCSHOP</Author>
            <Created>'.time().'</Created>
            <Company>www.vmcshop.com</Company>
            </DocumentProperties>'."\n";

        return $excelXmlHeaderStr;
    }

    /***
     * 单个工作薄定义
     */
    private function __worksheet()
    {
        $worksheet = '<Worksheet ss:Name="Sheet1"><Table>'."\n";
        return $worksheet;
    }

    /**
     * 将Excel格式的XML文件转换为数组
     *
     * @params string $lineXml 一行xml数据
     * @return bool true 表示还没获取到一行的excel数据需要继续传入xml
     * @return array  返回获取到excel一行完整的数据
     */
    private function __preExcelXmlToArray($lineXml){

        $preData = $this->excelXmlReader->getRowXml($lineXml);

        //找到行数据的头部，或者还没找到 ,还没找到行尾，继续
        if( $preData === true ){
            return true;
        }
        //找到行尾，处理一行数据
        return $this->excelXmlReader->getRowData($preData);
    }

    public function __construct(){
        $this->charset = vmc::singleton('base_charset');
        $this->excelXmlReader = vmc::singleton('importexport_type_excel_reader');
    }

     /**
     * excel 文件头部定义标签
     */
    public function fileHeader()
    {
        $fileHeader = $this->__excelRoot . $this->__excelXmlHeader() . $this->__worksheet();
        return $fileHeader;
    }

    /**
     * excel 文件尾部结束标签
     */
    public function fileFoot()
    {
        $fileFoot = '</Table>'."\n".'</Worksheet></Workbook>';
        return $fileFoot;
    }

    /**
     * 将导出的数组转换为excel 格式，
     * 约定：在每次转换后最后在此函数换行(循环调用此函数进行写文件)
     *       在将转换后的字符串写到文件中则不进行换行操作
     *
     * @params $data array 需要导入的数组，一维数组
     * @return $rs string 转换后excel格式的字符串
     */
    public function arrToExportType($data)
    {
        $rs = '';
        if( empty($data) ) return '';
        if( is_array($data) )
        {
            foreach( (array)$data as $key=>$val )
            {
                $rs .= '<Row';
                if( $val['Index'] )
                {
                    $rs .= ' ss:Index="'.($val['Index']+1).'">'."\n";
                    $rowData = $val['rowData'];
                }
                else
                {
                    $rs .= '>';
                    $rowData = $val;
                }
                foreach( (array)$rowData as $v )
                {
                    $rs .= '<Cell ';
                    if( is_array($v) )
                    {
                        if( $v['Index'] ){
                            $rs .= ' ss:Index="' . (intval($v['Index'])+1) . '"';
                        }
                        if( $v['MergeAcross'] ){
                            $rs .= ' ss:MergeAcross="' . (intval($v['MergeAcross'])-1) . '"';
                        }
                        if( $v['MergeDown'] ){
                            $rs .= ' ss:MergeDown="' . (intval($v['MergeDown'])-1) . '"';
                        }
                        $value = htmlspecialchars($v['value']);
                    }
                    else
                    {
                        $value = htmlspecialchars($v);
                    }
                    $rs .= '><Data ss:Type="String">'.$value.'</Data></Cell>'."\n";
                }
                $rs .= '</Row>'."\n";
            }
        }
        else
        {
            $rs .= '<Row>'."\n";
            $rs .= '<Cell><Data ss:Type="String">'.htmlspecialchars($data).'</Data></Cell>'."\n";
            $rs .= '</Row>'."\n";
        }

        return $rs;
    }

    /**
     * @param $handle 打开的文件句柄
     * @param $line 行数
     * @param $line 行数
     */
    public function fgethandle(&$handle,&$contents,$line){

        while($lineXml = fgets($handle)){
            $row = $this->__preExcelXmlToArray($lineXml);
            //表示找到一行数据的头部，继续查询直到找到行尾
            if( $row === true ) continue;
            //如果返回数据则表示返回一行数据
            if( is_array($row) ) break;
        }

        if ( !$lineXml ) return false;

        $contents[$line] = $row;

        return true;
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
                $new_length=$size2-$range;
                header("HTTP/1.1 206 Partial Content");
                header("Content-Length: $new_length");
                header("Content-Range: bytes $range$size2/$size");
            } else {
                $range = 0;
                $size2=$size-1;
                $size3=$size;
                header("Content-Range: bytes 0-$size2/$size");
                header("Content-Length: ".$size3);
            }
        }
        return $range;
    }
}
