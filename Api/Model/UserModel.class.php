<?php
namespace Api\Model;
use Think\Model;

class UserModel extends BaseModel {

	protected $tableName = 'member';

	protected $_validate = array(
		array('password', '6,30', 'password', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
		array('phone', '/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/', 'phone', self::MODEL_BOTH),
		array('nickname', '', 'username', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
		array('phone', '', 'phone', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
		array('email', '', 'email', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
	);

	protected $_auto = array(
		array('password', 'md5', 3, 'function'),
	);

	public $fields_can_update = array(
		'avator', 'phone', 'qq', 'email', 'gender', 'apps', 'nickname', 'fans_num', 'follow_num', 'is_teacher', 'is_student',
	);

	// 用户注册
	public function register($username, $password, $phone, $rec_code) {
		$result = $this->create(array('nickname' => $username, 'password' => $password, 'phone' => $phone, "status" => 1,'phone_verified'=>1));
		if (!$result) {
			return $this->getError();
		}

		if ($rec_code) {
			$rec_code = convDec($rec_code);
			if ($rec_code === false) {
				return "code";
			}
			$result["pid"] = convDec($rec_code);
		}

		$result = $this->add($result);
		return $result;
	}

	public function edit_password($where, $password) {

		$info = $this->where($where)->find();

		if (is_null($info)) {
			return 0;
		}

		$ret = $this->create(array('password' => $password));

		if ($info['password'] == $ret['password']) {
			return -1;
		}

		if ($ret === false) {
			return false;
		}

		if ($this->where($where)->setField($ret)) {
			return true;
		}
		return null;
	}

	// 获取用户详细信息
	// $id 用户id
	public function detail($id) {
		$this_user = $this->where(array('id' => $id))->find();
		$this_apps = M('member_bind')->field('type, keyid, info')->where(array('uid' => $id))->select();
		foreach ($this_apps as &$value) {
			$value['info'] = json_decode($value['info']);
		}
		$this_user['avator'] = GetSmallAvatar($id);
		$this_user['apps'] = $this_apps;
		return $this->filter($this_user);
	}

	public function get_userinfo($id, $field) {
		return $this->field($field)->where(array('id' => $id))->find();
	}

	// 更新用户信息
	// $id 用户id
	// 需要更新的数据
	public function update($id, $data) {
		if (array_key_exists('apps', $data)) {
			return 3004;
		}
		$result = $this->where(array('id' => $id))->data($this->filter($data))->save();
		return $result;
	}

	public function set_paypwd($id, $data) {
		return $this->where(array('id' => $id))->save($data);
	}

	public function chk_paypwd($id) {
		return $this->field('paypassword')->where(array('id' => $id))->find();
	}

}