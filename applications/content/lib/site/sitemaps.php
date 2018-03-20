<?php
/**
* sitemaps生成类
*/
class content_site_sitemaps {
    
	/**
	* 构造方法 实例化APP
	* @param object $app app实例
	*/
    public function __construct( $app ) {
        $this->app = $app;
    }
    
    /*
     * 返回map
     * @return array
     * array(
     *  array(
     *      'url' => '...........'
     *      ),
     *  array(
     *      'url' => '...........'
     *      )
     * )
     */
    
    public function get_arr_maps() {
        $this->router = app::get('site')->router();
        $this->_get( 'article_indexs','site_article','index','article_id',$tmp );
        return $tmp;
    }
    /**
	* 获取sitemaps
	* @param string $model model名
	* @param string $ctl 控制器
	* @param string $act 方法
	* @param int $index 索引
	* @param array sitemaps数组
	*/
     private function _get( $model,$ctl,$act,$index,&$tmp ) {
        $offset = 0;
        $limit  = 100;
        while(true) {
            $arr = $this->app->model($model)->getList( '*', array(), $offset, $limit );
            foreach( (array)$arr as $row ) {
                $tmp[] = array(
                    'app' => 'content',
                    'ctl' => $ctl,
                    'act' => $act,
                    'arg0' => $row[$index]);                    
            }
            $offset += $limit;
            if( $limit>count($arr) ) break;
        }
    }
}