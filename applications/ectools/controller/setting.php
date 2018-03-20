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


class ectools_ctl_setting extends desktop_controller {
    var $require_super_op = true;
    function __construct($app) {
        parent::__construct($app);
        $this->app = $app;
    }
    function index() {
        $all_settings = array(
            '价格精度设置' => array(
                'site_decimal_digit_count',
                'site_decimal_type_count',
                //'site_decimal_digit_display',
                //'site_decimal_type_display',

            ) ,
        );
        $this->pagedata['form_inputs'] = $this->_process($all_settings);
        $this->page('setting.html');
    }


    function save(){
        $this->begin();
        foreach ($_POST['set'] as $key => $value) {
            $this->app->setConf($key, $value);
        }
        $this->end(true,'保存成功!');

    }

    private function _process($all_settings) {
        $setting = new base_setting($this->app);
        $setlib = $setting->source();
        $typemap = array(
            SET_T_STR => 'text',
            SET_T_INT => 'number',
            SET_T_ENUM => 'select',
            SET_T_BOOL => 'bool',
            SET_T_TXT => 'text',
            SET_T_FILE => 'file',
            SET_T_IMAGE => 'image',
            SET_T_DIGITS => 'number',
        );
        $tabs = array_keys($all_settings);

        foreach ($tabs as $tab => $tab_name) {
            foreach ($all_settings[$tab_name] as $set) {
                $current_set = $pre_set = $this->app->getConf($set);
                $input_type = $typemap[$setlib[$set]['type']];
                $form_input = array(
                    'label' => $setlib[$set]['desc'],
                    'type' => $input_type,
                    'name' => "set[" . $set . "]",
                    'value' => $current_set,
                    'options' => $setlib[$set]['options'],
                );
                if ($input_type == 'select') $form_input['required'] = true;
                if ($input_type == 'image') {
                    $form_input = array_merge($form_input, array(
                        'width' => $setlib[$set]['width'],
                        'height' => $setlib[$set]['height']
                    ));
                }
                $form_inputs[]= $form_input;
            }
        }
        return $form_inputs;
    }
}
