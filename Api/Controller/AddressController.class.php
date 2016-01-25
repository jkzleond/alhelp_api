<?php
namespace Api\Controller;

class AddressController extends ApiBaseController {

	protected $is_check_token = true;

	protected $model = null;

	public function __construct() {

		parent::__construct();

		$this->model = M();
	}

	/**
	 * 收货地址列表
	 */
	public function addressList_get() {

		$sql = 'SELECT a.id,a.member_id,a.address,a.postcode,a.name,a.defaultaddress,a.phone,a.add_time,a1.title province,a.province province_id,a2.title city,a.city city_id,a3.title area,a.area area_id FROM address a LEFT JOIN area a1 ON a1.id=a.province LEFT JOIN area a2 ON a2.id=a.city LEFT JOIN area a3 ON a3.id=a.area WHERE a.member_id=' . $this->uid;

		$list = $this->model->query($sql);

		if ($list !== false) {

			$this->success($list);

		} else {

			$this->error(9001);
		}
	}

	/**
	 * 收货地址详情
	 */
	public function addressInfo_get() {

		$id = I('get.id');

		if (empty($id)) {
			$this->error(1001);
		}

		$sql = 'SELECT a.id,a.member_id,a.address,a.postcode,a.name,a.defaultaddress,a.phone,a.add_time,a1.pid province,a.province province_id,a2.pid city,a.city city_id,a3.pid area,a.area area_id FROM address a LEFT JOIN area a1 ON a1.id=a.province LEFT JOIN area a2 ON a2.id=a.city LEFT JOIN area a3 ON a3.id=a.area WHERE a.member_id=' . $this->uid . ' AND a.id=' . $id;

		$list = $this->model->query($sql);

		$sql = "SELECT id,title FROM area WHERE pid=";

		$list[0]['province'] = $this->model->query($sql . $list[0]['province']);

		$list[0]['city'] = $this->model->query($sql . $list[0]['city']);

		$list[0]['area'] = $this->model->query($sql . $list[0]['area']);

		if ($list !== false) {

			$this->success($list);

		} else {

			$this->error(9001);
		}
	}

	/**
	 * 获取省市县地址
	 * @param id int 父ID
	 */
	public function areas_get() {

		$id = intval(I('get.id'));

		$sql = 'SELECT id,title FROM area WHERE status=1 AND pid=' . $id . ' ORDER BY sort';

		$list = $this->model->query($sql);

		if ($list !== false) {

			$this->success($list);

		} else {

			$this->error(9001);
		}
	}

	/**
	 * 添加收货地址
	 */
	public function addressAdd_post() {

		$body = $this->get_request_data('address');

		if ($this->check_body_fields($body, array('province_id', 'city_id', 'area_id', 'address', 'postcode', 'name', 'phone', 'defaultaddress'))) {

			$add_time = date('Y-m-d H:i:s');

			$sql = "INSERT INTO address(`member_id`,`province`,`city`,`area`,`address`,`postcode`,`name`,`phone`,`defaultaddress`,`add_time`) VALUES('{$this->uid}','{$body['province_id']}','{$body['city_id']}','{$body['area_id']}','{$body['address']}','{$body['postcode']}','{$body['name']}','{$body['phone']}', 0,'{$add_time}')";

			if ($this->model->execute($sql)) {

				$id = $this->model->getLastInsID();

				if ($body['defaultaddress'] == 1) {
					$this->setDefault($id, $this->uid);
				}

				$this->success($id);

			} else {
				$this->error(9001);
			}

		} else {
			$this->error(1001);
		}

	}

	/**
	 * 删除收货地址
	 */
	public function addressDelete_delete() {

		$id = intval(I('get.id'));

		$sql = 'DELETE FROM address WHERE member_id=' . $this->uid . ' AND id=' . $id;

		if ($this->model->execute($sql)) {

			$this->success();

		} else {

			$this->error(9001);
		}
	}

	/**
	 * 修改收货地址
	 */
	public function addressEdit_put() {

		$body = $this->get_request_data("address");

		if ($this->check_body_fields($body, array('id', 'province_id', 'city_id', 'area_id', 'address', 'postcode', 'name', 'phone'))) {

			$sql = "UPDATE address SET `province`='{$body['province_id']}',`city`='{$body['city_id']}',`area`='{$body['area_id']}',`address`='{$body['address']}',`postcode`='{$body['postcode']}',`name`='{$body['name']}',`phone`='{$body['phone']}' WHERE id={$body['id']}";

			if ($this->model->execute($sql) !== false) {

				$this->success();

			} else {
				$this->error(9001);
			}

		} else {
			$this->error(1001);
		}

	}

	/**
	 * 设置为默认地址
	 */
	public function addressSetDefault_get() {

		$id = I('get.id');

		if ($this->setDefault($id, $this->uid)) {

			$this->success();

		} else {

			$this->error(9001);

		}
	}

	private function setDefault($id, $uid) {

		$sql = "UPDATE address SET defaultaddress=0 WHERE member_id={$uid}";

		$this->model->execute($sql);

		$sql = "UPDATE address SET defaultaddress=1 WHERE id={$id}";

		return $this->model->execute($sql);
	}
}