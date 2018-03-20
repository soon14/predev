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




class site_application_module extends base_application_prototype_xml
{
    var $xml='site.xml';
    var $xsd='site_content';
    var $path = 'module';

    public function current(){
        $this->current = $this->iterator()->current();
        return $this;
    }

    final public function install()
    {
        $modules = $this->parse_moduels_params($this->current);
        $this->insert_modules($modules);

        if(is_array($this->current['allow_seo'])){
            foreach($this->current['allow_seo'] AS $param){
                $this->insert_seo($this->parse_seo_params($param));
            }
        }

        vmc::singleton('site_module_base')->create_site_config();   //生成config文件
    }//End Function

    final public function update($app_id)
    {
        $this->clear_by_app($app_id);
        $this->update_modified = time();
        foreach($this->detect($app_id) as $name=>$item){
            $item->install();
        }
        return true;
    }//End Function



    private function assoc_allow_menu($params)
    {
        if(!is_array($params))  return '';
        foreach($params AS $param){
            $data[] = implode(':', $param);
        }
        return implode('|', $data);
    }//End Function

    private function insert_modules($data)
    {

        $data['update_modified'] = $this->update_modified;
        return app::get('site')->model('modules')->save($data);
    }//End Function

    private function parse_moduels_params($params)
    {
        $data['app'] = $this->target_app->app_id;
        $data['ctl'] = $params['controller'];
        $data['path'] = ($params['name']) ? $params['name'] : $params['controller'];
        $data['extension'] = ($params['extension']) ? $params['extension'] : '';
        $data['title'] = $params['title'];
        $data['allow_menus'] = $this->assoc_allow_menu($params['allow_menu']);
        $data['is_native'] = 'true';
        $data['enable'] = 'true';
        return $data;
    }//End Function


    private function insert_seo($data)
    {

        return app::get('site')->model('seo')->insert($data);
    }//End Function

    private function parse_menus_params($param)
    {
        $data['title'] = $param['title'];
        $data['app'] = $this->target_app->app_id;
        $data['ctl'] = $this->current['controller'];
        $data['act'] = ($param['act']) ? $param['act'] : '';
        $data['custom_url'] = ($param['custom_url']) ? $param['custom_url'] : '';
        $data['hidden'] = ($param['hidden'] === 'true') ? 'true' : 'false';
        $data['display_order'] = ($param['display_order'] > 0) ? $param['display_order'] : 0;
        if(is_array($param['params'])){
            foreach($param['params'] AS $v){
                $tmp[$v['id']] = $v['value'];
            }
            $data['params'] = $tmp;
            if(empty($param['config'])) $data['config'] = $data['params'];
        }
        return $data;
    }//End Function

    private function parse_seo_params($param){
        $data['title'] = $param['title'];
        $data['app'] = $this->target_app->app_id;
        $data['ctl'] = $this->current['controller'];
        $data['act'] = ($param['act']) ? $param['act'] : '';
        $data['seo_key'] = ($param['seo_key']) ? $param['seo_key'] : '';
	if(is_array($param['config'])){
            foreach($param['config'] AS $key=>$val){
                $data['config'][$key]['id'] = $val['id'];
                $data['config'][$key]['value'] = $val['value'];
            }
	}
        $data['param'] = $param['param'][0];
        $data['hidden'] = ($param['hidden'] === 'true') ? 'true' : 'false';
        return $data;
    }

    function clear_by_app($app_id){
        if(!$app_id){
            return false;
        }
        app::get('site')->model('modules')->delete(array(
            'app'=>$app_id));
        app::get('site')->model('seo')->delete(array(
            'app'=>$app_id));
    }

}//End Class
