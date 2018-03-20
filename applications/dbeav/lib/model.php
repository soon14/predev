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


class dbeav_model extends base_db_model
{
    //dbschema tableNameѡһ
    public $dbschema = null;
    public $api_id = null;

    public function events()
    {
    }

    public function _columns()
    {
        return $this->schema['columns'];
    }

    public function use_meta()
    {
        if (!$this->use_meta) {
            $meta_schema = dbeav_meta::get_meta_column($this->table_name(true));
            if (!is_array($meta_schema)) {
                return false;
            }
            $this->use_meta = true;
            $this->schema = array_merge_recursive($this->schema, $meta_schema);
            foreach ($meta_schema['columns'] as $c => $a) {
                if ($a['in_list']) {
                    $this->schema['in_list'][] = $c;
                }
            }
            $this->metaColumn = $this->schema['metaColumn'];
        }
    }

    public function count($filter = null)
    {
        $row = $this->db->select('SELECT count(*) as _count FROM `'.$this->table_name(1).'` WHERE '.$this->_filter($filter));

        return intval($row[0]['_count']);
    }

    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null)
    {
        if (!isset($this->use_meta)) {
            $this->use_meta = false;
        }
        if (!$cols) {
            $cols = $this->defaultCols;
        }
        if (!empty($this->appendCols)) {
            $cols .= ','.$this->appendCols;
        }
        if ($this->use_meta) {
            $meta_info = $this->prepare_select($cols);
        }
        $orderType = $orderType ? $orderType : $this->defaultOrder;
        $sql = 'SELECT '.$cols.' FROM `'.$this->table_name(true).'` WHERE '.$this->_filter($filter);
        if ($orderType) {
            $sql .= ' ORDER BY '.(is_array($orderType) ? implode($orderType, ' ') : $orderType);
        }
        $data = $this->db->selectLimit($sql, $limit, $offset);
        $this->tidy_data($data, $cols);
        if ($this->use_meta && count($meta_info['metacols']) && $data) {
            foreach ($meta_info['metacols'] as $col) {
                $obj_meta = new dbeav_meta($this->table_name(true), $col, $meta_info['has_pk']);
                $obj_meta->select($data);
            }
        }

        return $data;
    }

    public function getRow($cols = '*', $filter = array(), $orderType = null)
    {
        $data = $this->getList($cols, $filter, 0, 1, $orderType);
        if ($data) {
            return $data['0'];
        } else {
            return $data;
        }
    }

    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        if ($this->use_meta) {
            foreach (array_keys((array) $filter) as $col) {
                if (in_array(strval($col), $this->metaColumn)) {
                    $meta_filter[$col] = $filter[$col];
                    unset($filter[$col]);  #ȥfilterаmeta
                    $obj_meta = new dbeav_meta($this->table_name(true), $col);
                    $meta_filter_ret .= $obj_meta->filter($meta_filter);
                }
            }
        }
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $dbeav_filter_ret = $dbeav_filter->dbeav_filter_parser($filter, $tableAlias, $baseWhere, $this);
        if ($this->use_meta) {
            return $dbeav_filter_ret.$meta_filter_ret;
        }

        return $dbeav_filter_ret;
    }

    public function insert(&$data)
    {
        if ($ret = parent::insert($data)) {
            if ($this->use_meta) {
                foreach ($this->metaColumn as $col) {
                    if (isset($data[$col])) {
                        $obj_meta = new dbeav_meta($this->table_name(true), $col);
                        $metavalue['pk'] = $data[$this->idColumn];
                        $metavalue['value'] = $data[$col];
                        $obj_meta->insert($metavalue);
                    }
                }
            }
        }

        return $ret;
    }

    public function delete($filter, $subSdf = 'delete')
    {
        if ($subSdf && !is_array($subSdf)) {
            $subSdf = $this->getSubSdf($subSdf);
        }
        $allHas = (array) $this->has_parent;
        foreach ($allHas as $k => $v) {
            if (array_key_exists($k, $allHas)) {
                $subInfo = explode(':', $allHas[$k]);
                $modelInfo = explode('@', $subInfo[0]);
                $appId = $modelInfo[1] ? $modelInfo[1] : $this->app->app_id;
                $o = app::get($appId)->model($modelInfo[0]);
                $pkey = $o->_getPkey($this->table_name(), $subInfo[2], $this->app->app_id);
                $tFilter = $filter;
                if ($filter[$pkey['c']]) {
                    $tmp = array();
                    $tmp = $filter[$pkey['c']];
                    unset($tFilter[$pkey['c']]);
                    $tFilter[$pkey['p']] = $tmp;
                }

                $o->delete($tFilter, $v[1]);
            }
        }
        $allHas = array_merge((array) $this->has_many, (array) $this->has_one);
        foreach ((array) $subSdf as $k => $v) {
            if (array_key_exists($k, $allHas)) {
                // $subInfo = array();
                $subInfo = explode(':', $allHas[$k]);
                $modelInfo = explode('@', $subInfo[0]);
                $appId = $modelInfo[1] ? $modelInfo[1] : $this->app->app_id;

                $pkey = $this->_getPkey($modelInfo[0], $subInfo[2], $appId);
                $this->get_pk_list($filter);
                $tFilter = $filter;
                if ($filter[$pkey['p']]) {
                    $tmp = array();
                    $tmp = $filter[$pkey['p']];
                    unset($tFilter[$pkey['p']]);
                    $tFilter[$pkey['c']] = $tmp;
                }
                $o = app::get($appId)->model($modelInfo[0]);
                $o->delete($tFilter, $v[1]);
            }
        }

        if ($this->use_meta) {
            $pk = $this->get_pk_list($filter);
            foreach ($this->metaColumn as $col) {
                $obj_meta = new dbeav_meta($this->table_name(true), $col);
                $obj_meta->delete($pk);
            }
        }

        return parent::delete($filter);
    }

    public function update($data, $filter = array(), $mustUpdate = null)
    {
        if ($this->use_meta) {
            $pk = $this->get_pk_list($filter);
            foreach ($this->metaColumn as $col) {
                if (!in_array($col, array_keys($data))) {
                    continue;
                }
                $obj_meta = new dbeav_meta($this->table_name(true), $col);
                $obj_meta->update($data[$col], $pk);
                unset($data[$col]);
            }
        }

        return parent::update($data, $this->_filter($filter), $mustUpdate);
    }

    private function get_pk_list(&$filter)
    {
        $rows = $this->getList($this->idColumn, $filter);
        if(!$rows || !is_array($rows)){
            return array();
        }
        foreach ($rows as $row) {
            $pk[] = $row[$this->idColumn];
        }
        $filter = null;
        $filter[$this->idColumn] = $pk; #filter滻Ϊ
        return $pk;
    }

    /**
     * register_meta.
     *
     * Ϊעһֶ
     *
     * @param mixed $column
     *
     * @return bool
     */
    public function meta_register($column)
    {
        return app::get('dbeav')->model('meta_register')->register($this->table_name(true), $this->idColumn, $column);
    }

    public function meta_meta($col)
    {
        return app::get('dbeav')->model('meta_register')->drop($this->table_name(true), $col);
    }

    /**
     * prepare_select.
     *
     * ȥѯmetaֶκΪеĲѯ
     *
     * @param string $cols
     *
     * @return array
     */
    public function prepare_select(&$cols)
    {
        $aCols = explode(',', $cols);
        if ($aCols[0] == '*' or !$aCols) {
            $ret['has_pk'] = true;
            $ret['metacols'] = $this->metaColumn;

            return $ret;
        }
        foreach ($aCols as $key => $col) {
            $col = trim($col, '`');
            $aCols[$key] = $col;
            if (in_array($col, $this->metaColumn)) {
                unset($aCols[$key]);
                $ret['metacols'][] = $col;
            }
        }
        if (!in_array($this->idColumn, $aCols) && count($ret['metacols'])) {
            array_unshift($aCols, $this->idColumn);
            $ret['has_pk'] = false;
        } else {
            $ret['has_pk'] = true;
        }
        $cols = implode(',', $aCols);

        return $ret;
    }

    public function select($table_name = '')
    {
        $table_name = ($talbe_name) ? $table_name : $this->table_name(true);
        $adapter = new dbeav_select_mysql();
        $obj = vmc::singleton('dbeav_select')->set_adapter($adapter);

        return $obj->set_model($this)->reset()->from($table_name);
    }//End Function


    public function save(&$data, $mustUpdate = null, $mustInsert = false)
    {
        // ִsave
        $this->_save_parent($data, $mustUpdate, $mustInsert);
        $plainData = $this->sdf_to_plain($data);

        if (!$this->db_save($plainData, ($mustUpdate ? $this->sdf_to_plain($mustUpdate) : null), $mustInsert)) {
            return false;
        }
        if (!is_array($this->idColumn)) {
            if (!$data[$this->idColumn]) {
                $data[$this->idColumn] = $plainData[$this->idColumn];
            }
            $this->_save_depends($data, $mustUpdate);
        }
        $plainData = null;

        return true;
    }

    public function _save_parent(&$data, $mustUpdate, $mustInsert = false)
    {
        foreach ((array) $this->has_parent as $k => $v) {
            if (!isset($data[$k])) {
                continue;
            }
            $parentModel = explode('@', $v);
            $model = app::get($parentModel[1] ? $parentModel[1] : $this->app->app_id)->model($parentModel[0]);
            $model->save($data[$k], $mustUpdate, $mustInsert);
            foreach ($this->_columns() as $ck => $cv) {
                if (in_array($cv['type'], array('table:'.$parentModel[0], 'table:'.$parentModel[0].'@'.($parentModel[1] ? $parentModel[1] : $this->app->app_id)))) {
                    if ($cv['sdfpath']) {
                        eval('$data["'.implode('"]["', explode('/', $cv['sdfpath'])).'"] = $data[$k][$model->idColumn]; ');
                    } else {
                        $data[$ck] = $data[$k][$model->idColumn];
                    }
                    break;
                }
            }
        }
    }

    public function _save_depends(&$data, $mustUpdate = null, $mustInsert = false)
    {
        foreach (array_merge((array) $this->has_many, (array) $this->has_one) as $mk => $mv) {
            $mkKeys = explode('/', $mk);
            $mkKey = array_pop($mkKeys);
            eval(' $mkKeyExists = array_key_exists( $mkKey,(array)$data'.($mkKeys ? '["'.implode('"]["', $mkKeys).'"]' : '').' ); ');
            if ($mkKeyExists) {
                $itemdata = utils::apath($data, explode('/', $mk));
                $mv = explode(':', $mv);

                $subInfo = explode('@', $mv[0]);
                $obj = app::get(($subInfo[1] ? $subInfo[1] : $this->app->app_id))->model($subInfo[0]);

                $repId = array();
                $pkey = $this->_getPkey($mv[0], $mv[2], $appId);
                switch ($mv[1]) {
                    case 'contrast':
                        $repId = $obj->getList(implode(',', (array) $obj->idColumn), array($pkey['c'] => $data[$pkey['p']]), 0, -1);
                        break;
                    case 'replace':
                        $obj->delete(array($pkey['c'] => $data[$pkey['p']]));
                        break;
                }
                if (!isset($this->has_many[$mk])) {
                    $itemdata = array((array) $itemdata);
                }
                $defaultDataId = array();
                $contrastSaveData = array();
                foreach ((array) $itemdata as $mconk => $mconv) {
                    $mconv[$pkey['c']] = $data[$pkey['p']];
                    foreach ((array) $obj->idColumn as $acIdColumn) {
                        $defaultDataId[$acIdColumn] = $mconv[$acIdColumn];
                    }
                    if (!empty($repId) && ($hasDefId = array_search($defaultDataId, $repId)) !== false) {
                        unset($repId[$hasDefId]);
                    }
                    eval(' $subMustUpdate = $data'.($mkKeys ? '["'.implode('"]["', $mkKeys).'"]' : '').'["'.$mkKey.'"] ; ');
                    $contrastSaveData[$mconk] = array($mconv,$subMustUpdate);
//                    $obj->save($mconv,$subMustUpdate);
                }
                if($repId){
                    foreach ((array) $repId as $aRepId) {
                        $obj->delete($aRepId);
                    }
                }
                if ($contrastSaveData) {
                    foreach ($contrastSaveData as $contrastSaveDatak => $contrastSaveDatav) {
                        $obj->save($contrastSaveDatav[0], $contrastSaveDatav[1], $mustInsert);
                        eval(' $data["'.implode('"]["', explode('/', $mk)).'"][$contrastSaveDatak] = $contrastSaveDatav[0]; ');
                        if ($contrastSaveDatav[0]['default']) {
                            $this->set_default($data[$pkey['p']], $defaultDataId);
                        }
                    }
                }
                unset($obj);
            }
        }
    }

    public function dump($filter, $field = '*', $subSdf = null)
    {
        if (!$filter || (is_array($filter) && count($filter) == 1 && !current($filter))) {
            return;
        }
        //todo:ever need check
        if (!is_array($filter)) {
            $filter = array($this->idColumn => $filter);
        }

        $field = explode(':', $field);
        $unfield = $field[1];
        $field = $field[0];

        $data = $this->db_dump($filter, $field);

        if (!$data) {
            return;
        }

        $redata = $this->plain_to_sdf($data);
        if ($subSdf && !is_array($subSdf)) {
            $subSdf = $this->getSubSdf($subSdf);
        }
        if ($subSdf) {
            $this->_dump_depends($data, $subSdf, $redata);
        }
        $tCols = $this->_columns();
        if ($unfield) {
            foreach (explode(',', $unfield) as $k => $v) {
                $v = trim($v);
                if ($tCols[$v]) {
                    if ($tCols[$v]['sdfpath']) {
                        eval('unset( $redata["'.str_replace('/', '"]["', $tCols[$v]['sdfpath']).'"]);');
                    } else {
                        unset($redata[$v]);
                    }
                }
            }
        }

        return $redata;
    }

    public function _dump_depends(&$data, $subSdf, &$redata)
    {
        $has_col = array_merge((array) $this->has_many, (array) $this->has_one);
        foreach ((array) $subSdf as $subSdfKey => $subSdfVal) {
            $filter = null;
            if (isset($has_col[$subSdfKey])) {
                $subInfo = explode(':', $has_col[$subSdfKey]);
                $appId = explode('@', $subInfo[0]);
                if (count($appId) > 1) {
                    $subInfo[0] = $appId[0];
                    $appId = $appId[1];
                } else {
                    $appId = null;
                }
                $pkey = $this->_getPkey($subInfo[0], $subInfo[2], $appId);
                $filter[$pkey['c']] = $data[$pkey['p']];

                if (method_exists($this, '_dump_depends_'.$subInfo[0])) {
                    eval('$this->_dump_depends_'.$subInfo[0].'($data,$redata,$filter,$subSdfKey,$subSdfVal);');
                //    $this->_dump_depends_.$subInfo[0]($data,$filter,$subSdf,$subSdfVal);
                } else {
                    $subObj = app::get($appId ? $appId : $this->app->app_id)->model($subInfo[0]);
                    $start = '0';
                    $limit = '-1';
                    $orderBy = null;
                    if ($subSdfVal[2]) {
                        if (isset($subSdfVal[2][0])) {
                            $start = $subSdfVal[2][0];
                        }
                        if (isset($subSdfVal[2][1])) {
                            $limit = $subSdfVal[2][1];
                        }
                        if (isset($subSdfVal[2][2])) {
                            $orderBy = $subSdfVal[2][2];
                        }
                    }
                    $idArray = $subObj->getList(implode(',', (array) $subObj->idColumn), $filter, $start, $limit, $orderBy);

                    if ($idArray) {
                        foreach ($idArray as $aIdArray) {
                            $subDump = $subObj->dump($aIdArray, $subSdfVal[0], $subSdfVal[1]);
                            if ($this->has_many[$subSdfKey]) {
                                switch (count($aIdArray)) {
                                case 1:
                                    $subSdfKeyChild = '["'.current($aIdArray).'"]';
                                    break;
                                case 2:
                                    $subSdfKeyChild = '["'.current(array_diff_assoc($aIdArray, $filter)).'"]';
                                    break;
                                default:
                                    $subSdfKeyChild = '[]';
                                    break;
                            }
                            } else {
                                $subSdfKeyChild = '';
                            }
                            eval('$redata["'.strtr($subSdfKey, array('/' => '"]["')).'"]'.$subSdfKeyChild.' = $subDump; ');
                        }
                    }
                }
            }
            if (strpos($subSdfKey, ':') !== false) {
                $subSdfKey = explode(':', $subSdfKey);
                $tableName = $subSdfKey[1];
                $subSdfKey = $subSdfKey[0];

                $appId = explode('@', $tableName);
                if (count($appId) > 1) {
                    $tableName = $appId[0];
                    $appId = $appId[1];
                } else {
                    $appId = null;
                }

                $subObj = app::get($appId ? $appId : $this->app->app_id)->model($tableName);
                $tCols = $this->_columns();
                foreach ($tCols as $tCol => $tVal) {
                    if ($tVal['type'] == 'table:'.$tableName.($appId ? '@'.$appId : '')) {
                        $pkey = array(
                            'p' => $tCol,
                            'c' => $subObj->idColumn,
                        );
                        if (!$subSdfKey) {
                            if ($tVal['sdfpath']) {
                                $subSdfKey = substr($tVal['sdfpath'], 0, strpos($tVal['sdfpath'], '/'));
                            } elseif ($tableName == $tCol) {
                                $subSdfKey = '_'.$tableName;
                            } else {
                                $subSdfKey = $tableName;
                            }
                        }
                        break;
                    }
                }
                $filter[$pkey['c']] = $data[$pkey['p']];
                $redata[$subSdfKey] = $subObj->dump($filter, $subSdfVal[0], $subSdfVal[1]);
            }

            unset($subObj);
        }
    }

    public function _getPkey($tableName, $cCol, $appId)
    {
        if ($cCol) {
            $pkey = explode('^', $cCol);

            return array('p' => $pkey[0],'c' => $pkey[1]);
        }
        $basetable = 'table:'.$this->table_name();

        $oDbTable = new base_application_dbtable();
        $itemdefine = $oDbTable->detect(($appId ? $appId : $this->app->app_id), $tableName)->load();

        foreach ($itemdefine['columns'] as $k => $v) {
            if ($v['type'] == $basetable || $v['type'] == $basetable.'@'.$this->app->app_id) {
                $pk = substr($v['type'], strlen($v['type']));
                $pkey = array(
                    'p' => $pk ? $pk : $this->idColumn,
                    'c' => $k,
                );
                break;
            }
        }

        return $pkey;
    }

    public function set_default($parentId, $defaultDataId)
    {
        return true;
    }

    public function apply_pipe($action, &$data)
    {
    }

    public function sdf_to_plain($data, $appends = false)
    {
        foreach ($this->_columns() as $k => $v) {
            $map[$k] = $v['sdfpath'] ? $v['sdfpath'] : $k;
        }
        if ($appends) {
            $map = array_merge($map, (array) $appends);
        }
        $return = array();

        foreach ($map as $k => $v) {
            $ret = utils::apath($data, explode('/', $v));
            if ($ret !== false) {
                $return[$k] = $ret;
            }
        }

        return $return;
    }

    public function plain_to_sdf($data, $appends = false)
    {
        foreach ($this->_columns() as $k => $v) {
            $map[$k] = isset($v['sdfpath']) ? $v['sdfpath'] : $k;
        }
        if ($appends) {
            $map = array_merge($map, (array) $appends);
        }
        $return = array();
        foreach ($map as $k => $v) {
            if (utils::unapath($data, $k, explode('/', $v), $ret)) {
                $return = array_merge_recursive($return, $ret);
            }
        }
        $return = array_merge($return, $data);

        return $return;
    }

    public function getSubSdf($key)
    {
        if (array_key_exists($key, (array) $this->subSdf)) {
            return $this->subSdf[$key];
        } elseif ($this->subSdf['default']) {
            return $this->subSdf['default'];
        }
        $subSdf = array();
        foreach (array_merge((array) $this->has_many, (array) $this->has_one) as $k => $v) {
            $subSdf[$k] = array('*');
        }

        return $subSdf ? $subSdf : null;
    }

    public function batch_dump($filter, $field = '*', $subSdf = null, $start = 0, $limit = 20, $orderType = null)
    {
        $aId = $this->getList(implode(',', (array) $this->idColumn), $filter, $start, $limit, $orderType);
        $rs = array();
        foreach ($aId as $id) {
            $rs[] = $this->dump($id, $field, $subSdf);
        }

        return $rs;
    }
    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                $columns[$k] = $v['label'];
            }
        }

        return $columns;
    }
}
