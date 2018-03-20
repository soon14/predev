<?php

class importexport_data_object
{
    private $model;

    private $extends;

    private $title;

    private $fields;

    private $tables;

    private $where;

    private $order_by;

    private $columns;

    private $import_title;

    private $import_keys;

    public function __construct($class_model)
    {
        //实例化要导出或导入的model
        $model = substr(stristr($class_model, 'mdl_'), 4);
        $app_id = substr($class_model, 0, strpos($class_model, '_mdl'));
        $this->model = app::get($app_id)->model($model);
        $this->columns = $this->model->_columns();
        $meta_columns = $this->model->metaColumn;
        foreach ((array)$meta_columns as $meta) {
            unset($this->columns[$meta]);
        }
        //导出导入数据组织扩展
        $object = vmc::service('importexport.'.$class_model);
        if (is_object($object)) {
            $this->extends = $object;
        }
        $this ->title =$this ->get_title();
        $this ->fields =$this ->get_fields();
        $this ->tables =$this ->get_tables();
        $this ->order_by =$this ->get_order_by();
    }

    /**导出需要的几个对象
     * select {fields} from {tables} where {where} order by {order_by}
     * @param null $filter
     * @return array
     */
    public function get_export_object($filter =null){
        $this ->where =  $this ->get_where($filter);
        return array(
            'title' => $this ->title,
            'fields' => $this ->fields,
            'tables' => $this ->tables,
            'where' =>$this ->where,
            'order_by' =>$this ->order_by,
        );
    }

    /**
     * 基本导出表头，key对应查询语句的field ！！！
     * @return array
     */
    public function get_title(){
        if ($this->extends && method_exists($this->extends, 'get_title')) {
            $title = $this->extends->get_title();
        } else {
            $cols = $this->columns;
            $title = array();
            foreach ($cols as $col => $val) {
                if (!$val['deny_export']) {
                    //不进行导出导入字段
                    $title[$col] = $val['label'].'('.$col.')';
                }
            }
        }
        return $title;
    }

    /**
     * 最终sql需要的fields
     * @return string
     */
    public function get_fields(){
        $title = $this ->title ? : $this->get_title();
        return '`'.implode('`,`' ,array_keys($title)).'`';
    }

    /**
     * 最终sql需要的tables ,可以使用join
     * @return mixed
     */
    public function  get_tables(){
        if ($this->extends && method_exists($this->extends, 'get_tables')) {
            $tables = $this->extends->get_tables();
        } else {
            $tables = $this ->model->table_name(1);
        }
        return $tables;
    }

    /**最终sql需要的查询条件
     * @param $filter
     * @return mixed
     */
    public function get_where($filter){
        if ($this->extends && method_exists($this->extends, 'get_where')) {
            $where = $this->extends->get_where($filter);
        } else {
            $where = $this ->model->_filter($filter);
        }
        return $where;
    }

    /**
     * 最终sql需要的排序
     * @return string
     */
    public function get_order_by(){
        $order_by ='';
        if ($this->extends && method_exists($this->extends, 'get_order_by')) {
            $order_by = $this->extends->get_order_by();
        }
        return $order_by;
    }


    /**
     * 处理表头字段
     * @param $title
     */
    public function handle_title(&$title){
        $title = $title+$this ->get_extend_title();
        if ($this->extends && method_exists($this->extends, 'handle_title')) {
            $this->extends->handle_title($title);
        }
    }

    /**
     * 处理数据格式，生成最终报表数据
     * 查询关联表数据
     * 时间、地区等格式化
     * @param $rows
     */
    public function handle_rows(&$rows){
        $rows = $this ->get_extend_rows($rows);
        foreach($rows as &$row){
            foreach($row as $k=>$v){
                switch ($this->columns[$k]['type']) {
                    case 'time':
                    case 'last_modify':
                        $v = date('Y-m-d H:i:s', $v);
                        break;
                    case 'longtext':
                        if (strpos($v, "\n") !== false) {
                            $v = str_replace("\n", ' ', $v);
                        }
                        break;
                    case 'region':
                        $v = explode(':', $v);
                        $v = $v[1];
                        break;
                    default:
                        break;
                }
                $row[$k] = $v;
            }
        }
        foreach($this->title as $k=>$v){
            if(is_array($this->columns[$k]['type'])){
                foreach($rows as &$row){
                    $row[$k] = $this->columns[$k]['type'][$row[$k]];
                }
            }elseif (strpos((string) $this->columns[$k]['type'], 'table:') === 0) {
                //关联表字段
                $sub_obj = explode('@', substr($this->columns[$k]['type'] ,6));
                if (!$sub_obj[1]) {
                    //为指定app则表示关联表和当前表属相同app
                    $sub_obj[1] = $this->model->app->app_id;
                }
                if($sub_obj[1]=='b2c' && $sub_obj[0]=='members'){
                    //member id特殊处理
                    $member_ids = array_keys(utils::array_change_key($rows ,$k));
                    $data = app::get('pam')->model('members')->getList('*',array('member_id'=>$member_ids));
                    $data = utils::array_change_key($data ,'member_id' ,1);
                    foreach($rows as &$row){
                        $pam_data = $data[$row[$k]];
                        foreach((array)$pam_data as $v){
                            $arr_name[$v['login_type']] = $v['login_account'];
                        }
                        if( isset($arr_name['local']) ){
                            $login_name = $arr_name['local'];
                        }elseif(isset($arr_name['email'])){
                            $login_name = $arr_name['email'];
                        }elseif(isset($arr_name['mobile'])){
                            $login_name = $arr_name['mobile'];
                        }else{
                            $login_name = current($arr_name);
                        }
                        $row[$k] =$login_name;
                    }
                }else{
                    //显示对应的is_title字段
                    $has_mdl = app::get($sub_obj[1])->model($sub_obj[0]);
                    $ids = array_keys(utils::array_change_key($rows ,$k));
                    if($ids){
                        $textColumns = $has_mdl->getList($has_mdl->schema['textColumn'] .','.$has_mdl->schema['idColumn'], array($has_mdl->schema['idColumn'] => $ids));
                        $textColumns = utils::array_change_key($textColumns ,$has_mdl->schema['idColumn']);
                        foreach($rows as &$row){
                            $row[$k] = $textColumns[$row[$k]][$has_mdl->schema['textColumn']] ? $textColumns[$row[$k]][$has_mdl->schema['textColumn']] : $row[$k];
                        }
                    }
                }
            }

        }
        if ($this->extends && method_exists($this->extends, 'handle_rows')) {
            $this->extends->handle_rows($rows);
        }
    }

    /**
     * 扩展导出表头，对应的值，可以配合使get_extend_rows生成
     * @return array
     */
    public function get_extend_title(){
        if ($this->extends && method_exists($this->extends, 'get_extend_title')) {
            $title = $this->extends->get_extend_title();
        } else {
            $title = array();
        }
        return $title;
    }

    /**
     * 扩展导出数据
     * @param $rows
     * @return mixed
     */
    public function get_extend_rows($rows){
        if ($this->extends && method_exists($this->extends, 'get_extend_rows')){
            $rows= $this->extends->get_extend_rows($rows);
        }
        return $rows;
    }



    /**
     * 导入文件中的表头
     * @param $title
     */
    public function set_real_title($title){
        $this ->import_title = $title;
    }

    /**
     * 导入文件中的表头
     * @return mixed
     */
    public function get_real_title(){
        return  $this ->import_title;
    }

    /**
     * 将导入文件中的数据转成key =>value形式
     * @param $row
     * @return array
     */
    public function change_row_keys($row){
        $keys = $this ->get_import_keys();
        $new_row =array();
        foreach($row as $kk=>$vv){
            if(isset($keys[$kk])){
                $new_row[$keys[$kk]] = $vv;
            }
        }
        return $new_row;
    }


    /**
     * 检查当前数据是否需要和上一条数据进行合并，比如多规格商品
     * @param $current
     * @param $rows
     * @return bool
     */
    public function need_continue($current ,&$rows ){
        $current = $this ->change_row_keys($current);
        if ($this->extends && method_exists($this->extends, 'need_continue')){
            return $this->extends->need_continue($current ,$rows);
        }
        if(empty($rows)){
            $rows[] =$current;
            return true;
        }else{
            return false;
        }
    }

    /**
     * 将数据转为最终 model save方法所需要的数据格式
     * @param $rows
     * @param string $msg
     * @return mixed
     */
    public function dataToSdf($rows ,&$msg=''){
        if ($this->extends && method_exists($this->extends, 'dataToSdf')){
            return $this->extends->dataToSdf($rows ,$msg);
        }
        return $rows[0];
    }

    /**当前数据导入完成后的操作
     * @param $data_sdf
     * @param $msg
     * @return bool
     */
    public function import_after($data_sdf ,$msg){
        if ($this->extends && method_exists($this->extends, 'import_after')){
            return $this->extends->import_after($data_sdf ,$msg);
        }
        return true;
    }


    /**
     * 文件中数据全部导入完成后的操作
     * @param $msg
     * @return bool
     */
    public function import_end(&$msg){
        if ($this->extends && method_exists($this->extends, 'import_end')){
            return $this->extends->import_end($msg);
        }
        return true;
    }

    /**
     * 将导入文件表头转换成为数据库字段
     * @return array|mixed
     */
    private function get_import_keys(){
        if($this ->import_keys){
            $keys = $this ->import_keys;
        }else{
            $real_title = $this ->get_real_title();
            if ($this->extends && method_exists($this->extends, 'get_import_title')){
                $title = $this ->extends ->get_import_title();
                $keys = array();
                foreach($real_title as $k=>$v){
                    $key = array_search(trim($real_title[$k]) ,$title);
                    if($key){
                        $keys[$k] = $key;
                    }
                }

            }else{
                $keys = $real_title;
            }
            $this ->import_keys = $keys;
        }
        return $keys;
    }

}
