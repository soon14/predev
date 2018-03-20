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



class image_ctl_admin_manage extends desktop_controller
{

	/**
	 * act==index页面入口
	 * @param null
	 * @return string html内容
	 */
	function index(){
		$action = array(

		);
		$this->finder('image_mdl_image',array(
			'title'=>'图片管理',
			'actions'=>$action,
			'use_buildin_set_tag'=>true,
			'use_buildin_filter'=>true,
			'use_buildin_tagedit'=>true
		));
	}



/*new **/

	function upload($rebuild=false,$return_size = false){
		$image = $this->app->model('image');
		$image_name = $_FILES['files']['name'][0];
		$image_id = $image->store($_FILES['files']['tmp_name'][0],null,null,$image_name);
		if($_POST['tag']){
				$this->_set_tag($image_id,array($_POST['tag']));
		}
		if($rebuild){
			$image->rebuild($image_id,array('L','M','S','XS'));
		}
		if(!$return_size){
			echo json_encode(array('url'=>base_storager::image_path($image_id),'image_id'=>$image_id));
		}else{
			echo json_encode(array('url'=>base_storager::image_path($image_id,$return_size),'image_id'=>$image_id));
		}

		return $image_id;
	}

	function wysiswyg_upload(){
		$image = $this->app->model('image');
		$image_name = $_FILES['file']['name'];
		$image_id = $image->store($_FILES['file']['tmp_name'],null,null,$image_name);
		if(!isset($_POST['tag'])){
				$this->_set_tag($image_id,array('可视化编辑器内的图片'));
		}
		echo json_encode(array('url'=>base_storager::image_path($image_id,'l'),'image_id'=>$image_id));
	}


	function gimages_upload(){
		$image_id = $this->upload(true,'s');
		$this->_set_tag($image_id,array('商品相册图'));
	}



/*end new**/


	/**
	 * 设置图片的tag-本类私有方法
	 * @param null
	 * @return null
	 */
	function _set_tag($image_id,$tag_name){
		$tagctl   = app::get('desktop')->model('tag');
		$tag_rel   = app::get('desktop')->model('tag_rel');
		$data['rel_id'] = $image_id;
		$tags = is_array($tag_name)?$tag_name:explode(' ',$_POST['tag']['name']);
		$data['tag_type'] = 'image';
		$data['app_id'] = 'image';
		foreach($tags as $key=>$tag){
			if(!$tag) continue;
			$data['tag_name'] = $tag; //todo 避免重复标签新建
			$tagctl->save($data);
			if($data['tag_id']){
				$data2['tag']['tag_id'] = $data['tag_id'];
				$data2['rel_id'] = $image_id;
				$data2['tag_type'] = 'image';
				$data2['app_id'] = 'image';
				$tag_rel->save($data2);
				unset($data['tag_id']);
			}
		}
	}





	/**
	 * 图片大小配置
	 * @param nulll
	 * @return string 显示配置页面内容
	 */
	function imageset(){

		$image_set = $_POST['imageset'];
		if($image_set){
			$this->begin();
			foreach(vmc::servicelist('image_set') as $class_name=>$service){
				if($service instanceof image_interface_set){
					if(method_exists($service,'setconfig')){
						$service->setconfig($image_set);
					}
				}
			}
			$this->app->setConf('size',$image_set);
			$this->end(true,'保存成功');
		}
		$this->pagedata['storager'] =constant('FILE_STORAGER');
		$this->pagedata['this_url'] = $this->url;
		$this->page('imageset.html');
	}


}//End Class
