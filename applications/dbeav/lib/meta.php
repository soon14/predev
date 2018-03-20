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


class dbeav_meta
{
    public function __construct($table, $column, $has_pk = true)
    {
        $metadata = vmc::singleton('dbeav_metadata', 'all')->get_all();

        //$sql = "SELECT * FROM vmc_dbeav_meta_register WHERE tbl_name='".$table."' AND col_name='".$column."'";
        //$rows =  vmc::database()->select($sql);
        $rows = $metadata[$table][$column];
        $rows['col_desc'] = unserialize($rows['col_desc']);

        $this->table = $table;
        $this->column = $column;
        $this->mr_id = $rows['mr_id'];
        $this->mr_info = $rows;
        $this->value_type = 'meta_value_'.$rows['col_type'];
        $this->value = app::get('dbeav')->model($this->value_type);
        $this->pk_name = $rows['pk_name'];
        $this->has_pk = $has_pk;
    }

    public function insert($data)
    {
        $data['mr_id'] = $this->mr_id;

        return $this->value->insert($data);
    }

    public function select(&$data)
    {
        foreach ($data as $row) {
            $pk[] = $row[$this->pk_name];
        }
        $metarows = $this->value->select($this->mr_id, $pk);
        #кϲ
        foreach ($data as $dkey => $drow) {
            $pk_id = $drow[$this->pk_name];
            if (!$metarows[$pk_id]) {
                $metarows[$pk_id] = array($this->column => null);
            } else {
                switch (strtolower($this->mr_info['col_desc']['type'])) {
                    case 'serialize':
                        if (is_string($metarows[$pk_id][$this->column])) {
                            if (($meta_value_tmp = unserialize($metarows[$pk_id][$this->column])) !== false) {
                                $metarows[$pk_id][$this->column] = $meta_value_tmp;
                            }
                        }
                        break;
                    default:
                }//End Switch
            }
            $drow = array_merge($drow, $metarows[$pk_id]);
            #ҪеĲѯҪȥеֵ
            if (!$this->has_pk) {
                unset($drow[$this->pk_name]);
            }
            $data[$dkey] = $drow;
        }

        return true;
    }

    public function update($value, $pk)
    {
        $this->value->update($value, $pk, $this->mr_id);
    }

    public function delete($pk)
    {
        $this->value->delete($pk);
    }

    public function filter($filter)
    {
        $value = $filter[$this->column];
        $pk = $this->value->get_pk($value);
        if (!is_array($pk)) {
            return ' AND 0 ';
        }
        $ret = ' AND '.$this->pk_name.' IN ('.implode(',', $pk).')';

        return $ret;
    }

    public static function get_meta_column($tbl_name)
    {
        $metadata = vmc::singleton('dbeav_metadata', 'all')->get_all();

        foreach ((array) $metadata[$tbl_name] as $meta_columns) {
            $meta['metaColumn'][] = $meta_columns['col_name'];
            $meta['columns'][$meta_columns['col_name']] = unserialize($meta_columns['col_desc']);
        }

        return $meta;
    }
}
