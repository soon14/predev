<?php

class o2ocds_data_qrcode
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function get_title($title = array())
    {
        //定义表头
        return array(
            'prefix' => '二维码批次号(prefix)',
            'qrcode' => '二维码序号(qrcode)',
            'enterprise_id' => '所属企业',
            'store_id' => '所属店铺',
            'status' => '使用状态',
        );
    }

    public function get_extend_title() {
        return array(
            'qrcode_bn' => '二维码编号',
            'qrcode_url' => '二维码地址',
        );
    }
    public function get_extra_title(&$row){
        unset($row['prefix']);
        unset($row['qrcode']);
        return array(
            'qrcode_bn' => '二维码编号',
            'qrcode_url' => '二维码地址',
        );
    }
    /**
     * 导出订单数据，修改数据库已有数据
     * @return array $row 修改过后的数据
     */
    public function get_content_row($row){
        unset($row['prefix'],$row['qrcode']);
        if($store = app::get('o2ocds')->model('store')->getRow('name',array('store_id'=>$row['store_id']))) {
            $row['store_id'] = $store['name'];
        };
        if($enterprise = app::get('o2ocds')->model('enterprise')->getRow('name',array('enterprise_id'=>$row['enterprise_id']))) {
            $row['enterprise_id'] = $enterprise['name'];
        };
        $data[0] = $row;
        return $data;
    }//end function

    public function get_extend_rows($rows) {
        foreach($rows as $k=>$row) {
            $qrcode_bn = $row['prefix'].$row['qrcode'];
            $rows[$k]['qrcode_bn'] = $qrcode_bn;
            $url_preview = $this->app->getConf('domain');
            $o2o_url = app::get('mobile')->router()->gen_url(array('app'=>'o2ocds','ctl'=>'mobile_qrrouter'));
            $o2o_url_explode = array_values(array_filter(explode('/',$o2o_url)));
            if($o2o_url_explode[0] != 'm' && $o2o_url_explode[1] != 'm') {
                $o2o_url = '/m'.$o2o_url;
            };
            $url_preview .= $o2o_url.'?qrcode='.$qrcode_bn;
            $rows[$k]['qrcode_url'] = $url_preview;
        }
        return $rows;
    }







}
