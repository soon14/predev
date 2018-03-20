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
class widgets_ctl_admin_template extends desktop_controller
{
    public function __construct (&$app)
    {
        $this->app = $app;
        $this ->template_obj = vmc::singleton('widgets_template');
    }

    public function manage($type='pc' ,$current =''){
        $this ->pagedata['type'] =$type;
        $this ->pagedata['list'] = $this ->template_obj ->get_file_list($type ,$current);
        $this->pagedata['current_dir'] = str_replace(ROOT_DIR,'',$this ->template_obj->current_dir);
        $this ->pagedata['path_node'] = $this ->template_obj ->get_path_node($current);
        $current .= ($current==''? '/' :'');
        $this ->pagedata['current'] =rawurlencode($current);
        $this ->page('admin/template/index.html');
    }
    //创建模板文件
    public function add_template($type='pc',$file_path=''){
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] = $file_path;
        $this ->page('admin/template/edit.html');
    }
    //创建文件夹
    public function create_dir(){
        $type = $_POST['type'];
        $current = $_POST['current'];
        $this->begin("index.php?app=widgets&ctl=admin_template&act=manage&p[0]=$type&p[1]=$current");
        $flag = $this ->template_obj ->create_dir($_POST['type'],$_POST['name'],$_POST['current'],$msg);
        $this->end($flag,$msg);
    }
    //创建模板文件
    public function create_file(){
        $type = $_POST['type'];
        $current = $_POST['current'];
        $this->begin("index.php?app=widgets&ctl=admin_template&act=manage&p[0]=$type&p[1]=$current");
        $flag = $this->template_obj->create_file($_POST['type'],$_POST['tpl_type'],$_POST['name'],$_POST['current'],$_POST['use'],$msg);
        $this->end($flag,$msg);
    }
    //删除模板
    public function remove($type='pc',$file_name='',$current=''){
        $this->begin("index.php?app=widgets&ctl=admin_template&act=manage&p[0]=$type&p[1]=$current");
        $flag = $this ->template_obj ->remove_file($type,$file_name,$current,$msg);
        $this->end($flag,$msg);
    }
    //编辑模板
    public function edit($type='pc',$file_path=''){
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] = $file_path;
        $this ->page('admin/template/edit.html');
    }
    //编辑模板
    public function edit_file($type='pc',$file_path=''){
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] = $file_path;
        $this->pagedata['contents'] = $this->template_obj->get_content($type,$file_path);
        $this ->page('admin/template/edit_file.html');
    }
    //获取模板源码
    public function get_page_code($type='pc',$file_path=''){
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] = $file_path;
        $this->pagedata['contents'] = $this->template_obj->get_content($type,$file_path);
        echo $this->fetch('admin/template/page_code.html');
    }

    //获取历史修改
    public function get_history($file_path='',$type='pc'){
        $page = $_GET['page']?:1;
        $limit = 20;
        $theme_obj = $type=='pc' ? vmc::singleton('site_theme_base'):vmc::singleton('mobile_theme_base');
        $current_theme =  $theme_obj->get_default();
        if(substr($file_path ,-1,1)=='_'){
            $file_path = substr($file_path ,0,strlen($file_path)-1);
        }
        $filter = array(
            'templ_type' =>$type,
            'templ_dir' =>$current_theme,
            'templ_path' =>$file_path
        );
        $count = $this->app->model('log')->count($filter);
        $this->pagedata['pager']['total'] = ceil($count / $limit);
        $this->pagedata['pager']['current']  = $page;
        $this->pagedata['pager']['token'] = time();
        $this->pagedata['pager']['link'] = 'index.php?app=widgets&ctl=admin_template&act=get_history&p[0]='.rawurlencode($file_path).'&p[1]='.$type.'&page='.$this->pagedata['pager']['token'];
        $this->pagedata['history'] = $this->app->model('log')->getList('*',$filter ,($page-1)*$limit , $limit ,'createtime desc');
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] =rawurlencode($file_path);
        echo $this->fetch('admin/template/history.html');
    }

    public function show_history_code($log_id){
        $log = $this ->app->model('log')->dump($log_id);
        $this ->pagedata['log'] = $log;
        echo $this->fetch('admin/template/history_code.html');
    }

    public function recovery($log_id){
        $log = $this ->app->model('log')->dump($log_id);
        $this ->begin();
        $file = $this->template_obj->set_content($log['templ_type'],$log['templ_path'],$log['content'],$msg);
        if(!$file){
            $this->end(false ,$msg);
        }
        $this->end(true);
    }

    //获取页面内板块树
    public function get_widgets($type='pc',$file_path,$file_type){
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] = $file_path;
        $this->pagedata['widgets_data'] = $this->template_obj->get_widgets($type,$file_path,$file_type);
        echo $this->fetch('admin/template/widgets_list.html');
    }
    public function edit_widget($index=0,$widgets_id,$id,$file_path='',$group_index){
        $type = $_GET['type'] ? :'pc';
        $msg = '';
        $widgets_obj = vmc::singleton('widgets_widgets');
        $widget = $widgets_obj->get_instantiation_widget($widgets_id,$id,$msg);
        if(!$widget){
            echo $msg;
            exit;
        }
        $this->pagedata['widget'] = $widget;
        $this->pagedata['index'] = $index;
        $this->pagedata['type'] = $type;
        $this->pagedata['widgets_id'] = $widgets_id;
        $this->pagedata['id'] = $id;
        $this->pagedata['file_path'] = rawurlencode($file_path);
        $this->pagedata['group_index'] = $group_index;
        $this->page('admin/template/widget_edit.html');
    }

    public function get_instantiation($index=0,$widgets_id,$id,$file_path='',$group_index){
        $type = $_GET['type'] ? :'pc';
        $msg = '';
        $widgets_obj = vmc::singleton('widgets_widgets');
        $widget = $widgets_obj->get_instantiation_widget($widgets_id,$id,$msg);
        if(!$widget){
            echo $msg;
            exit;
        }
        $this->pagedata['index'] = $index;
        $this->pagedata['widget'] = $widget;
        $this->pagedata['type'] = $type;
        $this->pagedata['file_path'] = rawurlencode($file_path);
        $this->pagedata['group_index'] = $group_index;
        $this->display('admin/instantiation/edit.html');
    }


    public function get_instantiation_history($id){
        if($id){
            $page = $_GET['page']?:1;
            $limit = 20;
            $filter = array(
                'type' =>'instantiation',
                'target_id' =>$id,
            );
            $count = $this->app->model('data_log')->count($filter);
            $this->pagedata['pager']['total'] = ceil($count / $limit);
            $this->pagedata['pager']['current']  = $page;
            $this->pagedata['pager']['token'] = time();
            $this->pagedata['pager']['link'] = 'index.php?app=widgets&ctl=admin_template&act=get_instantiation_history&p[0]='.rawurlencode($file_path).'&p[1]='.$type.'&page='.$this->pagedata['pager']['token'];
            $this->pagedata['history'] = $this->app->model('data_log')->getList('*',$filter ,($page-1)*$limit , $limit ,'createtime desc');
        }
        $this->display('admin/instantiation/history.html');
    }

    public function show_instantiation_history($log_id){
        $log = $this ->app->model('data_log')->dump($log_id);
        if($log){
            $data=$log['data']['widgets'];
            $data['instantiation'] = $log['data'];
            if(!empty($data['instantiation']['data']['goods'])){
                $goods_list = app::get('b2c')->model('goods')->getList('*',array('goods_id'=>$data['instantiation']['data']['goods_id']));
                $goods_list = utils::array_change_key($goods_list ,'goods_id');
                $data['instantiation']['data']['goods_list'] =$goods_list;
            }
        }else{
            $data['instantiation'] = array();
        }
        $this ->pagedata['widget'] = $data;
        $this->display('admin/instantiation/history_show.html');
    }

    public function recovery_instantiation($log_id){
        $log = $this ->app->model('data_log')->dump($log_id);
        $this->begin();
        if(!$log){
            $this->end(false);
        }
        $new = $data =  $log['data'];
        $mdl_instantiation = $this->app->model('instantiation');
        $old  = $mdl_instantiation->dump($new['id']);
        unset($new['last_modify'],$new['createtime'] ,$new['widgets']);
        unset($old['last_modify'],$old['createtime']);
        if($old !=$new){
            $data_log = array(
                'type' =>'instantiation',
                'target_id' =>$new['id'],
                'data' =>$data,
                'createtime' =>time()
            );
            if(!app::get('widgets')->model('data_log')->save($data_log)){
                $this->end(false, '板块修改记录保存失败');
            }

        }
        if(!$mdl_instantiation->save($new)){
            $this->end(false, '恢复失败');
        }
        $this->end(true);
    }

    public function instantiation_widget(){
        $this->begin();
        if(!$_POST['createtime']){
            $_POST['createtime'] = time();
        }
        $msg = '';
        $widgets_obj = vmc::singleton('widgets_widgets');
        $save_data = $_POST;
        $save_data['file_path'] = rawurldecode($save_data['file_path'] );
        $flag = $widgets_obj->set_instantiation_widget($save_data,$msg);
        if($flag){
            $this->end(true,$save_data);
        }else{
            $this->end(false,$msg);
        }
    }
    public function save(){
        $this->begin();
        //$pre_content = $this->template_obj->get_content($_POST['type'],$_POST['file_path']);

        $file = $this->template_obj->set_content($_POST['type'],$_POST['file_path'],$_POST['content'],$msg);
        if($file){
            $this->end(true);
        }else{
            $this->end(false,$msg);
        }
    }

    public function edit_node_template($type='pc',$node_id){
        $node = app::get('content')->model('article_nodes')->dump($node_id);
        $node['setting']= is_array($node['setting']) ? $node['setting'] :unserialize($node['setting']);
        $site_template = $node['setting']['site_template'];
        $mobile_template = $node['setting']['mobile_template'];
        $file_path = $type=='pc' ? $site_template:$mobile_template;
        $file_path= '/'.$file_path;
        $this->edit($type ,$file_path);
    }
}
