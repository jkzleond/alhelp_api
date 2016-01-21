<?php
namespace Api\Model;
use Think\Model;

class CardModel extends BaseModel {

	protected $tableName = 'card';

	public $fields_can_update = array(
		'nickname', 'signature', 'content',
	);

	public function info($id) {
		$info = $this->where(array('member_id' => $id))->find();
		return $this->filter($info);
	}

	public function update($id, $data) {
		$result = $this->where(array('member_id' => $id))->data($this->filter($data))->save();
		return $result;
	}
}