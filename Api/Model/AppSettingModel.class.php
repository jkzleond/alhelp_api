<?php
namespace Api\Model;
use Think\Model;

class AppSettingModel extends BaseModel {

	protected $tableName = 'app_setting';

	public function get_update_version() {
		return $this->where(array('item' => 'version'))->find();
	}
	public function get_update_url() {
		return $this->where(array('item' => 'update_url'))->find();
	}
}