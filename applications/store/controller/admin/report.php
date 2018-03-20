<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/2/3
 * Time: 18:52
 */
class store_ctl_admin_report extends desktop_controller{
    public function __construct(&$app){
        $this ->app =$app;
        parent::__construct($app);
        $this ->is_super = vmc::singleton('desktop_user') ->is_super();
        $this ->store_list = $this ->_get_store();
    }


    public function index(){
        $this ->pagedata['store_list'] = $this ->store_list;
        $this ->pagedata['pay_app'] = array(
            'cash' => "现金支付",
            'alipayqrcode' => "支付宝条码支付",
            'wxqrcode' => "微信刷卡支付",
            'paybycard' => "刷卡",
        );
        $this ->pagedata['from'] = date("Y-m-d 00:00");
        $this ->pagedata['to'] = date("Y-m-d H:i");
        $this ->page('admin/report/index.html');
    }


    public function export(){
        header("Content-type:text/html;charset=utf-8");
        if(empty($_POST['store_id'])){
            $_POST['store_id'] = array_keys($this ->store_list);
        }
        $sql = vmc::singleton('store_report_filter') ->order_sql($_POST);
        if(!$sql){
            exit('参数错误');
        }
        $export = vmc::singleton('store_report_export');
        $file_dir = DATA_DIR.DIRECTORY_SEPARATOR.'export';
        $export ->set_file_dir($file_dir);
        if(!$export ->create_csv($sql)){
            exit('没有查询到数据');
        }
        $export ->download();
    }


    /*
     * 当班日报
     */
    public function current_count($type='store'){
        $user_id = $this ->user->user_id;
        $this ->pagedata['user'] = $this ->user->user_data;
        if($type=='store'){
            $store_id = $_SESSION['now_selected_store'];
            $this ->pagedata['store'] = app::get('store') ->model('store')->getRow('*' ,array('store_id' =>$store_id));
        }elseif($type=='center'){
            $cs_id = $_SESSION['center_id'];
            $center= app::get('store') ->model('center')->getRow('*' ,array('center_id' =>$cs_id));
            $this ->pagedata['center']  =$center ;
            $store_id = $center['store'];
        }
        if(!$store_id){
            $this ->splash('error' ,'' ,'参数错误');
        }
        $data = vmc::singleton('store_report_count') ->get_count($type ,$store_id ,$user_id ,$_POST);
        $this ->pagedata['report'] = $data;
        $this ->pagedata['report_time'] = array(
            'current' => date('Y-m-d H:i:s'),
            'from' =>$_POST['report_from'] ?$_POST['report_from']:date("Y-m-d 00:00"),
            'to' =>$_POST['report_to'] ?$_POST['report_to']:date("Y-m-d H:i"),
        );
        $this ->page("admin/report/{$type}.html");
    }


    /*
     * 当班日报，统一打印
     */
    public function print_store_report(){
        $store_id = $_SESSION['now_selected_store'];
        $this ->pagedata['store'] = app::get('store') ->model('store')->getRow('*' ,array('store_id' =>$store_id));
        if(!$store_id){
            $this ->splash('error' ,'' ,'参数错误');
        }
        $data = vmc::singleton('store_report_count') ->get_count('store' ,$store_id ,false ,$_POST ,true);
        $this ->pagedata['report'] = $data;
        $this ->pagedata['report_time'] = array(
            'current' => date('Y-m-d H:i:s'),
            'from' =>$_POST['report_from'] ?$_POST['report_from']:date("Y-m-d 00:00"),
            'to' =>$_POST['report_to'] ?$_POST['report_to']:date("Y-m-d H:i"),
        );
        $this ->pagedata['all_user'] = true;
        $this ->page("admin/report/store.html");
    }


    /*
     * 获取当前操作员可管理的门店
     */
    private function _get_store(){
        $store_list = $this ->app ->model('store') ->getList('store_id ,store_name');
        $store_list = utils::array_change_key($store_list ,'store_id');
        if($this ->is_super){
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