<?php

// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------


class universalform_export_data
{
    private $col_head = array();
    private $col_type = array();
    public function doexport($filter = array())
    {
        //查询出表单组件
        $mdl_form_module = app::get('universalform')->model('form_module');
        $form_module = $mdl_form_module -> getList('*',array('form_id'=>$filter['form_id']));
        foreach($form_module as $module) {
            $this->col_head[$module['name']] = $module['module_name'];
            $this->col_type[$module['name']] = $module['type'];
        }
        $this->col_head['universalfrom_form_data_creaetetime'] = '提交时间';

        //查询出数据
        $mdl_form_data = app::get('universalform')->model('form_data');
        $form_data = $mdl_form_data->getList('data,createtime',$filter);

        $exporter = new universalform_export_excel('browser','formdata-'.date('YmdHis').'.xls');
        $exporter->initialize();
        $exporter->addRow(array_values($this->col_head));
        foreach ($form_data as $data) {
            $row = $data['data'];
            $this->_format($row);
            $row['universalfrom_form_data_creaetetime'] = date('Y-m-d H:i:s', $data['createtime']);
            $exporter->addRow($row);
        }
        $exporter->finalize();
        exit();
    }

    private function _format(&$row){
        $col_head = array_keys($this->col_head);
        foreach ($row as $key => &$value) {
            if(!in_array($key,$col_head)){
                unset($row[$key]);
                continue;
            }
            switch($this->col_type[$key]) {
                case 'checkbox':
                    $value = implode(",",array_values($value));
                    break;
                case 'region':
                    $value = vmc::singleton('base_view_helper')->modifier_region($value);
                    break;
                case 'date':
                case 'text':
                case 'select':
                case 'image':
                case 'images':
                default:
                    break;
            }
            /*switch ($key) {
                case 'createtime':
                case 'last_modify':
                    $value = date('Y-m-d H:i:s', $value);
                    break;
                default:
                case 'consignee_area':
                    $value = vmc::singleton('base_view_helper')->modifier_region($value);
                    break;
            }*/
        }
    }
}//End Class
