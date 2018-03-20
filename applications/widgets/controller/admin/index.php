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

class widgets_ctl_admin_index extends desktop_controller
{
    public function index()
    {

        $this->finder('widgets_mdl_widgets', array(
            'title' => ('板块管理') ,
            'use_buildin_recycle'=>$this ->has_permission('widgets_delete'),
            'actions' => array(
                array(
                    'label' => ('添加板块') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=widgets&ctl=admin_index&act=add',
                ) ,
                /*
                array(
                    'label' => ('打包板块数据') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=widgets&ctl=admin_index&act=zip',
                ) ,
                */
            )
        ));
    }
    public function add(){
        $selectmaps = vmc::singleton ('widgets_category')->get_selectmaps ();
        $this->pagedata['selectmaps'] = $selectmaps;
        $this->page('admin/edit.html');
    }
    public function zip(){
        echo 'zip';
    }

    public function ajax_get_tpl(){
        $tpl = array(
            '0' =>$this->app->app_dir.'/view/admin/common/pic.html',
            '1' =>$this->app->app_dir.'/view/admin/common/goods.html',
            '2' =>$this->app->app_dir.'/view/admin/common/custom.html',
        );
        $content = file_get_contents($tpl[$_GET['type']]);
        $this ->splash('success' ,'' , $content);
    }
    public function edit($id){
        $selectmaps = vmc::singleton ('widgets_category')->get_selectmaps ();
        $this->pagedata['selectmaps'] = $selectmaps;
        $mdl_widgets = $this->app->model('widgets');
        $widgets = $mdl_widgets->getRow('*',array('id'=>$id));
        $html_code = vmc::singleton('widgets_widgets')->get_widgets_content($widgets);
        $this->pagedata['html_code'] = $html_code ?$html_code :'';
        $this->pagedata['widgets'] = $widgets;
        $this->pagedata['id'] = $id;
        $this->page('admin/edit.html');
    }
    public function get_edit($id){
        $selectmaps = vmc::singleton ('widgets_category')->get_selectmaps ();
        $this->pagedata['selectmaps'] = $selectmaps;
        $mdl_widgets = $this->app->model('widgets');
        $widgets = $mdl_widgets->getRow('*',array('id'=>$id));
        $this->pagedata['widgets'] = $widgets;
        $this->page('admin/widgets/edit.html');
    }

    public function get_edit_code($id){
        $mdl_widgets = $this->app->model('widgets');
        $widgets = $mdl_widgets->getRow('*',array('id'=>$id));
        $html_code = vmc::singleton('widgets_widgets')->get_widgets_content($widgets);
        echo  $html_code ?$html_code :'';
    }

    public function get_history($id){
        if($id){
            $page = $_GET['page']?:1;
            $limit = 20;
            $filter = array(
                'type' =>'widgets',
                'target_id' =>$id,
            );
            $count = $this->app->model('data_log')->count($filter);
            $this->pagedata['pager']['total'] = ceil($count / $limit);
            $this->pagedata['pager']['current']  = $page;
            $this->pagedata['pager']['token'] = time();
            $this->pagedata['pager']['link'] = 'index.php?app=widgets&ctl=admin_index&act=get_history&p[0]='.rawurlencode($file_path).'&p[1]='.$type.'&page='.$this->pagedata['pager']['token'];
            $this->pagedata['history'] = $this->app->model('data_log')->getList('*',$filter ,($page-1)*$limit , $limit ,'createtime desc');
        }
        $this->display('admin/widgets/history.html');
    }

    public function show_history($log_id){
        $selectmaps = vmc::singleton ('widgets_category')->get_selectmaps ();
        $this->pagedata['selectmaps'] = $selectmaps;
        $log = $this->app->model('data_log')->dump($log_id);
        $widgets = $log['data'];
        $html_code = $widgets['file_content'];
        $this->pagedata['html_code'] = $html_code ?$html_code :'';
        $this->pagedata['widgets'] = $widgets;
        $this->page('admin/widgets/history_show.html');
    }

    public function recovery($log_id){
        $this ->begin();
        $log = $this->app->model('data_log')->dump($log_id);
        $new = $log['data'];
        $html_code = $new['file_content'];
        unset($new['content']);

        $mdl_widgets = $this->app->model('widgets');
        if(!$mdl_widgets->save($new)){
            $this->end(false, '恢复失败');
        }
        $old = $mdl_widgets->dump($new['id']);
        unset($old['createtime'] ,$old['last_modify']);

        $old_content = vmc::singleton('widgets_widgets')->get_widgets_content($old);
        if($old!=$new || $old_content!=$html_code){
            $new['file_content'] =  $html_code;
            $data_log = array(
                'type' =>'widgets',
                'target_id' =>$new['id'],
                'data' =>$new,
                'createtime' =>time()
            );
            if(!app::get('widgets')->model('data_log')->save($data_log)){
                $this->end(false, '板块修改记录保存失败');
            }
        }
        $new['html_code'] =$html_code ;
        $this->end(vmc::singleton('widgets_widgets')->create_widgets_file($new));
    }

    public function copy($id){
        $selectmaps = vmc::singleton ('widgets_category')->get_selectmaps ();
        $this->pagedata['selectmaps'] = $selectmaps;
        $mdl_widgets = $this->app->model('widgets');
        $widgets = $mdl_widgets->getRow('*',array('id'=>$id));
        $html_code = vmc::singleton('widgets_widgets')->get_widgets_content($widgets);
        $max_id = $mdl_widgets->db ->selectrow('SELECT max(id) as max_id FROM `'.$mdl_widgets->table_name(1).'` order by id DESC ');
        unset($widgets['id']);
        $widgets['code'] .= '-'.($max_id['max_id']+1);
        $this->pagedata['copy_id'] = $id;
        $this->pagedata['html_code'] = $html_code ?$html_code :'';
        $this->pagedata['widgets'] = $widgets;
        $this->page('admin/edit.html');
    }

    public function save(){
        $this->begin();
        $mdl_widgets = $this->app->model('widgets');
        if(!$_POST['createtime']){
            $_POST['createtime'] = time();
        }
        switch($_POST['type']){
            case '0':
            //图文展示
                unset($_POST['data']['customer']);
                unset($_POST['data']['goods']);
                unset($_POST['data']['goods_id']);
                $widgets = $_POST;
            break;
            case '1':
            //商品展示
                unset($_POST['data']['customer']);
                unset($_POST['data']['pic']);
                $widgets = $_POST;
            break;
            case '2':
            //自定义代码
                unset($_POST['data']['goods']);
                unset($_POST['data']['goods_id']);
                unset($_POST['data']['pic']);
                $widgets = $_POST;
            break;
            default:
                unset($_POST['data']['goods']);
                unset($_POST['data']['goods_id']);
                unset($_POST['data']['pic']);
                $widgets = $_POST;
            break;
        }
        if(!$widgets['id']){
            $check = $mdl_widgets->getRow('id',array('code'=>$widgets['code']));
            if(!empty($check)){
                $this->end(false,'编码重复');
            }
        }
        $old =null;
        if($widgets['id']){
            $old = $mdl_widgets->dump($widgets['id']);
            unset($old['createtime'] ,$old['last_modify']);
        }
        if($mdl_widgets->save($widgets)){
            $widgets['html_code'] = $widgets['type'] !='2' ?$widgets['html_code'] :'<{$data.data.customer}>';
            $new = $mdl_widgets->dump($widgets['id']);
            unset($new['createtime'] ,$new['last_modify']);
            $old_content = vmc::singleton('widgets_widgets')->get_widgets_content($widgets);
            if($old!=$new || $old_content!=$widgets['html_code']){
                $new['file_content'] =  $widgets['html_code'];
                $data_log = array(
                    'type' =>'widgets',
                    'target_id' =>$new['id'],
                    'data' =>$new,
                    'createtime' =>time()
                );
                if(!app::get('widgets')->model('data_log')->save($data_log)){
                    $this->end(false, '板块修改记录保存失败');
                }
            }
            vmc::singleton('widgets_widgets')->create_widgets_file($widgets);
            $this->end(true ,'操作成功' ,'index.php?app=widgets&ctl=admin_index&act=edit&p[0]='.$widgets['id']);
        }else{
            $this->end(false);
        }
    }

    //ajax异步拉取商品数据
    public function get_goods(){
        $gids = $_POST['goods_id'];
        $this->pagedata['goods'] = app::get('b2c')->model('goods')->getList('goods_id,name,image_default_id,gid', array(
            'goods_id' => $gids,
        ));
        $this->display('admin/get_goods.html');
    }
    //ajax异步拉取图文输入框
    public function get_pic(){
        $this->pagedata['id'] = $_POST['id'];
        $this->display('admin/get_pic.html');
    }
}
