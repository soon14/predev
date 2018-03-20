<?php
/**
 *excel xml 格式的数据获取
 */
class importexport_type_excel_reader {

    /**
     * 是否已找到一行数据的开始
     *
     * true 为已找到 false 已找到一行并且进行结束 null 还没找到
     */
    private $rowopen = null;

    /**
     *将xml转换为array 不进行任何逻辑处理
     */
    public function xmlToArray($xml)
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xml, $tags);
        xml_parser_free($parser);

        return $tags;
    }

    /**
     * 根据单行的xml,获取到整行数据的xml
     *
     * @params string $lineXml 一行的xml
     * @return bool true 需要继续传入lineXml
     * @return array $data 已近找到excel中一行的数据，不进行任何数据处理
     */
    public function getRowXml($lineXml)
    {

        if( $this->rowopen )
        {
            $this->xml .= $lineXml;
        }
        else
        {
            $this->xml = $lineXml;
        }

        $data = $this->xmlToArray($this->xml);

        foreach( (array)$data as $value )
        {
            if( $this->rowopen != true && $value['tag'] == 'Row' && $value['type'] == 'open' )
            {
                $this->rowopen = true;
            }

            if( $this->rowopen == true && $value['tag'] == 'Row' && $value['type'] == 'close' )
            {
                $this->rowopen = false;
            }
        }

        if($this->rowopen === true || $this->rowopen === null)
        {
            $return = true;
        } 
        else
        {
            $this->rowopen = null;
            $return = $data;
        }

        return $return;
    }

    /**
     * excel xml格式中一行未经过处理完整的数据，转化为导入格式的数组
     *
     * @params array $data xml转换为array的数据
     * @return array $row  一行导入的数组
     */
    public function getRowData($data)
    {
        $row = array();
        $index = 0;
        foreach( (array)$data as $value )
        {
            if( $cellopen != true && $value['tag'] == 'Cell' && $value['type'] == 'open' )
            {
                $cellopen = true;
                if( $value['attributes']['ss:Index'] )
                {
                    for( $index; $index < ($value['attributes']['ss:Index'] - 1);$index++ )
                    {
                        $row[$index] = '';
                    }
                }
                $index = $value['ss:Index'] ? ($value['ss:Index'] - 1) : $index;
            }

            if( $cellopen == true && $value['tag'] == 'Cell' && $value['type'] == 'close' )
            {
                $cellopen = false;
                $index++;
            }
            if( ($cellopen == true || $value['tag'] == 'Cell' ) && $value['type'] == 'complete')
            {
                if( $value['attributes']['ss:Type'] == 'Number' )
                {
                    $value['value'] = $value['value'];
                }
                elseif( $value['attributes']['ss:Type'] == 'Boolean' )
                {
                    $value['value'] = $value['value'] ? 'true' : 'false';
                }
                $row[$index] .= $value['value'] ? $value['value'] : '';

                if( $value['tag'] == 'Cell' ) $index++;
            }
        }

        return $row;
    }


}
