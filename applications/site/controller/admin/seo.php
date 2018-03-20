<?php

class site_ctl_admin_seo extends desktop_controller
{

    public function index(){
        $this->finder('site_mdl_seo', array(
            'title' =>  'SEO优化',
            'base_filter' => array(),
            'use_buildin_set_tag' => false,
            'use_buildin_recycle' => false,
            'use_buildin_export' => false,
            'use_buildin_selectrow'=>false,
            'actions'=>array(
                array(
                    'icon'=>'fa-cog',
                    'label' => '默认配置',
                    'href' => 'index.php?app=site&ctl=admin_seo&act=set_defaut_seo',
                ),
            ),

        ));
    }

    function set_defaut_seo(){
        $seo['param'] = vmc::singleton('site_seo_base')->get_default_seo();
        $this->pagedata['param'] = $seo['param'];
        $this->display('admin/seo/default.html');
    }


    public function saveseo($id){
        $this->begin();
        if($id == 'default'){
            if($this->app->setConf('page_default_title',$_POST['seo_title'])&&$this->app->setConf('page_default_keywords',$_POST['seo_keywords'])&&$this->app->setConf('page_default_description',$_POST['seo_content'])){
                $this->end(true, '保存成功');
            }else{
                $this->end(false, '保存失败');
            }
        }
        $data['param'] = $_POST;
        $data['update_modified'] = time();
        if($id > 0){
            if(app::get('site')->model('seo')->update($data, array('id'=>$id))){
                $this->end(true, '保存成功');
            }else{
                $this->end(false, '保存失败');
            }
        }else{
            if(app::get('site')->model('seo')->insert($data)){
                $this->end(true, '添加成功');
            }else{
                $this->end(false, '添加失败');
            }
        }
    }
}//End Class
