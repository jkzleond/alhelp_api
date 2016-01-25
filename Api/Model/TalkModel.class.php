<?php
namespace Api\Model;
use Think\Model;

class UserModel extends BaseModel {

	protected $tableName = 'member_post';

	public $fields_can_update = array(
		"member_id", "content"
	);

	public $fields_can_get = array(
		"id", "member_id", "member_nickname", "content", "praise_num", "replies_num", "add_time", "type"
	);

	/**
	 * 获取某个用户的所有说说列表
	 * @param  integer $member_id 指定用户id
	 */
	public function list($member_id){
		$data = $this->where(array('member_id' => $member_id))->select();
		$data_length = count($data);
		for ($i=0; $i < $data_length; $i++) { 
			$data[$i] = $this->filter($data[$i], $this->fields_can_get);
		}
		return $data;
	}
}