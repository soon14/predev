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
class widgets_widgets
{
    public function __construct (&$app)
    {
        $this->app = $app;
    }

    public function create_widgets_file($data){
        $file = $this ->get_widgets_file($data);
        if(!file_put_contents($file ,$data['html_code'])){
            logger::error('模块文件写入失败：'.$file);
            return false;
        }

        return true;
    }

    public function get_widgets_content($data){
        $file = $this ->get_widgets_file($data);
        if(!is_file($file)){
            return false;
        }
        return file_get_contents($file);
    }


    public function get_widgets_file($data ,$full=true){
        if($data['screen'] == '1'){
            $widgets_dir = ($full ?vmc::get_themes_m_root_dir().'/' :'').vmc::singleton('mobile_theme_base') ->get_default().'/widgets/';
        }else{
            $widgets_dir = ($full ?vmc::get_themes_root_dir().'/' :'').vmc::singleton('site_theme_base') ->get_default().'/widgets/';
        }
        $category = $this ->app ->model('widgets_category') ->getRow('dir' ,array('cid'=>$data['cid']));
        $dir = $widgets_dir.$category['dir'];
        if(!is_dir($dir) && $full){
            utils::mkdir_p($dir ,0755);
        }
        $file = $widgets_dir.$category['dir'].$data['code'].'.html';
        return $file;
    }
    //获取实例化widget
    public function get_instantiation_widget($widgets_id,$id,&$msg=''){
        if(!$widgets_id){
            $msg = '没有板块编码';
            return false;
        }
        $mdl_widgets = app::get('widgets')->model('widgets');
        $data = $mdl_widgets->getRow('*',array('id'=>$widgets_id));
        if(empty($data)){
            $msg = '没有此板块';
            return false;
        }
        if($id){
            $mdl_widgets = app::get('widgets')->model('instantiation');
            $data['instantiation'] = $mdl_widgets->getRow('*',array('id'=>$id));
            if(!empty($data['instantiation']['data']['goods'])){
                $goods_list = app::get('b2c')->model('goods')->getList('*',array('goods_id'=>$data['instantiation']['data']['goods_id']));
                $goods_list = utils::array_change_key($goods_list ,'goods_id');
                $data['instantiation']['data']['goods_list'] =$goods_list;
            }
        }else{
            $data['instantiation'] = array();
        }
        return $data;
    }
    //存储实例化widget
    public function set_instantiation_widget(&$data,&$msg=''){
        $temp_file = $data['file_path'];
        if(!$data['file_path']){
            $msg = '没有指定文件路径';
            return false;
        }
        $mdl_widgets = app::get('widgets')->model('widgets');
        $widgets = $mdl_widgets->getRow('*',array('id'=>$data['widgets_id']));
        if(!$widgets){
            $msg = '没有此板块';
            return false;
        }
        switch($widgets['screen']){
            case '1':
                $widgets_path = vmc::singleton('mobile_theme_base')->get_default().'/widgets/';
                if(strpos($data['file_path'],$widgets_path)){
                    $file = $data['file_path'];
                }else{
                    $file = vmc::singleton('mobile_theme_base')->get_default().$data['file_path'];
                }
                $file = vmc::get_themes_m_root_dir().'/'.$file;
            break;
            default:
                $widgets_path = vmc::singleton('site_theme_base')->get_default().'/widgets/';
                if(strpos($data['file_path'],$widgets_path)){
                    $file = $data['file_path'];
                }else{
                    $file = vmc::singleton('site_theme_base')->get_default().$data['file_path'];
                }
                $file = vmc::get_themes_root_dir().'/'.$file;
            break;
        }
        $mdl_instantiation = app::get('widgets')->model('instantiation');
        if(!isset($data['data'])){
            $data['data'] = array();
        }
        if(!empty($data['data']['goods'])){
            $goods_list = app::get('b2c')->model('goods')->getList('name ,image_default_id,goods_id',array('goods_id'=>$data['data']['goods_id']));
            $goods_list = utils::array_change_key($goods_list,'goods_id');
            $sorts = array();
            foreach($data['data']['goods'] as $k=>$v){
                if($v['custom_default_id']==$goods_list[$v['goods_id']]['image_default_id']){
                    $data['data']['goods'][$k]['custom_default_id']='';
                }
                if(trim($v['name']) ==trim($goods_list[$v['goods_id']]['name'])){
                    $data['data']['goods'][$k]['name']='';
                }
                $sorts[$k]=$v['sort'];
            }
            array_multisort($sorts, SORT_ASC, SORT_NUMERIC , $data['data']['goods']);
        }
        $old =null;
        if($data['id']){
            $old = $mdl_instantiation->dump($data['id']);
            unset($old['last_modify'] ,$old['createtime']);
        }
        $flag = $mdl_instantiation->save($data);
        if($flag){
            $new  = $mdl_instantiation->dump($data['id']);
            unset($new['last_modify'],$new['createtime']);

            if($old !=$new){
                $new['widgets'] = $widgets;
                $data_log = array(
                    'type' =>'instantiation',
                    'target_id' =>$new['id'],
                    'data' =>$new,
                    'createtime' =>time()
                );
                if(!app::get('widgets')->model('data_log')->save($data_log)){
                    $msg ='板块修改记录保存失败';
                    return false;
                }

            }
            $file_content = file_get_contents($file);
            $new_file_array = array();
            if($_POST['group_index'] !== ''){
                $file_array = preg_split('/\<\{widgets\s+group=[^\}\>]*\}\>/',$file_content);
                preg_match_all('/\<\{widgets\s+group=[^\}\>]*\}\>/',$file_content,$widgets_array);
                $widgets_array = $widgets_array[0];
                $tmp_group = preg_replace('/\<\{(.*)\}\>/','$1',$widgets_array[$data['group_index']]);
                $tmp_arr = explode(' ',$tmp_group);
                $group = '';
                $new_id = $data['code'].':'.$data['id'].':'.$data['index'];
                foreach($tmp_arr as $group_attr){
                    if(preg_match('/.+=.+/',$group_attr)){
                        $gtmp = explode('=',$group_attr);
                        if($gtmp[0] == 'group'){
                            $group = trim(str_replace('"','',$gtmp[1]));
                        }
                        if($gtmp[0] == 'id'){
                            $gtmp[1] = trim(str_replace('"','',$gtmp[1]));
                            $group_code = explode(',',$gtmp[1]);
                            foreach($group_code as $key=>$code){
                                $ids = explode(':',$code);
                                if($ids[0] == $data['code'] && $ids[2] == $data['index']){
                                    $group_code[$key] = $new_id;
                                    $new_id = '';
                                }
                            }
                            if($new_id != ''){
                                array_push($group_code,$new_id);
                            }
                        }
                    }
                }
                $new_widget = '<{widgets group="'.$group.'" id="'.implode(',',$group_code).'"}>';
                $widgets_array[$data['group_index']] = $new_widget;
                foreach($file_array as $key=>$item){
                    array_push($new_file_array,$item);
                    if($widgets_array[$key]){
                        array_push($new_file_array,$widgets_array[$key]);
                    }
                }
            }else{
                $file_array = preg_split('/\<\{widgets\s+code=[^\}\>]*\}\>/',$file_content);
                preg_match_all('/\<\{widgets\s+code=[^\}\>]*\}\>/',$file_content,$widgets_array);
                $new_widget = '<{widgets code="'.$data['code'].'" id="'.$data['id'].'"}>';
                $widgets_array = $widgets_array[0];
                $widgets_array[$data['index']] = $new_widget;
                foreach($file_array as $key=>$item){
                    array_push($new_file_array,$item);
                    if($widgets_array[$key]){
                        array_push($new_file_array,$widgets_array[$key]);
                    }
                }
            }
            $type =$widgets['screen'] =='1' ?'mobile' :'pc';
            $flag = vmc::singleton('widgets_template')->set_content($type ,$temp_file,implode('',$new_file_array) ,$msg);
        }
        return $flag;
    }
}
