<?php
namespace Common\Controller;

class DataObj implements \ArrayAccess {

	protected $_elements = array();

	function __construct($arr) {
		$this->_elements = $arr;
	}

	public function offsetExists($offset) {
		return isset($this->_elements[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->_elements[$offset]) ? $this->_elements[$offset] : null;
	}
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->_elements[] = $value;
		} else {
			$this->_elements[$offset] = $value;
		}
	}
	public function offsetUnset($offset) {
		unset($this->_elements[$offset]);
	}

	public function __get($name) {
		return isset($this->_elements[$name]) ? $this->_elements[$name] : null;
	}

	public function __set($name, $value) {
		return $this->_elements[$name] = $value;
	}
}