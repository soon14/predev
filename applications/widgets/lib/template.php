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
class widgets_template
{
    public function __construct (&$app)
    {
        $this->app = $app;
    }

    public function get_file_list($type ='pc' ,$current='/'){
        if($type == 'mobile'){
            $current_theme_dir = vmc::get_themes_m_root_dir().'/' .vmc::singleton('mobile_theme_base') ->get_default();
        }else{
            $current_theme_dir = vmc::get_themes_root_dir().'/' .vmc::singleton('site_theme_base') ->get_default();
        }
        $this->current_dir = $current_dir = $current_theme_dir.$current;
        //过滤非法操作
        if(false === stripos(realpath($current_dir) ,realpath($current_theme_dir))){
            return array();
        }
        $file_config = $this->get_config($type);
        $file_config = utils::array_change_key($file_config ,'value');
        $file_list = glob( $current_dir."/*" ,GLOB_NOSORT  );
        $file_types = array(
            'html' =>'模板文件',
            'xml' =>'配置文件'
        );
        $result = array();
        foreach($file_list as $k=>$v){
            $v =  str_ireplace('\\' ,'/' ,$v);
            $file_type = is_dir($v) ?'目录' :$file_types[pathinfo($v,PATHINFO_EXTENSION)];
            $result[$k]['type'] = $file_type ? $file_type :'资源文件';
            $result[$k]['is_writable'] = $file_type ?true :false;
            $result[$k]['last_modify'] = filemtime($v);
            $result[$k]['is_dir'] = is_dir($v) ?true :false;
            $result[$k]['file_path'] = rawurlencode(substr($v , strlen($current_theme_dir)));
            $result[$k]['use'] = $file_config[ substr($v , strlen($current_theme_dir)+1)]['attr']['use'];
            $result[$k]['file_name'] = basename($v);
            $result[$k]['file_extension'] = pathinfo($v,PATHINFO_EXTENSION);
        }
        return $result;
    }

    public function get_path_node($current , $base ='/'){
        $nodes = explode('/' ,$current);
        $node_arr = array();
        foreach($nodes as $k =>$v){
            $path = rawurlencode(substr($current,0 ,stripos($current ,$v)).$v);
            if($path == ''){
                continue;
            }
            $node_arr[] = array(
                'name' =>$v,
                'path' =>rawurlencode(substr($current,0 ,stripos($current ,$v)).$v)
            );
        }
        return $node_arr;
    }
    public function get_content($type='pc',$file_path=''){
        if($file_path==''){
            return '文件不存在';
        }
        if($type == 'mobile'){
            $current_theme_dir = vmc::get_themes_m_root_dir().'/' .vmc::singleton('mobile_theme_base') ->get_default();
        }else{
            $current_theme_dir = vmc::get_themes_root_dir().'/' .vmc::singleton('site_theme_base') ->get_default();
        }
        $file_path = $current_theme_dir.$file_path;
        return $file_content = file_get_contents($file_path);
    }
    //此方法是按照行来排列返回数组
    public function get_widgets($type='pc',$file_path='',$file_type){
        if($file_path==''){
            return '文件不存在';
        }
        if($file_type == 'widget'){
            $file_path = explode('/',$file_path);
            unset($file_path[0]);
            $file_path = implode('/',$file_path);
        }
        $file_content = $this->get_content($type,'/'.$file_path);
        $lines = preg_split('/[\r\n]+/s',$file_content);
        $widgets_data = array();
        $group_index = 0;
        $mdl_widgets = $this->app->model('widgets');
        $mdl_instantiation = $this->app->model('instantiation');
        //$mdl_groups = $this->app->model('groups');
        foreach($lines as $line_number=>$line){
            $flags = preg_match_all('/\<\{widgets\s+code=[^\}\>]*\}\>/',$line,$match);
            $group_flags = preg_match_all('/\<\{widgets\s+group=[^\}\>]*\}\>/',$line,$group_match);
            if(!empty($group_match[0])){
                foreach($group_match[0] as $gkey=>$gvalue){
                    $gtmp_str = preg_replace('/\<\{(.*)\}\>/','$1',$gvalue);
                    $gtmp_arr = explode(' ',$gtmp_str);
                    $gtmp_data = array(
                        'line_number'=>$line_number,
                        'line'=>$line,
                        'group_index'=>$group_index
                    );
                    $group_index = $group_index + 1;
                    foreach($gtmp_arr as $gattr){
                        if(preg_match('/.+=.+/',$gattr)){
                            $gtmp = explode('=',$gattr);
                            if($gtmp[0] == 'group'){
                                $gtmp[1] = trim(str_replace('"','',$gtmp[1]));
                                $widget_group = $mdl_groups->getRow('*',array('groups'=>$gtmp[1]));
                                if(!empty($widget_group)){
                                    foreach(explode(',',$widget_group['widgets']) as $widgets_item_code){
                                        $gtmp_data['group'][] = $mdl_widgets->getRow('*',array('code'=>$widgets_item_code));
                                    }
                                }
                            }
                            if($gtmp[0] == 'id'){
                                $gtmp[1] = trim(str_replace('"','',$gtmp[1]));
                                $group_code = explode(',',$gtmp[1]);
                                foreach($group_code as $code){
                                    $ids = explode(':',$code);
                                    $instantiation_data = $mdl_instantiation->getRow('name,data',array('id'=>$ids[1],'status'=>'0'));
                                    $gtmp_data['group'][$ids[2]]['id'] = $ids[1];
                                    $gtmp_data['group'][$ids[2]]['name'] = $instantiation_data['name'];
                                }
                            }
                        }
                    }
                    $widgets_data[] = $gtmp_data;
                }
            }
            if(!empty($match[0])){
                foreach($match[0] as $key=>$value){
                    $tmp_str = preg_replace('/\<\{(.*)\}\>/','$1',$value);
                    $tmp_arr = explode(' ',$tmp_str);
                    $tmp_data = array(
                        'line_number'=>$line_number,
                        'line'=>$line,
                    );
                    foreach($tmp_arr as $attr){
                        if(preg_match('/.+=.+/',$attr)){
                            $tmp = explode('=',$attr);
                            $tmp[1] = trim(str_replace('"','',$tmp[1]));
                            if($tmp[0] == 'code'){
                                $widget = $mdl_widgets->getRow('*',array('code'=>$tmp[1],'type|noequal'=>'3'));
                                if(!empty($widget)){
                                    $tmp_data[$tmp[0]] = $tmp[1];
                                    $tmp_data['tmp_name'] = $widget['tmp_name'];
                                    $tmp_data['type'] = $widget['type'];
                                    $tmp_data['screen'] = $widget['screen'] == '0'?'pc':'mobile';
                                    $tmp_data['file_path'] = $widget['file_path'];
                                    $tmp_data['widgets_id'] = $widget['id'];
                                }else{
                                    $tmp_data = array();
                                }
                            }
                            if($tmp[0] == 'id'){
                                $instantiation_data = $mdl_instantiation->getRow('name,data',array('id'=>$tmp[1],'status'=>'0'));
                                $tmp_data['id'] = $tmp[1];
                                $tmp_data['name'] = $instantiation_data['name'];
                            }
                        }
                    }
                    $widgets_data[] = $tmp_data;
                }
            }
        }
        return $widgets_data;
    }
    public function set_content($type='pc',$file_path='',$contents,&$msg){
        if(!$contents){
            $msg = '文件没有内容';
            return false;
        }
        if($file_path==''){
            $msg = '没有找到文件';
            return false;
        }
        $theme_dir = $type=='mobile' ? vmc::singleton('mobile_theme_base') ->get_default():vmc::singleton('site_theme_base') ->get_default();
        if($type == 'mobile'){
            $current_theme_dir = vmc::get_themes_m_root_dir().'/' .$theme_dir;
        }else{
            $current_theme_dir = vmc::get_themes_root_dir().'/' .$theme_dir;
        }
        $real_file_path = $current_theme_dir.$file_path;
        $old = file_get_contents($real_file_path);
        if($old != $contents){
            $file_config = $this->get_config($type);
            $file_config = utils::array_change_key($file_config ,'value');
            $use = $file_config[substr($file_path ,1)]['use'];
            $history=array(
                'templ_type'=>$type,
                'templ_dir' =>$theme_dir,
                'templ_path' =>$file_path,
                'templ_name'=>$use,
                'content'=>$contents,
                'createtime'=>time()
            );
            if(!app::get('widgets')->model('log')->save($history)){
                $msg = '历史记录保存失败';
                return false;
            }
        }
        $file_path = $current_theme_dir.$file_path;
        return $file_content = file_put_contents($file_path,$contents);
    }
    public function create_dir($type='pc',$name='',$current = '/',&$msg){
        $name = trim($name);
        if($name == ''){
            $msg = '没有文件夹名';
            return false;
        }
        if($type == 'mobile'){
            $current_theme_dir = vmc::get_themes_m_root_dir().'/' .vmc::singleton('mobile_theme_base') ->get_default();
        }else{
            $current_theme_dir = vmc::get_themes_root_dir().'/' .vmc::singleton('site_theme_base') ->get_default();
        }
        $current_dir = $current_theme_dir.rawurldecode($current);
        if(false === stripos(realpath($current_dir) ,realpath($current_theme_dir))){
            return false;
        }
        $dir = $current_dir.$name;
        if(!is_dir($dir)){
            return utils::mkdir_p($dir ,0755);
        }else{
            $msg = '文件夹已存在';
            return false;
        }
    }
    //创建模板文件
    public function create_file($type='pc',$tpl_type,$name='',$current = '/',$use='', &$msg){
        $name = trim($name);
        if($name == ''){
            $msg = '没有文件名';
            return false;
        }
        if($type == 'mobile'){
            $theme =vmc::singleton('mobile_theme_base') ->get_default();
            $current_theme_dir = vmc::get_themes_m_root_dir().'/' .$theme;
        }else{
            $theme =vmc::singleton('site_theme_base') ->get_default();
            $current_theme_dir = vmc::get_themes_root_dir().'/' .$theme;
        }
        $current_dir = $current_theme_dir.rawurldecode($current);
        if(false === stripos(realpath($current_dir) ,realpath($current_theme_dir))){
            return false;
        }
        $file = $current_dir.$name.'.html';
        if(file_exists($file)){
            $msg = '文件已存在';
            return false;
        }
        if(file_put_contents($file,' ')){
            $msg = '文件创建成功';
            $this ->write_config($current_theme_dir.'/config.xml' ,substr(rawurldecode($current.$name.'.html') ,1),$tpl_type, $use);
            $type=='pc' ? vmc::singleton('site_theme_install')->monitor_change($theme) : vmc::singleton('mobile_theme_install')->monitor_change($theme);
            return true;
        }else{
            $msg = '文件保存失败';
            return false;
        }
    }
    //删除模板文件
    public function remove_file($type='pc',$file_name = '',$current = '/',&$msg){
        $file_name = trim($file_name);
        if($file_name == ''){
            $msg = '没有文件名';
            return false;
        }
        if(in_array($file_name,array('index.html','config.xml','default.xml'))){
            $msg = '禁止删除';
            return false;
        }
        if($type == 'mobile'){
            $current_theme_dir = vmc::get_themes_m_root_dir().'/' .vmc::singleton('mobile_theme_base') ->get_default();
        }else{
            $current_theme_dir = vmc::get_themes_root_dir().'/' .vmc::singleton('site_theme_base') ->get_default();
        }
        $current_dir = $current_theme_dir.rawurldecode($current);
        if(false === stripos(realpath($current_dir) ,realpath($current_theme_dir))){
            return false;
        }
        $file = $current_dir.$file_name;
        if(file_exists($file)){
            $flag = unlink($file);
            if($flag){
                $msg = '文件删除成功';
                return true;
            }else{
                $msg = '文件删除失败';
                return false;
            }
        }else{
            $msg = '文件不存在';
            return false;
        }
    }


    public function write_config($config_file ,$file_path,$tpl_type ,$use){
        $dom=new DOMDocument('1.0');
        $dom->load($config_file);
        $views =$dom->getElementsByTagName('views');
        $views=$views->item(0);
        $file = $dom->createElement('file');
        $file->setAttribute('type',$tpl_type);
        $file->setAttribute('use',$use);

        $file = $views->appendChild($file);
        $file->appendChild($dom->createTextNode($file_path));
        $dom->formatOutput = true;
        $dom->save($config_file);
    }

    public function get_config($type){
        $theme_base = $type=='pc' ? vmc::singleton('site_theme_base'):vmc::singleton('mobile_theme_base');
        $theme = $theme_base->get_default();
        $type=='pc' ? vmc::singleton('site_theme_install')->monitor_change($theme) : vmc::singleton('mobile_theme_install')->monitor_change($theme);
        $views = $theme_base->get_theme_views($theme);
        $result= array();
        foreach($views as $k=>$view){
            foreach($view as $file){
                $file['type'] = $k;
                $result[] =$file;
            }
        }
        return $result;
    }
}
