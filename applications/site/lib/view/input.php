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




class site_view_input{

    function input_checkbox($params){
        $params['type'] = 'checkbox';
        $params['class'] = 'x-check'.($params['class'] ? ' '.$params['class'] : '');
        $params['autocomplete'] = 'off';
        return utils::buildTag($params,'input');
    }
    function input_radio($params){
        $params['type'] = 'radio';
        $params['class'] = 'x-check'.($params['class'] ? ' '.$params['class'] : '');
        $params['autocomplete'] = 'off';
        return utils::buildTag($params,'input');
    }

    function input_datepicker($params){
        if(!$params['type']){
            $params['type'] = 'date';
        }
        if(!$params['vtype']){
            $params['vtype'] = 'date';
        }else if ($params['vtype'] != 'date'){
          $params['vtype'] = $params['vtype'].'&&date';
        }
        if(is_numeric($params['value'])){
            $params['value'] = date('Y-n-j',$params['value']);
        }
        if(isset($params['concat'])){
            $params['name'] .= $params['concat'];
            unset($params['concat']);
        }
        // if(!$params['format'] || $params['format']=='timestamp'){
        //     $prefix = '<input type="hidden" name="_DTYPE_'.strtoupper($params['type']).'[]" value="'.htmlspecialchars($params['name']).'" />';
        // }else{
        //     $prefix = '';
        // }

        $params['type'] = 'text';
        $return = utils::buildTag($params,'input class="x-input calendar'.($params['class'] && $params['class'] != 'cal' ? ' '.$params['class'] : '').'" maxlength="10" readonly="readonly"');
        return $prefix.$return;
    }


}//End Class
