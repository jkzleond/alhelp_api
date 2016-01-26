<?php
namespace Api\Controller;

class FansController extends ApiBaseController {

	// protected $is_check_token = true;

	//分页大小
	protected $fans_page_size = 20;
	protected $follow_page_size = 20;

	public function fans_get() {

		$follow = M("follow");

		$uid = I("get.uid", 0, 'intval');

		if ($uid <= 0) {
			$this->check_token();
			$uid = $this->uid;
		}

		$pageCount = 1;

		//构造sql
		$options = array(
			'alias' => 'f',
			'join' => array(
				"LEFT JOIN follow f2 ON f2.to_member_id = f.from_member_id AND f2.from_member_id = f.to_member_id",
				"LEFT JOIN pillow_talk_time t ON ((t.member_id_s = f.to_member_id AND t.member_id_b = f.from_member_id) OR(t.member_id_b = f.to_member_id AND t.member_id_s = f.from_member_id))",
				"INNER JOIN member m on f.from_member_id = m.id",
			),
			'where' => array("f.to_member_id" => $uid),
			'field' => "UNIX_TIMESTAMP(f.add_time) as add_time,m.id AS from_member_id,m.nickname AS from_member_nickname,max(t.time) AS last_pillow_talk_time, IF(f2.id IS NOT NULL, '1', '0') as is_mutual",
			'order' => "is_mutual desc, t.time desc",
			'group' => "f.id",
		);

		$pageNum = intval(I("get.page", 1));

		if ($pageNum) {

			$follow->options = $options;

			unset($follow->options['field']);
			unset($follow->options['group']);

			$count = $follow->count();

			$_GET["p"] = max(1, $pageNum);

			$Page = new \Think\Page($count, $this->fans_page_size);

			$follow->limit($Page->firstRow . ',' . $Page->listRows);

			$pageCount = ceil($Page->totalRows / $Page->listRows);
		}

		$data = $follow->select($options);

		foreach ($data as &$value) {
			$value["avatar"] = GetSmallAvatar($value['from_member_id']);
			$value["community"] = D("Community")->communities($value["from_member_id"]);
		}

		$data = array(
			'list' => $data,
			'count' => count($data),
			'next_page' => null,
		);

		if ($pageCount > 1 && $pageNum < $pageCount) {
			$data['next_page'] = $this->url("/v1/fans/page/" . (++$pageNum));
		}

		$this->success($data);
	}

	public function follow_get() {
		$follow = M("follow");

		$uid = I("get.uid", 0, 'intval');

		if ($uid <= 0) {
			$this->check_token();
			$uid = $this->uid;
		}

		$pageCount = 1;

		//构造sql
		//互相关注is_mutual
		$options = array(
			'alias' => 'f',
			'join' => array(
				"LEFT JOIN follow f2 ON f2.to_member_id = f.from_member_id AND f2.from_member_id = f.to_member_id",
				"LEFT JOIN pillow_talk_time t ON ((t.member_id_s = f.to_member_id AND t.member_id_b = f.from_member_id) OR(t.member_id_b = f.to_member_id AND t.member_id_s = f.from_member_id))",
				"INNER JOIN member m on f.to_member_id = m.id",
			),
			'where' => array("f.from_member_id" => '12673'),
			'field' => "UNIX_TIMESTAMP(f.add_time) as add_time,m.id AS to_member_id,m.nickname AS from_member_nickname,max(t.time) AS last_pillow_talk_time, IF(f2.id IS NOT NULL, '1', '0') as is_mutual",
			'order' => "is_mutual desc, t.time desc",
			'group' => "f.id",
		);

		$pageNum = intval(I("get.page", 1));

		if ($pageNum) {

			$follow->options = $options;
			unset($follow->options['group']);
			unset($follow->options['field']);

			$count = $follow->count();

			$_GET["p"] = max(1, $pageNum);

			$Page = new \Think\Page($count, $this->follow_page_size);

			$follow->limit($Page->firstRow . ',' . $Page->listRows);

			$pageCount = ceil($Page->totalRows / $Page->listRows);
		}

		$data = $follow->select($options);

		foreach ($data as &$value) {
			$value["avatar"] = GetSmallAvatar($value['to_member_id']);
			$value["community"] = D("Community")->communities($value["to_member_id"]);
		}

		$data = array(
			'list' => $data,
			'count' => count($data),
			'next_page' => null,
		);

		if ($pageCount > 1 && $pageNum < $pageCount) {
			$data['next_page'] = $this->url("/v1/follow/page/" . (++$pageNum));
		}

		$this->success($data);
	}

	//获取指定用户是否是另一个用户的粉丝
	public static function isFans($uid, $to_uid) {
		$follow = M("follow");
		return $follow->where(array(
			"from_member_id" => $uid,
			"to_member_id" => $to_uid,
		)
		)->count() > 0;
	}
}