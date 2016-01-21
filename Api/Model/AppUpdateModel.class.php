<?php
namespace Api\Model;
use Think\Model;

class AppUpdateModel extends BaseModel {

	protected $tableName = 'app_update';

	public function get_new_update() {
		return $this->field('version,update_url,update_time,version_info')->limit(1)->order('id desc')->select();
	}

}