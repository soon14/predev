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


class community_mdl_blog extends dbeav_model
{
    public $defaultOrder = array(
        ' createtime',
        'DESC',
    );
    public $has_many = array(
        'blog_tag' => 'blog_tag:replace',
    );
    public function apply_id()
    {
        $tb = $this->table_name(1);
        do {
            $i = substr(mt_rand(), -3);
            $blog_id = date('ymdHis').$i;
            $row = $this->db->selectrow('SELECT blog_id from '.$tb.' where blog_id ='.$blog_id);
        } while ($row);

        return $blog_id;
    }
    public function get_bloglist($filter
 = array(), $offset = 0, $limit = -1, $orderType = null,&$count)
    {

        if($filter['_LT_RELATION']){
            $relation_user_id = $filter['_LT_RELATION'];
            unset($filter['_LT_RELATION']);
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $where_str = $dbeav_filter->dbeav_filter_parser($filter, 'b', false, $this);
            $SELECT_SQL = "SELECT r.user_id,r.relation_id,b.* FROM vmc_community_relation AS r LEFT JOIN vmc_community_blog AS b ON r.relation_id = b.author WHERE r.user_id = $relation_user_id AND ".$where_str;
            $COUNT_SQL = "SELECT count(r.user_id) AS count,r.relation_id,b.author FROM vmc_community_relation AS r LEFT JOIN vmc_community_blog AS b ON r.relation_id = b.author WHERE r.user_id = $relation_user_id AND ".$where_str;
            $SELECT_SQL.='ORDER BY b.createtime DESC';
            if($orderType){
                $SELECT_SQL.=','.$orderType;
            }
            //vmc::dump($SELECT_SQL);exit;
            $blog_list = $this->db->selectLimit($SELECT_SQL, $limit, $offset);
            $row = $this->db->selectrow($COUNT_SQL);
            $count = $row['count'];
        }elseif($filter['_LT_LOCAL']){
            $lng = $filter['_LT_LOCAL'][0];
            $lat = $filter['_LT_LOCAL'][1];
            unset($filter['_LT_LOCAL']);
            $distance_col_sql = "ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-latitude*PI()/180)/2),2)+COS($lat*PI()/180)*COS(latitude*PI()/180)*POW(SIN(($lng*PI()/180-longitude*PI()/180)/2),2))))* 1000 AS distance";
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $filter['longitude|notin'] = array('null',NULL,'');
            $filter['latitude|notin'] = array('null',NULL,'');
            $where_str = $dbeav_filter->dbeav_filter_parser($filter, null, false, $this);
            $SELECT_SQL = "SELECT $distance_col_sql,vmc_community_blog.* FROM vmc_community_blog WHERE ".$where_str;
            if($orderType){
                $SELECT_SQL.='ORDER BY distance,'.$orderType;
            }else{
                $SELECT_SQL.='ORDER BY distance';
            }

            $blog_list = $this->db->selectLimit($SELECT_SQL, $limit, $offset);
            $count = $this->count($filter);
        }else{
            $blog_list = parent::getList('*', $filter, $offset, $limit, $orderType);
            $count = parent::count($filter);
        }

        if (empty($blog_list)) {
            return false;
        }
        $blog_id_arr = array_keys(utils::array_change_key($blog_list, 'blog_id'));
        $author_arr = array_keys(utils::array_change_key($blog_list, 'author'));
        $media_audio_list = app::get('community')->model('media_audio')->getList('*', array('target_id' => $blog_id_arr));
        $media_video_list = app::get('community')->model('media_video')->getList('*', array('target_id' => $blog_id_arr));
        $media_image_attach_list = app::get('community')->model('media_image_attach')->getList('*', array('target_id' => $blog_id_arr));
        $users = app::get('community')->model('users')->getListPlus('*', array('user_id' => $author_arr));

        return array(
            'blog_list' => $blog_list,
            'author_list' => utils::array_change_key($users, 'user_id'),
            'media_audio' => utils::array_change_key($media_audio_list, 'target_id'),
            'media_video' => utils::array_change_key($media_video_list, 'target_id'),
            'media_images' => utils::array_change_key($media_image_attach_list, 'target_id', true),
        );
    }
    public function update_blog_count($blog_id,$target_col,$val){
        if(empty($blog_id)){
            return false;
        }
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $table_name = $this->table_name(true);
        //$where_str = $dbeav_filter->dbeav_filter_parser($filter, null, null, $this);
        $SQL = "UPDATE $table_name SET $target_col = (IFNULL($target_col,0) + $val) WHERE blog_id = $blog_id";
        return $this->db->exec($SQL);
    }
}
