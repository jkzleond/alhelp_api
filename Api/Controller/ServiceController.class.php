<?php
namespace Api\Controller;

class ServiceController extends ApiBaseController {
	//发布服务、需求
	public function release_post() {

		$this->check_token();

		$data = $this->get_request_data();

		$m = M("member");

		$info = $m->getById($this->uid);

		$content_demand = $data['content'];
		$description = trim($data['description']);

		if (!is_numeric($data['cost']) && $data['cost'] < 0) {
			$this->error(6002);
		}

		$profes_type = $data['profes_type']; //专业类型  1.非统考 2.统考 3.公共课

		$profes_data = $data['profes_data'];

		(!is_array($profes_data)) && $this->error(6006);

		if ($profes_type == 1) {

			if (!isset($profes_data['city']) || 0 == $profes_data['city']) {
				$this->error(6007);
			}

			empty($profes_data['university']) && $this->error(6008);

			$college = $profes_data['college'];
			$university = $profes_data['university'];

		} else if ($profes_type == 2) {

			empty($profes_data['major_subject']) && $this->error(6009);
			empty($profes_data['major_classify']) && $this->error(6010);

			$profes_data['major'] = $profes_data['major_subject'];
			$code = get_info('unified_classify', array(
				'id' => $profes_data['major_classify'],
			));
			$profes_data['major_code'] = $code['code'];
			$profes_data['college'] = $profes_data['major_classify'];
		} else if ($profes_type == 3) {
			$profes_data['major'] = $profes_data['major_subject'];
		}

		empty($description) && $this->error(6004);
		mb_strlen($description, "utf-8") > 20 && $this->error(6005);

		if ($data['demand_type'] < 4) //服务类别 1.资料  2.答疑 3.授课 4.直播课
		{
			empty($content_demand) && $this->error(6011);

		} else {
			empty($data['content_course']) && $this->error(6012);
			empty($data['content_reference']) && $this->error(6013);
			empty($data['yy_number']) && $this->error(6014);
		}

		if ($data['role_type'] == 2) {
			if (empty($data['image_sha1']) || !count($data['image_sha1'])) {
				$this->error(6015);
			}
		}

		$demand = M("demand");
		$demand_ins_data = array(
			"demand_type" => $data['demand_type'],
			"profes_type" => $data['profes_type'],
			"city" => $profes_data['city'],
			"university" => $profes_data['university'],
			"college" => $profes_data['college'],
			"major" => $profes_data['major'],
			"major_code" => $profes_data['major_code'],
			"description" => $data['description'],
			"cost_type" => $data['cost'] > 0 ? 2 : 1,
			"cost" => $data['cost'],
			"qq" => $data['qq'],
			"mobile" => $data['phone'],
			"content_demand" => $data['content'],
			"member_id" => $this->uid,
			"member_name" => $info["nickname"],
			"role_type" => $data['role_type'],
			"require_identity" => $data['require_identity'],
			"require_authenticate" => $data['require_authenticate'],
			"require_security" => $data['require_security'],
		);

		if ($data['demand_type'] == 4) {
			$demand_ins_data['content_course'] = $data["content_course"];
			$demand_ins_data['content_reference'] = $data["content_reference"];
			$demand_ins_data['remarks'] = $data["remarks"];
			$demand_ins_data['set_time'] = max(0, intval($data['set_time']));
		}

		if (!$demand->add($demand_ins_data)) {
			$this->error(9001);
		}

		$res = $demand->getLastInsID();

		$qq = $data['qq'];
		if (empty($info['qq'])) //用户以前没有填写QQ,现在填了,更新一下
		{
			$m->where("id=" . $this->uid)->save(array("qq" => $qq));
		}

		if (1 == $data["role_type"]) {
			$title = '您的需求"' . $description . '"已发布成功';
		} else {
			$title = '你的辅导项目"' . $description . '"已发布成功';
			if ($data['demand_type'] == 4) {
				$title = $description . '已发布成功！我们将在24小时内进行审核！审核成功后将发布在首页。审核成功后我们会给您安排YY频道的YY频道3503895的一间直播教室。';
			}
		}

		//发送系统消息
		M('message')->add(array(
			"from_member_id" => 0,
			"to_member_id" => $info['id'],
			'content' => $title,
			'table' => $this->table,
			'role' => $dsts['role_type'],
			'table_id' => $res,
			'add_time' => date('Y-m-d H:i:s'),
		));

		$image = $data['image_sha1'];
		//将图片写入数据库
		if (!empty($image)) {
			$att = M("attachments");
			$att_data = array(
				"member_id" => $info["id"],
				"table" => "demand",
				"table_id" => $res,
				"sha1" => $image,
				"add_time" => date("Y-m-d H:i:s"),
			);

			$att->add($att_data);
		}

		// 更新发布数
		if ($info['wap_role'] == 1) //学生发布
		{
			$this->upNumber('member', array(
				'id' => $this->uid,
			), 'demand_num');

			//更新最后发布服务的时间
			M('member')->where(array(
				'id' => $this->uid,
			))->save(array(
				'last_serve_time' => time(),
			)); // 根据条件保存修改的数据
		} else {
			$this->upNumber('member', array(
				'id' => $this->uid,
			), 'serve_num');
		}
		//用户发了需求/服务之后,要把用户放到所在的圈子里,假如没在的情况下

		if ($data['profes_type'] == 1) {
			if ($college) {
				$community = get_info('community', array(
					'member_id' => $info['id'],
					'table_type' => 'school',
					'table_id' => $college,
					'critype' => 2,
					'status' => 1,
				), 'id');
				if (!$community) {
					M('community')->add(array(
						'member_id' => $info['id'],
						'table_type' => 'school',
						'table_id' => $college,
						'critype' => 2,
						'add_time' => date('Y-m-d H:i:s'),
					));
				}

			} else {
				$community = get_info('community', array(
					'member_id' => $info['id'],
					'table_type' => 'school',
					'table_id' => $university,
					'critype' => 1,
					'status' => 1,
				), 'id');
				if (!$community) {
					M('community')->add(array(
						'member_id' => $info['id'],
						'table_type' => 'school',
						'table_id' => $university,
						'critype' => 1,
						'add_time' => date('Y-m-d H:i:s'),
					));
				}

			}

		}
		//发布成功送积分
		$param = array(
			'uid' => $info['id'],
			'tablename' => 'demand',
			'tableid' => $res,
			'uname' => $info['nickname'],
			'action' => MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME,
			'ruleId' => 36,
			'field' => 'score',
			'isLimit' => false,
		);
		tag('setScore', $param);

		if ($info['pid'] != 0 && strtotime($info['reg_time']) + 365 * 24 * 3600 > time()) {
			if ($info['role'] == 1) {
				$reward_setting = 'set_stu_publish';
				$reward_msg = "推广用户发布需求获取金币";
				$t_res = query_sql("", "select * from extend_reward where set_name='set_stu_publish' and from_member_id=" . $info['id'] . " and status=1");
			} else {
				$reward_msg = "推广用户发布服务获取金币";
				$reward_setting = 'set_tea_publish';
				$t_res = query_sql("", "select * from extend_reward where set_name='set_tea_publish' and from_member_id=" . $info['id'] . " and status=1");
			}

			if (!$t_res) {
				$extend_reward = M('extend_reward')->add(array(
					'from_member_id' => $info['id'],
					'to_member_id' => $info['pid'],
					'set_name' => $reward_setting,
					'add_time' => date('Y-m-d H:i:s'),
					'role' => $info['role'],
					'table_name' => 'demand',
					'table_id' => $res,
				));
				$data['coin'] = array(
					'exp',
					'coin+' . C($reward_setting),
				);
				//M('member')->
				$pname = get_info('member', array(
					'id' => $info['pid'],
				), 'nickname');
				D('score_log')->add(array(
					'uid' => $info['pid'],
					'table_name' => 'extend_reward',
					'table_id' => $extend_reward,
					'uname' => $pname['nickname'],
					'action' => MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME,
					'type' => 'coin',
					'score' => C($reward_setting),
					'rule_id' => 34,
					'msg' => $reward_msg,
					'create_time' => time(),
				));

				//更新最后发布时间
				M('member')->where('id=' . $info['id'])->save(array(
					'last_serve_time' => time(),
				));

			}
		}

		$this->success(array("id" => $res));

	}

	//更新数量
	private function upNumber($table, $where, $field, $num = 1, $into = true) {
		if (empty($table) || empty($where) || empty($field)) {
			return false;
		}
		$obj = M($table);
		if ($into) {
			return $obj->where($where)->setInc($field, $num);
		} else {
			return $obj->where($where)->setDec($field, $num);
		}
	}
}