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


class vmcconnect_mdl_app_items extends dbeav_model {

    protected $item_types = array('api', 'hook');

    public function __construct($app) {
        parent::__construct($app);
        $this->use_meta();
    }

    protected function _allow_items($app_id, $type) {
        $app_id = (int) $app_id;
        $type = strtolower($type);
        if (!$app_id || !in_array($type, $this->item_types)) return false;
        $filter = array(
            'app_id' => $app_id,
            'app_allow_type' => $type,
        );
        $rows = $this->getList('app_item', $filter);
        if (!$rows) return false;
        $items = array();
        foreach ($rows as $_v) {
            $items[] = trim($_v['app_item']);
        }
        return $items;
    }

    public function get_allow_api_items($app_id) {
        return $this->_allow_items($app_id, 'api');
    }

    public function get_allow_hook_items($app_id) {
        return $this->_allow_items($app_id, 'hook');
    }

    protected function _cln_items($app_id, $type) {
        $app_id = (int) $app_id;
        $type = strtolower($type);
        if (!$app_id || !in_array($type, $this->item_types)) return false;
        $filter = array(
            'app_id' => $app_id,
            'app_allow_type' => $type,
        );
        return $this->delete($filter);
    }

    protected function _set_items($app_id, $type, $items) {
        $app_id = (int) $app_id;
        $type = strtolower($type);
        if (!$app_id || !in_array($type, $this->item_types)) return false;
        $oldItems = $this->_allow_items($app_id, $type);
        $this->_cln_items($app_id, $type);

        $items = (is_array($items) && $items) ? array_unique(array_values($items)) : null;
        if (!$items) return true;

        $vaules = array();
        foreach ($items as $_v) {
            $vaules[] = "('$app_id','$type','" . trim($_v) . "')";
        }
        $sqls = "INSERT INTO `" . $this->table_name(true) . "` (app_id,app_allow_type,app_item) values " . implode(',', $vaules);
        $res = $this->db->exec($sqls);

        $oldItems && sort($oldItems);
        $items && sort($items);
        if ($res && $oldItems != $items) {
            $servKey = 'app_allow_' . $type . '_items';
            foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                if (method_exists($object, $servKey)) {
                    $object->$servKey($app_id, $items, $oldItems);
                }
            }
        }
        return $res;
    }

    public function set_api_items($app_id, $items) {
        return $this->_set_items($app_id, 'api', $items);
    }

    public function set_hook_items($app_id, $items) {
        return $this->_set_items($app_id, 'hook', $items);
    }

}
