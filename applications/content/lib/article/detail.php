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




class content_article_detail
{
	/**
	* @var string 版本
	*/
    const VERSION = '0.1';
	/**
	* @var array 节点数组
	*/
    private $_index_objects = array();

	/**
	* 构造方法,实例化MODEL
	*/
    function __construct()
    {
        $this->indexs_model = app::get('content')->model('article_indexs');
        $this->bodys_model = app::get('content')->model('article_bodys');
    }//End Function

	/**
	* 从KV中获取改变的时间
	* @return string
	*/
    public function fetch_detail_change()
    {
        return app::get('content')->getConf('content.kvstore_detail_change');
    }//End Function

	/**
	* 存KV content.kvstore_detail_change 的值
	*/
    public function store_detail_change()
    {
        return app::get('content')->setConf('content.kvstore_detail_change', time());
    }//End Function

	/**
	* 存KV content.kvstore_detail_change 的值
	*/
    public function fetch_index_kvstore($article_id, &$value)
    {
        return base_kvstore::instance('cache/content/indexs')->fetch('index_info_' . $article_id, $value);
    }//End Function

	/**
	* 存KV index_info_。。。 的值
	* @param int $article_id 文章ID
	* @param string $value KV存入的值
	*/
    public function store_index_kvstore($article_id, $value)
    {
        return base_kvstore::instance('cache/content/indexs')->store('index_info_' . $article_id, $value);
    }//End Function

	/**
	* 删除KV index_info_。。 的值
	* @param int $article_id文章ID
	*/
    public function delete_index_kvstore($article_id)
    {
        return base_kvstore::instance('cache/content/indexs')->store('index_info_' . $article_id, array(), 1);
    }//End Function

	/**
	* 获取 KV body_info_... 的值
	* @param int $article_id文章ID
	* @param string $value KV存入的值
	*/
    public function fetch_body_kvstore($article_id, &$value)
    {
        return base_kvstore::instance('cache/content/bodys')->fetch('body_info_' . $article_id, $value);
    }//End Function

	/**
	* 存入 KV body_info_... 的值
	* @param int $article_id文章ID
	* @param string $value KV存入的值
	*/
    public function store_body_kvstore($article_id, $value)
    {
        return base_kvstore::instance('cache/content/bodys')->store('body_info_' . $article_id, $value);
    }//End Function

	/**
	* 删除KV body_info_ 的值
	* @param int $article_id文章ID
	*/
    public function delete_body_kvstore($article_id)
    {
        return base_kvstore::instance('cache/content/bodys')->store('body_info_' . $article_id, array(), 1);
    }//End Function

	/**
	* 获取index 数据
	* @param int $article_id文章ID
	* @param bool $kvstore 是否KV
	* @return string
	*/
    public function get_index($article_id, $kvstore=false)
    {
        $article_id = intval($article_id);
        if($kvstore===false || !isset($this->_index_objects[$article_id])){
            if($kvstore===true && $this->fetch_index_kvstore($article_id, $value)===true){
                $this->fetch_detail_change();
            }else{
                $value = $this->indexs_model->dump($article_id, '*');
                if($kvstore !== false)   $this->store_index_kvstore($article_id, $value);
            }
            $this->_index_objects[$article_id] = $value;
        }else{
            $this->fetch_detail_change();
        }
        return $this->_index_objects[$article_id];
    }//End Function

	/**
	* body 数据
	* @param int $article_id文章ID
	* @param bool $kvstore 是否KV
	* @return string
	*/
    public function get_body($article_id, $kvstore=false)
    {
        $article_id = intval($article_id);
        if($kvstore===true && $this->fetch_body_kvstore($article_id, $value)===true){
            $this->fetch_detail_change();
        }else{
            $value = $this->bodys_model->select()->where('article_id = ?', $article_id)->instance()->fetch_row();
            if($kvstore !== false)  $this->store_body_kvstore($article_id, $value);
        }
        return $value;   //todo: 取get_detail一个请求基本上只有一个，所以不用类缓存
    }//End Function

    /*
     * 取得文件
     * @var int $article_id
     * @access public
     * @return mixed
     */
    public function get_detail($article_id, $kvstore=false)
    {
        $data['indexs'] = $this->get_index($article_id, $kvstore);
        $data['bodys'] = $this->get_body($article_id, $kvstore);
        return $data;   //todo: 取get_detail一个请求基本上只有一个，所以不用类缓存
    }

	/**
	* 解析文章内容里面的热点链接
	* @param string $bodys 文章内容
	* @return string 返回内容
	*/
    public function parse_hot_link($bodys)
    {
        if(is_array($bodys['hot_link'])){
            foreach($bodys['hot_link'] AS $k=>$v){
				$keyword[$k] = $v['keyword'];
                $links[$k] = sprintf('<a href="%s" target="_blank">%s</a>', $v['url'], $v['keyword']);
            }
            return str_replace($keyword, $links, $bodys['content']);
        }else{
            return $bodys['content'];
        }
    }//End Function

}//End Class
