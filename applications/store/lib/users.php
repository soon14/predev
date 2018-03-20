<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/3/29
 * Time: 14:58
 */
class store_users{

    private $user;

    public function __construct(&$app){
        $this ->user = vmc::singleton('desktop_user');
        $this ->user ->user_id = $this ->user ->get_id();
        $this ->app = $app;
    }


    /*
     * 获取当前操作员可管理的门店
     */
    public function get_store_list(){
        $store_list = $this ->app ->model('store') ->getList('*');
        $store_list = utils::array_change_key($store_list ,'store_id');
        if($this ->user ->is_super()){
            return $store_list;
        }
        $store_id = array();
        //获取可管理的中台
        $center = $this ->app ->model('center_desktopuser') ->getList('center_id' ,array('user_id' =>$this->user->user_id));
        if(is_array($center)){
            $center_store = $this ->app ->model('center') ->getList('store' ,array('center_id' =>array_keys(utils::array_change_key($center ,'center_id'))));

            foreach($center_store as $v){
                $store_id = array_merge($store_id ,$v['store']);
            }
        }

        //获取可管理的店铺
        $store = $this ->app ->model('relation_desktopuser') ->getList('store_id' ,array('user_id' =>$this->user->user_id));
        $store_id = array_merge($store_id , array_keys(utils::array_change_key($store ,'store_id')));
        $store_id = array_unique($store_id);
        foreach($store_list as $k =>$v){
            if(!in_array($k ,$store_id)){
                unset($store_list[$k]);
            }
        }

        return $store_list;
    }
}