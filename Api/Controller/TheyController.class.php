<?php
namespace Api\Controller;

class TheyController extends ApiBaseController {

	protected $is_check_token = true;

	const TEACHER = 0; //教师身份
	const STUDENT = 1; //学生身份

	//他人操作的统一入口
	public function index_post() {
		$type = $this->get_request_data("type");
		switch ($type) {
			case 'theyInfo':
				$this->info();
				break;
			case 'getFollowStatus':
				$this->get_follow_status();
				break;
			case "setFollowStatus":
				$this->set_follow_status();
				break;
		}

		$this->error(1001);
	}

	//获取他人消息
	private function info() {
		$identity = intval($this->get_request_data("identity"));
		$nickname = mysql_escape_string($this->get_request_data("nickname"));

		if (empty($nickname)) {
			return;
		}

		$m = M('member');
		$m->alias('m')
			->join("left join school s_u on s_u.id=m.university_id")
			->join("left join school s_c on s_c.id=m.college_id")
			->join("left join (select _s_a.title,_s_u.id from school _s_a inner join school _s_u on _s_a.id=_s_u.pid) s_a on s_a.id=s_u.id")
			->field('s_a.title as schoolPlace,s_u.title as collegeName,s_c.title as schoolName,follow_num as followNum,fans_num as fansNum');

		if (self::TEACHER == $identity) {

			$data = $m->where(array(
				'm.is_teacher' => 1,
				'm.nickname' => $nickname,
			))->find();

			if ($data) {
				$this->success($data);
			} else {
				$this->error(1404);
			}
		} else if (self::STUDENT == $identity) {
			$data = $m->where(array(
				'is_student' => 1,
				'nickname' => $nickname,
			))->find();

			if ($data) {
				$this->success($data);
			} else {
				$this->error(1404);
			}
		}
	}

	//获取指定用户是否已关注
	private function get_follow_status() {
		$nickname = mysql_escape_string($this->get_request_data("nickname"));

		if (empty($nickname)) {
			return;
		}

		$options = array(
			'alias' => 'f',
			'join' => array(
				"INNER JOIN member m on f.to_member_id = m.id",
			),
			'where' => array("f.from_member_id" => $this->uid, "m.nickname" => $nickname),
		);

		$follow = M("follow");
		$follow->options = $options;

		if ($follow->count()) {
			$this->success(array("follow" => true));
		} else {
			$this->success(array("follow" => false));
		}
	}

	//设置关注指定用户
	private function set_follow_status() {
		$nickname = mysql_escape_string($this->get_request_data("nickname"));
		$status = $this->get_request_data("status") ? true : false;

		if (empty($nickname)) {
			return;
		}

		$fid = M('member')->getFieldByNickname($nickname, 'id');

		if ($fid) {
			$follow = M('follow');
			if ($status) {
				if ($follow->where(array('to_member_id' => $fid, 'from_member_id' => $this->uid))->count()) {
					$this->error(1011);
				}

				if ($follow->add(array(
					'to_member_id' => $fid,
					'from_member_id' => $this->uid,
					'add_time' => date("Y-m-d H:i:s"),
				))) {
					if ($status) {
						M('member')->where(array('id' => $this->id))->setInc("follow_num", 1);
						M('member')->where(array('id' => $fid))->setInc("fans_num", 1);
					} else {
						M('member')->where(array('id' => $this->id))->setDec("follow_num", 1);
						M('member')->where(array('id' => $fid))->setDec("fans_num", 1);
					}

					$this->success(array("result" => true));
				} else {
					$this->success(array("result" => false));
				}
			} else {
				if ($follow->where(array('to_member_id' => $fid, 'from_member_id' => $this->uid))->delete()) {
					$this->success(array("result" => true));
				} else {
					$this->success(array("result" => false));
				}
			}
		} else {
			$this->error(1404);
		}
	}
}