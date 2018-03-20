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


class universalform_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public $filter = array(
        'module_id',
        'form_id',
        'module_name',
        'show',
        'm_order',
    );
    public function function_formmodule($params, &$smarty)
    {
        $data = $params['data'];
        unset($params['data']);
        foreach($data as $k=>$v) {
            if(in_array($k,$this->filter)) {
                unset($data[$k]);
            };
        }
        $data = array_merge((array)$data,(array)$params);
        if($data['required'] == 'false') {
            unset($data['required']);
        }
        $method = $data['type'];
        if($method == 'region') {
            $obj = vmc::singleton('ectools_view_input');
            $data['app'] = 'ectools';
            return $obj->input_region($data);
        }
        if(method_exists($this,$method)) {
            return $this->$method($data);
        };
        return '';
    }

    private function checkbox($params){

        $params['type'] = 'checkbox';
        $options = $params['options'];
        $value = $params['value'];
        $params['name'] = $params['name'].'[]';
        unset($params['options'],$params['value']);
        $input_tpl = utils::buildTag($params,'input ',true);
        $htmls = array();
        foreach($options as $k=>$item){
            if($value==$k || in_array($k,$value)){
                $html = str_replace('/>',' value="'.htmlspecialchars($k).'" checked="checked" />',$input_tpl);
            }else{
                $html = str_replace('/>',' value="'.htmlspecialchars($k).'" />',$input_tpl);
            }
            $html = str_replace('id="'.$id_base.'"', 'id="'.$id.'"', $html);
            $htmls[]= '<label class="checkbox-inline">'.htmlspecialchars($item).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$html.'</label>';
        }
        $params['separator'] = $params['separator']?$params['separator']:'';
        $return = implode($params['separator'],$htmls);

        return $return;
    }

    private function select($params){

        if(is_string($params['options'])){
            $ui = new base_component_ui($this);
            $params['remote_url'] = $params['options'];
            $params['options'] = array($params['value']=>$params['value']);
        }
        if($params['rows']){
            foreach($params['rows'] as $r){
                $step[$r[$params['valueColumn']]]=intval($r['step']);
                $options[$r[$params['valueColumn']]] = $r[$params['labelColumn']];
            }
            unset($params['valueColumn'],$params['labelColumn'],$params['rows']);
        }else{
            $options = $params['options'];
            unset($params['options']);
        }
        $params['name'] = $params['search']?'_'.$params['name'].'_search':$params['name'];
        $params['class'] .= ' form-control';
        $value = $params['value'];
        unset($params['value']);
        $html=utils::buildTag($params,'select',false);
        if(!$params['required']){
            $html.='<option></option>';
        }
        foreach((array)$options as $k=>$item){
            if($k==='0' || $k===0){
                $selected = ($value==='0' || $value===0);
            }else{
                $selected = ($value==$k);
            }
            $t_step=$step[$k]?str_repeat('&nbsp;',($step[$k]-1)*3):'';
            $html.='<option'.($params['noselect']?' disabled=true ':' ').($selected?' selected="selected"':'').' value="'.htmlspecialchars($k).'">'.$t_step.htmlspecialchars($item).'</option>';
        }
        $html.='</select>';
        return $html.$script;
    }

    private function text($params){
        unset($params['options']);
        $params['class'] .= ' form-control';
        $html=utils::buildTag($params,'input',false);
        return $html;
    }

    private function date($params){
        $params['class'] .= ' form-control';
        $ret = $this->build_params($params);
        $html = '<input type="date" '.implode(' ', $ret).' >';
        return $html;
    }

    private function image($params) {
        $value = $params['value'];
        if(!$value) {
            $params['class'] .= ' form-control';
        }
        $ret = $this->build_params($params);
        if($value) {
            $img_src = base_storager::modifier($value, 's');
            $html =  '<img  src="'.$img_src.'"'.implode(' ',$ret).'>';
        }else{
            $html = '<input type="file" '.implode(' ', $ret).'>';
        }
        return $html;
    }

    private function images($params) {
        $value = $params['value'];

        if(!$value) {
            $params['class'] .= ' form-control';
        }
        $ret = $this->build_params($params);

        if($value) {
            $html = '';
            foreach($value as $image_id) {
                $img_src = base_storager::modifier($image_id, 's');
                $html .=  '<img  src="'.$img_src.'"'.implode(' ',$ret).'>';
            }
        }else{
            $html = '<a class="btn btn-danger" id="add_images">添加更多图片&nbsp;<i class="fa fa-plus"></i></a>';
            $file = '<input type="file" '.implode(' ', $ret).'>';
            $html .= $file;
            $html .= <<<EOF
        <script charset="utf-8">
            $("#add_images").on('click',function(){
                $(this).parent().append('$file');
            });
        </script>
EOF;
        }

        return $html;
    }


    private function build_params($params) {
        $ret = array();
        foreach ((array) $params as $k => $v) {
            if (!is_null($v) && !is_array($v)) {
                if ($k == 'value') {
                    $v = htmlspecialchars($v);
                }
                $ret[] = $k.'="'.$v.'"';
            }
        }
        return $ret;
    }

}