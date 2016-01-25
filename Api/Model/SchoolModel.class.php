<?php
namespace Api\Model;
use Think\Model;

class SchoolModel extends BaseModel {

	protected $tableName = 'school';

	public $fields_can_get = array(
		'id', 'title', 'initials', 'sort',
	);

	/**
	 * 获取所有省份
	 */
	public function provinces() {
		$provinces        = $this->where(array('type' => 1))->select();
		$provinces_length = count($provinces);
		for ($i = 0; $i < $provinces_length; $i++) {
			$provinces[$i] = $this->filter($provinces[$i], $this->fields_can_get);
		}
		return $provinces;
	}

	/**
	 * 获取学校列表
	 * @param  integer $province_id 省份id，如果不提供则获取所有学校
	 */
	public function schools($province_id = null) {
		if (!$province_id) {
			$schools = $this->where(array('type' => 2))->select();
		} else {
			$province = $this->where(array('type' => 1, 'id' => $province_id))->find();
			if (!$province) {
				return 3008;
			} else {
				$schools = $this->where(array('type' => 2, '_string' => "path REGEXP '^{$province_id}.[0-9]+$'"))->select();
			}
		}
		$schools_length = count($schools);
		for ($i = 0; $i < $schools_length; $i++) {
			$schools[$i] = $this->filter($schools[$i], array('id', 'title', 'initials', 'sort', 'report_radio', 'website', 'telephone', 'address'));
		}
		return $schools;
	}

	/**
	 * 获取指定学校的学院
	 * @param  integer $school_id 指定学校
	 */
	public function colleges($school_id) {
		$school = $this->where(array('type' => 2, 'id' => $school_id))->find();
		if (!$school) {
			return 3009;
		}
		$colleges        = $this->where(array('type' => 3, '_string' => "path REGEXP '^[0-9]+.{$school_id}.[0-9]+$'"))->select();
		$colleges_length = count($colleges);
		for ($i = 0; $i < $colleges_length; $i++) {
			$colleges[$i] = $this->filter($colleges[$i], array('id', 'title', 'initials', 'sort', 'report_radio'));
		}
		return $colleges;
	}

	/**
	 * 获取指定学院的专业
	 * @param  integer $college_id 指定学院
	 */
	public function majors($college_id) {
		$college = $this->where(array('type' => 3, 'id' => $college_id))->find();
		if (!$college) {
			return 3010;
		}
		$majors        = $this->where(array('type' => 4, '_string' => "path REGEXP '^[0-9]+.[0-9]+.{$college_id}.[0-9]+$'"))->select();
		$majors_length = count($majors);
		for ($i = 0; $i < $majors_length; $i++) {
			$majors[$i] = $this->filter($majors[$i], array('id', 'title', 'initials', 'sort', 'report_radio'));
		}
		return $majors;
	}

	/**
	 * 由学校关键词搜索所给省份，获取准确的学校
	 * @param  string $keywords  需要查询的学校关键词
	 * @param  integer $province_id 指定的省份id
	 */
	public function schools_search($keywords, $province_id) {
		$province = $this->where(array('type' => 1, 'id' => $province_id))->find();
		if (!$province) {
			return 3008;
		} else {
			$path    = $province['path'];
			$schools = $this->where(array('type' => 2, '_string' => "title LIKE '%{$keywords}%' AND path REGEXP '^{$path}.[0-9]+$'"))->select();
		}
		$schools_length = count($schools);
		for ($i = 0; $i < $schools_length; $i++) {
			$schools[$i] = $this->filter($schools[$i], array('id', 'title', 'initials', 'sort', 'report_radio', 'website', 'telephone', 'address'));
		}
		return $schools;
	}

	/**
	 * 由学院关键词搜索所给学校，获取准确的学院
	 * @param  string $keywords  需要查询的学院关键词
	 * @param  integer $school_id 指定的学校id
	 */
	public function colleges_search($keywords, $school_id) {
		$school = $this->where(array('type' => 2, 'id' => $school_id))->find();
		if (!$school) {
			return 3009;
		}
		$path            = $school['path'];
		$colleges        = $this->where(array('type' => 3, '_string' => "title LIKE '%{$keywords}%' AND path REGEXP '^[0-9]+.{$school_id}.[0-9]+$'"))->select();
		$colleges_length = count($colleges);
		for ($i = 0; $i < $colleges_length; $i++) {
			$colleges[$i] = $this->filter($colleges[$i], array('id', 'title', 'initials', 'sort', 'report_radio'));
		}
		return $colleges;
	}

	/**
	 * 由专业关键字搜索所给学院，获取准确的专业
	 * @param  string $keywords 需要查询的专业关键字
	 * @param  integer $college_id 指定学院id
	 */
	public function majors_search($keywords, $college_id) {
		$college_path  = $this->getFieldById($college_id, 'path');
		$majors        = $this->where(array('type' => 4, '_string' => "title LIKE '%{$keywords}%' AND path LIKE '{$college_path}%'"))->select();
		$majors_length = count($majors);
		for ($i = 0; $i < $majors_length; $i++) {
			$majors[$i] = $this->filter($majors[$i], array('id', 'title', 'initials', 'sort', 'report_radio'));
		}
		return $majors;
	}

	/**
	 * 由专业id获取所有设有该专业的学校
	 * @param  integer $major_id 专业id
	 * @param  integer $province_id 省份id
	 */
	public function major_to_schools($major_id, $province_id = null) {
		$this_major = $this->where(array('id' => $major_id))->find();
		if (!$this_major) {
			return 3011;
		}
		if ($province_id) {
			$this_province = $this->where(array('type' => 1, 'id' => $province_id))->find();
			if (!$this_province) {
				return 3008;
			}

			$this_title = $this_major['title'];
			$majors     = $this->where(array('type' => 4, '_string' => "title LIKE '%{$this_title}%' AND path REGEXP '^{$province_id}.[0-9]+.[0-9]+.[0-9]+$'"))->page('1, 20')->select();
		} else {
			$this_title = $this_major['title'];
			$majors     = $this->where(array('type' => 4, '_string' => "title LIKE '%{$this_title}%'"))->page('1, 20')->select();
		}
		$schools       = array();
		$majors_length = count($majors);
		for ($i = 0; $i < $majors_length; $i++) {
			$path   = explode('.', $majors[$i]['path']);
			$school = $this->where(array('path' => $path[0] . '.' . $path[1]))->find();
			array_push($schools, $this->filter($school, array('id', 'title', 'initials', 'sort', 'report_radio', 'website', 'telephone', 'address')));
		}
		return $schools;
	}

	/**
	 * 获取学校信息
	 * @param  integer $id 指定学校id
	 */
	public function school($id) {
		$this_school = $this->where(array('type' => 2, 'id' => $id))->find();

		if (!$this_school) {
			return 3009;
		} else {
			$this_school = $this->filter($this_school, array('id', 'title', 'initials', 'sort', 'report_radio', 'website', 'telephone', 'address'));
			return $this_school;
		}
	}

	/**
	 * 获取学校信息
	 * @param  integer $id 指定学院id
	 */
	public function college($id) {
		$this_college = $this->where(array('type' => 3, 'id' => $id))->find();

		if (!$this_college) {
			return 3010;
		} else {
			$this_college = $this->filter($this_college, array('id', 'title', 'initials', 'sort', 'report_radio'));
			return $this_college;
		}
	}

	/**
	 * 获取学校信息
	 * @param  integer $id 指定专业id
	 */
	public function major($id) {
		$this_major = $this->where(array('type' => 4, 'id' => $id))->find();

		if (!$this_major) {
			return 3011;
		} else {
			$this_major = $this->filter($this_major, array('id', 'title', 'initials', 'sort', 'report_radio'));
			return $this_major;
		}
	}

}