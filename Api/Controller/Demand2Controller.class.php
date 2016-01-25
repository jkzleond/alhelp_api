<?php
namespace Api\Controller;

class Demand2Controller extends ApiBaseController {

	protected $demand_types = array(0, 1, 2, 3, 4);

	protected $role_types = array(0, 1, 2);

	/**
	 * 获取需求服务列表
	 */
	public function demands_get() {
		$type = intval(I('get.type'));
		$role = intval(I('get.role'));
		$page = intval(I('get.page'));
		$self = intval(I('get.self'));
		$uid = intval(I('get.uid'));
		if (!$role) {
			$role = 0;
		}
		if (!$type) {
			$type = 0;
		}
		if ($self) {
			$this->check_token();
			$uid = $this->uid;
		}

		$this->_demand_type_check($type);
		$this->_role_type_check($role);
		$data = D('Demand2')->demands($type, $role, $page, $uid);
		$this->success($data);
	}

	public function demand_get() {
		$id = intval(I('get.id'));
		$this_demand = D('Demand2')->get_by_id($id);
		if ($this_demand === 5997) {
			$this->error(5997);
		}
		$this->success($this_demand);
	}

	protected function _demand_type_check($type) {
		if (!in_array($type, $this->demand_types)) {
			$this->error(3012);
		}
	}

	protected function _role_type_check($role) {
		if (!in_array($role, $this->role_types)) {
			$this->error(3013);
		}
	}

	public function listens_get() {
		$demand_id = I('get.demand_id');
		$page = intval(I('get.page'));
		$result = D('Demand2')->listens_by_demand_id($demand_id, $page);
		if ($result === 5997) {
			$this->error(5997);
		}
		$this->success($result);
	}

	public function public_similar_get() {
		$public_id = I('get.public_id');
		$page = intval(I('get.page'));
		$result = D('Demand2')->public_similar($public_id, $page);
		if ($result === 3023) {
			$this->error(3023);
		}
		$this->success($result);
	}

	public function classifies_get() {
		$this->success(D('Demand2')->classify_list());
	}

	public function unified_get() {
		$classify_id = I('get.classify_id');
		$unified_id = I('get.unified_id');
		if ($classify_id && !$unified_id) {
			$result = D('Demand2')->unified_list($classify_id);
		}
		if (!$classify_id && $unified_id) {
			$result = D('Demand2')->unified_by_id($unified_id);
		}

		if ($result === 3025) {
			$this->error(3025);
		}
		if ($result === 3026) {
			$this->error(3026);
		}
		$this->success($result);
	}

	public function demand_by_unified_get() {
		$condition = I('get.');
		$result = D('Demand2')->get_by_unified(
			intval($condition['role']),
			intval($condition['type']),
			intval($condition['classify_id']),
			intval($condition['unified_id']),
			intval($condition['page'])
		);
		if (gettype($result) == 'integer') {
			$this->error($result);
		}
		$this->success($result);
	}

	public function demand_by_school_get() {
		$condition = I('get.');
		$result = D('Demand2')->get_by_school(
			intval($condition['role']),
			intval($condition['type']),
			intval($condition['province_id']),
			intval($condition['university_id']),
			intval($condition['college_id']),
			intval($condition['page'])
		);
		if (gettype($result) == 'integer') {
			$this->error($result);
		}
		$this->success($result);
	}

}