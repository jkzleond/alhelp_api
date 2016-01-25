<?php
namespace Api\Model;
use Think\Model;

class CommunityModel extends BaseModel {

	protected $tableName = 'community';

	public $fields_can_get = array(
		'id', 'add_time', 'label', 'type',
	);

	public function communities($member_id, $lock = false) {
		if ($lock) {
			$data = $this->field('community.*')
				->join('INNER JOIN member ON member.id = community.member_id AND member.community_id = community.id')
				->where(array('member.id' => $member_id))
				->find();
			$data['label'] = $this->_get_info($data['type'], $member_id);
			$data['id'] = $data['table_id'];
			$data = $this->filter($data, $this->fields_can_get);
			return $data;
		}
		$data = $this->where(array('member_id' => $member_id))->order('id')->select();
		$data_length = count($data);
		for ($i = 0; $i < $data_length; $i++) {
			$data[$i]['label'] = $this->_get_info($data[$i]['type'], $member_id);
			$data[$i]['id'] = $data[$i]['table_id'];
			$data[$i] = $this->filter($data[$i], $this->fields_can_get);
		}
		return $data;
	}

	public function getcommunityInfo($id) {
		$data = $this->where(array('id' => $id))->find();
		return $this->_get_info($data["type"], $data["member_id"]);
	}

	protected function _get_info($community_type, $member_id) {
		$type_info = explode('_', $community_type);
		if ($type_info[0] == 'school') {
			$school = M('school');
			$path_info = $school->where(array('id' => $type_info[1]))->getFieldById($type_info[1], 'path');
			$path = explode('.', $path_info);
			$info = array();
			$path_length = count($path);
			for ($i = 0; $i < $path_length; $i++) {
				$school_info = $school->where(array("id" => $path[$i]))->find();
				if ($i == 0) {
					$info['city'] = $school_info['title_fix'];
				}
				if ($i == 1) {
					$info['school'] = $school_info['title_fix'];
					$info['website'] = $school_info['website'];
					$info['telephone'] = $school_info['telephone'];
					$info['address'] = $school_info['address'];
					$info['report_radio'] = $school_info['report_radio'];
				}
				if ($i == 2) {
					$info['college'] = $school_info['title_fix'];
				}
				if ($i == 3) {
					$info['major'] = $school_info['title_fix'];
				}
				$info['is_master'] = $school_info['group_member_id'] == $member_id ? true : false;
			}
			return $info;
		} else {
			return $community_type;
		}
	}

	protected function _community_check($member_id, $id) {
		$data = $this->where(array('member_id' => $member_id, 'table_id' => $id))->select();
		if ($data) {
			return true;
		} else {
			return false;
		}
	}

	public function community_join($member_id, $id) {

		// $this_community = $this->where(array('id' => $id))->find();
		// if ($this_community) {
		// 	if ($this_community['member_id'] == $member_id) {
		// 		return 3007;
		// 	}
		// 	unset($this_community['id']);
		// 	$this_community['member_id'] = $member_id;
		// 	$result                      = $this->data($this_community)->add();
		// 	return $result;
		// } else {
		// 	return 3006;
		// }

		$this_school = D('School')->where(array('id' => $id))->find();

		if (!$this_school) {
			return 3006;
		}

		$this_community = $this->where(array('table_id' => $id))->select();
		$this_community_length = count($this_community);
		if ($this_community) {
			for ($i = 0; $i < $this_community_length; $i++) {
				if ($this_community[$i]['member_id'] == $member_id) {
					return 3007;
				}
			}
			unset($this_community[0]['id']);
			$this_community[0]['member_id'] = $member_id;
			$this_community = $this_community[0];
		} else {
			$this_type = $this_school['id'] - 1;
			$path = explode('.', $this_school['path']);
			$path_length = count($path);
			$this_path = '';
			for ($i = 0; $i < $path_length; $i++) {
				$this_path .= $path[$i] . '.';
				$title = D('School')->getFieldByPath(substr($this_path, 0, strlen($this_path) - 1), 'title');
				$this_title .= $title . ' ';
			}

			$this_community = array(
				'member_id' => $member_id,
				'table_type' => 'school',
				'critype' => $this_school['type'],
				'status' => 1,
				'add_time' => date('Y-m-d H-i-s'),
				'table_id' => $this_school['id'],
				'title' => $this_title,
				'type' => 'school_' . $this_type,
			);
		}
		$result = $this->data($this_community)->add();
		return $result;
	}

	public function community_delete($member_id, $id) {
		if (!$this->_community_check($member_id, $id)) {
			return 3005;
		}

		$result = $this->where(array('member_id' => $member_id, 'id' => $id))->delete();
		return $result;
	}
}