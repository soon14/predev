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


class vmcconnect_mdl_hook_items extends dbeav_model {

    public function __construct($app) {
        parent::__construct($app);
        $this->use_meta();
    }

    protected function _cln_items($app_id, $hook_id) {
        $app_id = (int) $app_id;
        $hook_id = (int) $hook_id;
        if (!$app_id || !$hook_id) return false;

        $filter = array(
            'app_id' => $app_id,
            'hook_id' => $hook_id,
        );
        return $this->delete($filter);
    }

    public function set_items($app_id, $hook_id, $items) {
        $app_id = (int) $app_id;
        $hook_id = (int) $hook_id;
        if (!$app_id || !$hook_id) return false;
        $oldItems = $this->get_items($hook_id, $app_id);
        $this->_cln_items($app_id, $hook_id);

        $items = (is_array($items) && $items) ? array_unique(array_values($items)) : null;
        if (!$items) return true;

        $vaules = array();
        foreach ($items as $_v) {
            $vaules[] = "('$app_id','$hook_id','" . trim($_v) . "')";
        }
        $sqls = "INSERT INTO `" . $this->table_name(true) . "` (app_id,hook_id,hook_item) values " . implode(',', $vaules);
        $res = $this->db->exec($sqls);
        $oldItems && sort($oldItems);
        $items && sort($items);
        if ($res && $oldItems != $items) {
            foreach (vmc::servicelist('vmcconnect.log.app') as $object) {
                if (method_exists($object, 'app_hook_allow_items')) {
                    $object->app_hook_allow_items($app_id, $hook_id, $items, $oldItems);
                }
            }
        }
        return $res;
    }

    public function get_items($hook_id, $app_id = 0) {
        $hook_id = (int) $hook_id;
        if (!$hook_id) return false;
        $app_id = (int) $app_id;
        $filter = array(
            'hook_id' => $hook_id,
        );
        $app_id && $filter['app_id'] = $app_id;
        $rows = $this->getList('hook_item', $filter);
        if (!$rows) return false;
        $get_items = array();
        foreach ($rows as $_v) {
            $get_items[] = trim($_v['hook_item']);
        }
        return $get_items;
    }

}
