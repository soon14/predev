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


class vmcconnect_mdl_hooks extends dbeav_model {

    protected $mod_hook_items;

    public function __construct($app) {
        parent::__construct($app);
        $this->use_meta();
        $this->mod_hook_items = app::get('vmcconnect')->model('hook_items');
    }
    
    protected function _msgs() {
        static $msgs;
        if($msgs) return $msgs;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/msgs.php')) {
            $msgs = (include $_tmp_file);
            return $msgs;
        }
        $msgs = is_file($_msg_file = $this->app->app_dir . '/vars/hook/msgs.php') ? (include $_msg_file) : null;
        return $msgs;
    }
    
    public function modifier_hook_msg_tpl($col,$row) {
        $_msgs = $this->_msgs();
        $_str = ($_tpls && isset($_msgs[$row['hook_msg_tpl']])) ? $_msgs[$row['hook_msg_tpl']] : '默认';
        return $_str;
    }
    
    public function modifier_hook_status($col,$row) {
        $_str = $row['hook_status'] ? '<span class="text-success">启用</span>' : '<span class="text-danger">禁用</span>';

        return $_str;
    }

    public function urlExists($app_id, $url, $hook_id = 0) {
        $app_id = (int) $app_id;
        $url = trim($url);
        if (!$app_id || !$url) return false;
        $hook_id = (int) $hook_id;

        $filter = array();
        $_row = $this->getRow('hook_id', array(
//            'app_id' => $app_id,
            'hook_url' => $url,
        ));
        if ($_row) {
            if ($hook_id && $hook_id == $_row['hook_id']) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function save($data) {
        if (!$data || !is_array($data)) return false;
        $_hook_items = isset($data['hook_items']) ? $data['hook_items'] : null;
        unset($data['hook_items']);
        
        $saveRes = parent::save($data);
        $app_id = ($data && $data['app_id']) ? $data['app_id'] : 0;
        $hook_id = ($data && $data['hook_id']) ? $data['hook_id'] : 0;
        if (!$app_id || !$hook_id) return false;

        $this->mod_hook_items->set_items($app_id, $hook_id, $_hook_items);
        return $saveRes;
    }
    
    public function insert(&$data) {
        $res = parent::insert($data);
        if ($res) {
            foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                if (method_exists($object, 'app_hook_create')) {
                    $object->app_hook_create($data, array());
                }
            }
        }
        return $res;
    }

    public function update($data, $filter = array(), $mustUpdate = null) {
        $oldDatas = ($filter && isset($filter['hook_id']) && is_array($filter['hook_id'])) ? $this->getList('*', $filter) : null;
        $oldData = (isset($data['hook_id']) && $data['hook_id']) ? $this->dump($data['hook_id'], '*', 'default') : array();
        $res = parent::update($data, $filter, $mustUpdate);
        if ($res) {
            if ($oldDatas) {
                foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                    if (method_exists($object, 'app_hook_batch_update')) {
                        $newDatas = $filter ? $this->getList('*', $filter) : null;
                        $object->app_hook_batch_update($data, $filter, $newDatas, $oldDatas);
                    }
                }
            } else {
                $newData = $this->dump($data['hook_id'], '*', 'default');
                foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                    if (method_exists($object, 'app_hook_update')) {
                        $object->app_hook_update($newData, $oldData);
                    }
                }
            }
        }

        return $res;
    }

}
