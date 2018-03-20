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


class vmcconnect_mdl_apps extends dbeav_model {

    public $defaultOrder = array(
        'app_status DESC, app_order ASC, app_id ASC',
    );

    public function __construct($app) {
        parent::__construct($app);
        $this->use_meta();
    }

    protected function _tpls() {
        static $tpls;
        if ($tpls) return $tpls;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/tpls.php')) {
            $tpls = (include $_tmp_file);
            return $tpls;
        }
        
        $tpls = is_file($_tpl_file = $this->app->app_dir . '/vars/api/tpls.php') ? (include $_tpl_file) : null;
        return $tpls;
    }

    public function modifier_app_com_tpl($col, $row) {
        $_tpls = $this->_tpls();
        $_str = ($_tpls && isset($_tpls[$row['app_com_tpl']])) ? $_tpls[$row['app_com_tpl']] : '默认';
        return $_str;
    }

    public function modifier_app_status($col, $row) {
        $_str = $row['app_status'] ? '<span class="text-success">启用</span>' : '<span class="text-danger">禁用</span>';

        return $_str;
    }

    public function modifier_app_api_status($col, $row) {
        $_str = $row['app_api_status'] ? '<span class="text-success">启用</span>' : '<span class="text-danger">禁用</span>';

        return $_str;
    }

    public function modifier_app_hook_status($col, $row) {
        $_str = $row['app_hook_status'] ? '<span class="text-success">启用</span>' : '<span class="text-danger">禁用</span>';

        return $_str;
    }

    function random($len) {
        $source = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz|';
        $ret = '';
        for (; $len >= 1; $len--) {
            $position = rand() % strlen($source);
            $ret .= substr($source, $position, 1);
        }
        return $ret;
    }

    function get_app($app_id) {
        $app_id = (int) $app_id;
        if (!$app_id) return false;
        $filter = array(
            'app_id' => $app_id,
        );
        $row = $this->getRow('*', $filter);
        return $row;
    }

    function get_app_secret($app_id) {
        $app_id = (int) $app_id;
        if (!$app_id) return false;

        $row = $this->get_app($app_id);
        if (!$row) return false;

        return $row['app_secret'];
    }

    public function insert(&$data) {
        $res = parent::insert($data);
        if ($res) {
            foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                if (method_exists($object, 'app_create')) {
                    $object->app_create($data, array());
                }
            }
        }
        return $res;
    }

    public function update($data, $filter = array(), $mustUpdate = null) {
        $oldDatas = ($filter && isset($filter['app_id']) && is_array($filter['app_id'])) ? $this->getList('*', $filter) : null;
        $oldData = (isset($data['app_id']) && $data['app_id']) ? $this->dump($data['app_id'], '*', 'default') : array();
        $res = parent::update($data, $filter, $mustUpdate);
        if ($res) {
            if ($oldDatas) {
                foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                    if (method_exists($object, 'app_batch_update')) {
                        $newDatas = $filter ? $this->getList('*', $filter) : null;
                        $object->app_batch_update($data, $filter, $newDatas, $oldDatas);
                    }
                }
            } else {
                $newData = $this->dump($data['app_id'], '*', 'default');
                foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                    if (method_exists($object, 'app_update')) {
                        $object->app_update($newData, $oldData);
                    }
                }
            }
        }

        return $res;
    }

}
