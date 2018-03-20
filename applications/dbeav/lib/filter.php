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


class dbeav_filter {
    function dbeav_filter_parser($filter, $tableAlias = null, $baseWhere = null, &$object) {

        $this->use_like = $object->filter_use_like;
        $filter = utils::addslashes_array($filter);
        $schema = $object->get_schema();
        $idColumn = $schema['idColumn'];
        $tPre = ($tableAlias ? $tableAlias : '`' . $object->table_name(true) . '`') . '.';
        $where = $baseWhere ? $baseWhere : array(
            1
        );
        if (isset($filter['tag']) && $tag = $filter['tag']) {
            if (is_array($filter['tag'])) {
                foreach ($filter['tag'] as $tk => $tv) {
                    if ($tv == '_ANY_') unset($filter['tag'][$tk]);
                }
            }
            if (isset($filter['tag'])) unset($filter['tag']);
            if (is_array($tag)) {
                if (count($tag) == 0) {
                    unset($tag);
                }
            } else {
                $tag = array(
                    $tag
                );
            }
            if ($tag == '_ANY_' || $tag == array(
                '_ANY_'
            )) {
                unset($tag);
            }
            if ($tag) {
                $a = array();
                $db_prefix = $object->db->prefix;
                $obj_table_name = $object->table_name(true);
                if (is_array($tag) && count($tag) > 0) {
                    foreach ($object->db->select("select rel_id from vmc_desktop_tag_rel where tag_id in (" . implode(',', $tag) . ") AND CONCAT('$db_prefix',app_id,'_',tag_type) = '$obj_table_name'") as $r) {
                        $a[] = $r['rel_id'];
                    }
                }
                if (count($a) > 0) {
                    $where[] = "{$tPre}{$idColumn} in ('" . implode("','", $a) . "')";
                } else {
                    $where[] = ' 0';
                }
            }
        }

        if (isset($filter['tag_name']) && $tag_name = $filter['tag_name']) {
            if (isset($filter['tag_name'])) unset($filter['tag_name']);
            if (is_array($tag_name)) {
                if (count($tag_name) == 0) {
                    unset($tag_name);
                }
            }else{
                if(strpos($tag_name,'|')){
                        $tag_name = explode('|',$tag_name);
                }else{
                        $tag_name = explode(',',$tag_name);
                }
            }


            if (!empty($tag_name)) {
                $a = array();
                $db_prefix = $object->db->prefix;
                $obj_table_name = $object->table_name(true);
                $tag_name_filter_sql = "SELECT tr.rel_id,t.tag_name from vmc_desktop_tag_rel as tr LEFT JOIN vmc_desktop_tag as t ON tr.tag_id = t.tag_id WHERE t.tag_name IN ('".implode("','", $tag_name)."') AND CONCAT('$db_prefix',tr.app_id,'_',tr.tag_type) = '$obj_table_name' ";
                foreach ($object->db->select($tag_name_filter_sql) as $r) {
                    $a[] = $r['rel_id'];
                }

                if (count($a) > 0) {
                    $where[] = "{$tPre}{$idColumn} in ('" . implode("','", $a) . "')";
                } else {
                    $where[] = ' 0';
                }
            }
        }

        $cols = array_merge($object->searchOptions() , $object->_columns());
        //idColumn为数组时单独处理
        if (!is_array($idColumn) && isset($filter[$idColumn]) && ($filter[$idColumn] == '_ALL_' || $filter[$idColumn] == array(
            '_ALL_'
        ))) {
            unset($filter[$idColumn]);
        } elseif (!is_array($idColumn) && isset($filter[$idColumn])) {
            $where[] = " {$tPre}{$idColumn} in ('" . implode("','", (array)$filter[$idColumn]) . "') ";
            unset($filter[$idColumn]);
        }
        if (is_array($filter)) {
            foreach ($filter as $k => $v) {
                if (is_null($v)) {
                    $where[] = $tPre . $k . ' is NULL ';
                    continue;
                }
                if ($k == 'filter_sql') {
                    $where[] = str_replace('{table}', $tPre, stripslashes($v));
                    continue;
                }
                if (isset($cols[$k]) || strpos($k, '|')) {
                    if (strpos($k, '|') !== false) {
                        list($k, $type) = explode('|', $k);
                        unset($filter[$k]);
                        if(in_array($cols[$k]['type'] ,array('time','last_modify')) && is_string($v) && !is_numeric($v)){
                            $v = strtotime($v);
                        }
                        $_str = $this->_inner_getFilterType($type, $v, false);
                        if (strpos($_str, '{field}') !== false) $where[] = str_replace('{field}', $tPre . $k, $_str);
                        else $where[] = $tPre . $k . $_str;
                        $_str = null;
                        continue;
                    }
                    if (is_array($v)) {
                        $where[] = $tPre . $k . $this->_inner_getFilterType('in', $v, false);
                        continue;
                    }
                    $ac = array();
                    if ($cols[$k]['type'] == 'time' || $cols[$k]['type'] == 'last_modify') {
                        if ($filter['_' . $k . '_search'] == 'between') {
                            $a_v = array();
                            $a_v[] = strtotime($filter[$k . '_from']);
                            $a_v[] = strtotime($filter[$k . '_to']);
                            if (!empty($filter[$k . '_from']) && empty($filter[$k . '_to'])) {
                                $where[] = $tPre . $k . $this->_inner_getFilterType('bthan', $a_v[0]);
                            } elseif (empty($filter[$k . '_from']) && !empty($filter[$k . '_to'])) {
                                $where[] = $tPre . $k . $this->_inner_getFilterType('lthan', $a_v[1]);
                            } elseif (!empty($filter[$k . '_from']) && !empty($filter[$k . '_to'])) {
                                $where[] = str_replace('{field}', $tPre . $k, $this->_inner_getFilterType($filter['_' . $k . '_search'], $a_v));
                            }
                        } else {
                            $a_v = strtotime($filter[$k]);
                            $where[] = $tPre . $k . $this->_inner_getFilterType($filter['_' . $k . '_search'], $a_v);
                        }
                    } elseif (!is_array($cols[$k]['type']) && ($cols[$k]['type'] == 'money' || $cols[$k]['type'] == 'number' || $cols[$k]['type'] == 'float' || strpos($cols[$k]['type'], 'decimal') !== false) && $filter['_' . $k . '_search']) {
                        if ($filter['_' . $k . '_search'] == 'between') {
                            $a_v = array(
                                $filter[$k . '_from'],
                                $filter[$k . '_to']
                            );
                            if (!empty($filter[$k . '_from']) && empty($filter[$k . '_to'])) {
                                $where[] = $tPre . $k . $this->_inner_getFilterType('bthan', $a_v[0]);
                            } elseif (empty($filter[$k . '_from']) && !empty($filter[$k . '_to'])) {
                                $where[] = $tPre . $k . $this->_inner_getFilterType('sthan', $a_v[1]);
                            } elseif (!empty($filter[$k . '_from']) && !empty($filter[$k . '_to'])) {
                                $where[] = str_replace('{field}', $tPre . $k, $this->_inner_getFilterType($filter['_' . $k . '_search'], $a_v));
                            }
                        } else {
                            $where[] = $tPre . $k . $this->_inner_getFilterType($filter['_' . $k . '_search'], $v);
                        }
                    } else if (isset($cols[$k]['filtertype']) && isset($filter['_' . $k . '_search'])) {
                        $where[] = $tPre . $k . $this->_inner_getFilterType($filter['_' . $k . '_search'], $v);
                    } else if (isset($cols[$k]['searchtype']) && !isset($filter['object_filter'])) {
                        $where[] = $tPre . $k . $this->_inner_getFilterType($cols[$k]['searchtype'], $v);
                    } else if (substr($k, 0, 1) != '_') {
                        if ($k != 'object_filter') {
                            if ($cols[$k]['type'] == 'region') {
                                if (isset($v)) $v = explode(':', $v);
                                unset($v[2]);
                                $v = implode(':', $v);
                                $where[] = $tPre . $k . ' like \'' . $v . '%\'';
                            } elseif (is_array($v)) {
                                foreach ($v as $m) {
                                    if ($m !== '_ANY_' && $m !== '' && $m != '_ALL_') {
                                        $ac[] = $cols[$k]['fuzzySearch'] ? ($tPre . $k . ' like \'%' . $m . '%\'') : ($tPre . $k . '=\'' . $m . '\'');
                                    } else {
                                        $ac = array();
                                        break;
                                    }
                                }
                                if (count($ac) > 0) {
                                    $where[] = '(' . implode($ac, ' or ') . ')';
                                }
                            } elseif (isset($v)) {
                                $where[] = $tPre . $k . '=\'' . $v . '\'';
                            }
                        }
                    }
                }
            }
        }
        $_return = implode($where, ' AND ');
        return $_return;
    }
    function _inner_getFilterType($type, $var, $force = true) {
        if (!$this->use_like && !is_array($var) && $force) {
            $type = 'nequal';
        }
        $FilterArray = array(
            'than' => ' > ' . $var,
            'lthan' => ' < ' . $var,
            'nequal' => ' = \'' . $var . '\'',
            'noequal' => ' <> \'' . $var . '\'',
            'tequal' => ' = \'' . $var . '\'',
            'sthan' => ' <= ' . $var,
            'bthan' => ' >= ' . $var,
            'has' => ' like \'%' . $var . '%\'',
            'head' => ' like \'' . $var . '%\'',
            'foot' => ' like \'%' . $var . '\'',
            'nohas' => ' not like \'%' . $var . '%\'',
            'between' => ' {field}>=' . $var[0] . ' and ' . ' {field}<=' . $var[1],
            'in' => " in ('" . implode("','", (array)$var) . "') ",
            'notin' => " not in ('" . implode("','", (array)$var) . "') ",
        );
        return $FilterArray[$type];
    }
}
