<?php
namespace Api\Model;
use Think\Model;

class BaseModel extends Model {

	public $fields_can_update = array();

	/**
	 * 字段过滤器
	 * @param  array $data      需要过滤的数组
	 * @param  array $condition 过滤条件，默认为$fields_can_update
	 */
	protected function filter($data, $condition = null) {
		if ($condition) {
			$condition = $condition;
		} else {
			$condition = $this->fields_can_update;
		}
		$result    = array();
		$data_keys = array_keys($data);
		foreach ($data as $key => $value) {
			if (in_array($key, $condition)) {
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * 生成API URL
	 * @param  string $path   地址
	 * @return string
	 */
	public function url($path) {
		return C("APP_DOMAIN.Api") . $path;
	}
}