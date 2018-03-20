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
* 安装资源文件类,查找各个APP下面的operatorlog.xml 遍历
*/
class operatorlog_application_register extends base_application_prototype_xml
{
    /**
    * @var string 文件名称
    */
    var $xml='operatorlog.xml';
    /**
    * @var string xml文件对应的schema文件
    */
    var $xsd='operatorlog_content';
    /**
    * @var string 路径
    */
    var $path = 'register';
    /**
    * 迭代找到当前类实例
    * @return object 返回当前类实例
    */
    public function current(){
        $this->current = $this->iterator()->current();
        return $this;
    }
    /**
    * 安装资源数据
    * @access final
    */
    final public function install()
    {
        //$this->target_app->app_id;
        $data = $this->current;
        $data['app'] = $this->target_app->app_id;
        $arr_method = array('get'=>'get','post'=>'post');
        $data['method'] = $arr_method[strtolower($data['method'])] ? $arr_method[strtolower($data['method'])] : 'post';
        app::get('operatorlog')->model('register')->insert($data);
    }//End Function

    /**
    * 卸载资源数据 register表中对应APP的数据删除
    * @param string $app_id appid
    */
    function clear_by_app($app_id){
        if(!$app_id){
            return false;
        }
        app::get('operatorlog')->model('register')->delete(array('app'=>$app_id));
    }

}//End Class