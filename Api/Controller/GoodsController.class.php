<?php
namespace Api\Controller;

class GoodsController extends ApiBaseController {
	protected $is_check_token = true;

	public function add_post() {
		$a_user = intval($this->get_request_data("a_user_id"));
		$b_user = intval($this->get_request_data("b_user_id"));
		$goods_id = intval($this->get_request_data("goods_id"));

		if ($a_user > 0 && $b_user > 0 && $goods_id > 0) {

			$type = M("demand")->getFieldById($goods_id, "demand_type");

			if ($type <= 0) {
				$this->error(7001);
			}

			$ret = M("goods")->add(
				array(
					"a_member_id" => $a_user,
					"b_member_id" => $b_user,
					"goods_id" => $goods_id,
					"type" => $type,
					"add_time" => time(),
				)
			);
			if ($ret) {
				$this->success();
			}

			$this->error(9001);
		}

		$this->error(1001);
	}

	public function goods_get() {
		$a_user = I("get.a_user_id", 0, "intval");
		$b_user = I("get.b_user_id", 0, "intval");

		if ($a_user > 0 && $b_user > 0) {
			$ret = M("goods")->where(
				array(
					"a_member_id" => $a_user,
					"b_member_id" => $b_user,
				)
			)
				->order("add_time desc")->find();
			if ($ret) {
				$this->success(array("type" => $ret["type"], "id" => $ret["goods_id"]));
			}

			$this->error(1404);
		}

		$this->error(1001);
	}

	public function goods_list_get() {
		$user_id = I("get.user_id", false, "intval");
		$type_id = I("get.type_id", false, "intval");

		if ($a_user > 0 && $type_id > 0) {

			$list = M("goods")
				->alias('g')
				->where(
					array(
						"g.a_member_id|g.b_user_id" => $a_user,
						"g.type" => $type_id,
					)
				)
				->join("inner join demand d on d.id=g.goods_id")

				->join("left join school slc on slc.id=g.city")
				->join("left join school slu on slu.id=g.university")
				->join("left join school slcc on slcc.id=g.college")
				->join("left join school slm on slm.id=g.major")

				->field("d.id,d.description,slc.title_fix as city,slu.title_fix as university,slcc.title_fix as college,slm.title_fix as major")
				->order("add_time desc")
				->select();

			$this->success($list);
		}

		$this->error(1001);
	}
}