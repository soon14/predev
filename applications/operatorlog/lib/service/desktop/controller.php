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


/**
* 该类主要是用来记录后台管理员的操作日志,当对象销毁前执行
*/
class operatorlog_service_desktop_controller
{
    public function get_delimiter(){
        return '--';
    }

    public function construct()
    {
        $this->_predata = $_POST;
    }

    /**
    * 记录管理员日志
    * @param object $controller 后台控制器对象
    */
    public function destruct($controller)
    {
        if(isset($controller->_end_status) && $controller->_end_status == 'error'){
        }else{
            $this->_logs($controller);
        }
    }

    /**
    * 插入管理员日志的操作
    * @access private
    * @params object $controller 后台控制器对象
    */
    private function _logs($controller) {
    // p($this->_predata);
        $data['app'] = $_GET['app'];
        $data['ctl'] = $_GET['ctl'];
        $data['act'] = $_REQUEST['action'] ? $_REQUEST['action'] : $_GET['act'];

        $rows = app::get('operatorlog')->model('register')->getList('*', $data);
        if($rows[0]['operate_type']){
            $memo = $this->print_template($rows[0]['param'],$rows[0]['template'],$rows['0']['method']);
            if($memo){
                if($rows['0']['prk']=='0'){
                    $prk_parse = '0';
                }else{
                    $prk_parse = $this->parse_param($rows['0']['prk']);
                }
                if($prk_parse=='0'){
                    $prefix = '';
                }elseif(empty($prk_parse)){
                    $prefix = "添加 ";
                }else{
                    $prefix = "编辑 ";
                }
                $this->logs($rows[0]['module'], $prefix.$rows[0]['operate_type'], $prefix.$memo);
            }
        }

    }//End Function

    /**
    * 生成日志记录值
    * @access private
    * @params string $params 日志参数 如"goods.name,goods.goods_id"，goods.name=>$_POST['goods']['name']
    * @params string $template 日志模板%s是函数sprintf的写法对照
    * @params string $method 提交方法，post,get,默认post
    */
    private function print_template($params,$template,$method='post'){
        if(empty($params)) return $template;
        $template_arr = explode(',', $params);
        $temp_value = '';
        foreach ($template_arr as $key => $value) {
            if(!$tmpv = $this->parse_params($value,$method)){return false;};
            $temp_value .= "'".$tmpv."'".',';
        }
        eval('$temp_str=sprintf($template,'.rtrim($temp_value,',').');');
        return $temp_str;
    }

    private function parse_params($params,$method='post'){
        $subscriptName = explode('.',$params);
        $str = '';
        foreach ($subscriptName as $key => $value) {
            $str .="['".$value."']";
        }
        $varname = '$_'.strtoupper($method).$str;
        eval('if($method=="post"){if(!$_POST){return false;}}$postvalue='.$varname.';');
        if(is_array($postvalue)){
            $newvalue = '';
            foreach ($postvalue as $key => $value) {
                $newvalue .= '"'.$value.'",';
            }
            $postvalue = rtrim($newvalue,',');
        }
        return $postvalue;
    }

    public function logs($module,$operate_type,$memo){
        $obj = new desktop_user();
        $data['username'] = ($obj->get_login_name())?($obj->get_login_name()):'system_core';
        $data['module'] = $module;
        $data['operate_type'] = $operate_type;
        $data['dateline'] = time();
        $data['memo'] = $memo;
        app::get('operatorlog')->model('normallogs')->insert($data);
    }

    private function parse_param($param){
        $subscriptName = explode('.',$param);
        $str = '';
        foreach ($subscriptName as $key => $value) {
            $str .="['".$value."']";
        }
        eval('$postvalue=$this->_predata'.$str.';');
        if(!$postvalue){
            return false;
        }
        return $postvalue;
    }

}//End Class