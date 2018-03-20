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


class widgets_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->mdl_widgets = $app->model('widgets');
        $this->mdl_instantiation = $app->model('instantiation');
    }
    public function function_widgets($params,&$smarty){
        if($params['group']){
            return $this->_widgets_group($params,$smarty);
        }
        if(!$params['code']){
            return '请指定挂件模板';
        }
        if(!$params['id']){
            $params['id'] = 0;
        }
        $render = vmc::singleton('base_render');
        if($params['parent_data']){
            $render ->pagedata['parent_data'] = $params['parent_data'];
        }
        $data = $this->mdl_widgets->getRow('*',array('code'=>$params['code']));
        if($data['screen'] == '1'){
            $screen = '移动端';
            $widgets_tmpl_file = vmc::get_themes_m_root_dir().'/'.$data['file_path'];
        }else{
            $screen = 'PC端';
            $widgets_tmpl_file = vmc::get_themes_root_dir().'/'.$data['file_path'];
        }
        if(!is_file($widgets_tmpl_file)){
            return '没有找到挂件模板('.$params['code'].'.html)，请在'.$screen.'模板的widgets目录下检查';
        }
        $instantiation_data = $this->mdl_instantiation->getRow('link,name,data,nums,image,background_image',array('id'=>$params['id'],'status'=>'0'));
        if(!empty($instantiation_data)){
            $data['name'] = $instantiation_data['name'];
            $data['data'] = $instantiation_data['data'];
            $data['nums'] = $instantiation_data['nums'];
            $data['image'] = $instantiation_data['image'];
            $data['background_image'] = $instantiation_data['background_image'];
            $data['link'] = $instantiation_data['link'];
            if($data['type'] == '1'){//丰富商品数据
                $data = $this ->_goods_type($data);
            }
            if($data['nums']){
                if(!empty($data['data']['goods'])){
                    $data['data']['goods'] = array_slice($data['data']['goods'] ,0 ,$data['nums']);
                }
                if(!empty($data['data']['pic'])){
                    $data['data']['pic'] = array_slice($data['data']['pic'] ,0 ,$data['nums']);
                }
            }
            $render ->pagedata['data'] = $data;
            if($data['screen'] == '1'){
                return $render->fetch($data['file_path'],null,false,true);
            }else{
                return $render->fetch($data['file_path'],null,true,false);
            }
        }else{
            return '';
        }

    }

    /*
     * 丰富商品数据
     */
    private function _goods_type($widgets){
        if(!empty($widgets['data']['goods_id'])){
            $mdl_goods = app::get('b2c')->model('goods');
            $goods_list = $mdl_goods->getList('*',array('goods_id'=>$widgets['data']['goods_id'] ,'marketable'=>'true'));
            vmc::singleton('b2c_goods_stage')->gallery($goods_list,$widgets['screen']);
            $goods_list = utils::array_change_key($goods_list ,'goods_id');
            foreach($widgets['data']['goods'] as $k=>$v){
                if(!$goods_list[$v['goods_id']]){
                    unset($widgets['data']['goods'][$k]);
                    continue;
                }
                $v['name'] = $v['name'] ? : $goods_list[$v['goods_id']]['name'];
                $v['custom_default_id'] = $v['custom_default_id'] ? : $goods_list[$v['goods_id']]['image_default_id'];
                $widgets['data']['goods'][$k] =  array_merge($goods_list[$v['goods_id']],$v);
            }
        }
        return $widgets;
    }
    private function _widgets_group($params,&$smarty){
        $code = $params['group'];
        $mdl_group = app::get('widgets')->model('groups');
        $group = $mdl_group->getRow($code);
        return $widgets;
    }
}
