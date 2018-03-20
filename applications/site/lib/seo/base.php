<?php

class site_seo_base
{

    public function __construct($app){
        $this->app = $app;
    }

    function get_default_seo(){
    	return array(
    	    'seo_title'=>$this->app->getConf('page_default_title'),
    	    'seo_keywords'=>$this->app->getConf('page_default_keywords'),
    	    'seo_content'=>$this->app->getConf('page_default_description')
    	);
    }


    final public function get_seo_conf($app,$act,$args=null) {
         $seo = app::get('site')->model('seo')->dump(array('ctl'=>$app,'act'=>$act),'param');
         if(!$seo) $seo['param'] = $this->get_default_seo();
         $seo['param'] = $seo['param'];
         return $seo['param'] = $this->toreplace($seo['param'],$args);
    }

    function toreplace($data,$args){
        if(is_array($data)){
            foreach($data as $key=>$val){
                 if(preg_match_all('/\{([a-z][a-z0-9_]+)\}/i', $val, $matches)){
                     foreach($matches[1] as $v){
                            if(substr($v,0,4)=='ENV_'){
                                $v = substr($v,4);
                                if(is_array($args)){
                                    if(array_key_exists($v, $args)){
                                        $to_replace['{ENV_'.$v.'}'] = $args[$v];
                                    }else{
                                        $to_replace['{ENV_'.$v.'}'] = '';
                                    }
                                }
                            }else{
                                $to_replace = '';
                            }
                     }
                     if(is_array($to_replace))
                          $data[$key] = str_replace(array_keys($to_replace),array_values($to_replace),$val);
                 }
            }
        }
        return $data;
    }





}//End Class
