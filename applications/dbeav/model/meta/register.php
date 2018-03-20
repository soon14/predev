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


class dbeav_mdl_meta_register extends base_db_model
{
    public function register($table, $id_column, $column)
    {
        list($col_name, $col_desc) = each($column);
        $this->table = $table;
        $this->col_name = $col_name;
        $this->col_desc = $col_desc;
        if ($this->is_valid()) {
            $entry = array(
                'tbl_name' => $table,
                'pk_name' => $id_column,
                'col_name' => $col_name,
                'col_type' => $this->get_col_type(),
                'col_desc' => $col_desc,
                    );
            $this->insert($entry);

            return true;
        } else {
            return false;
        }
    }

    public function save(&$column, $mustUpdate = null, $mustInsert = false)
    {
        $this->col_desc = $column['col_desc'];
        $column['col_type'] = $this->get_col_type();

        return parent::save($column);
    }

    public function drop($table, $col)
    {
        $mrId = vmc::database()->selectrow('SELECT mr_id FROM vmc_dbeav_meta_register WHERE tbl_name = "'.$table.'" AND col_name = "'.$col.'"');
        if (!$mrId) {
            return true;
        }
        $mrId = $mrId['mr_id'];
        $this->delete(array('mr_id' => $mrId));

        return vmc::database()->exec('DELETE FROM vmc_dbeav_meta_register WHERE mr_id = '.$mrId);
    }

    private function is_valid()
    {
        if (!is_string($this->col_name) && !is_array($this->col_desc)) {
            return false;
        }
        $sql = "select count(*) as c from vmc_dbeav_meta_register where tbl_name='".$this->table."' and col_name='".$this->col_name."'";
        $ret = $this->db->select($sql);
        if (intval($ret[0]['c']) > 0) {
            return false;
        }

        return true;
    }

    private function get_col_type()
    {
        $col_type = $this->col_desc['type'];
        if (in_array($col_type, array('time', 'cdate', 'last_modify', 'number')) || strstr($col_type, 'int')) {
            return  'int';
        }
        if (in_array($col_type, array('money')) || strstr($col_type, 'decimal')) {
            return 'decimal';
        }
        if (strstr($col_type, 'datetime')) {
            return 'datetime';
        }
        if (strstr($col_type, 'denum')) {
            return 'denum';
        }
        if (strstr($col_type, 'text')) {
            return  'text';
        }
        if (in_array($col_type, array('serialize'))) {
            return 'longtext';
        }

        return  'varchar';
    }

    public static function get_meta_column($tbl_name)
    {
        $sql = "select col_name,col_desc from vmc_meta_refs where tbl_name='{$tbl_name}'";
        $rows = $this->db->select($sql);
        foreach ($rows as $row) {
            $meta['metaColumn'][] = $row['col_name'];
            $meta['columns'][$row['col_name']] = ($row['col_desc']);
        }

        return $meta;
    }
}
