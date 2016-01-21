<?php
namespace Api\Controller;

class CommunityController extends ApiBaseController {

	/**
	 * 获取当前用户所有感兴趣的圈子列表
	 */
	public function communities_get($uid = '') {
		$this->check_token();
		if ($id) {
			$uid = $id;
		} else {
			$uid = $this->uid;
		}
		$data = D('Community')->communities($uid);
		$this->success($data);
	}

	public function community_join_post() {
		$this->check_token();
		$body = $this->get_request_data('community');
		if ($body && array_key_exists('id', $body)) {
			$result = D('Community')->community_join($this->uid, $body['id']);
			if ($result === 3006) {
				$this->error(3006);
			} else if ($result === 3007) {
				$this->error(3007);
			} else {
				if ($result) {
					$this->success();
				} else {
					$this->error(9001);
				}
			}
		} else {
			$this->error(1001);
		}
	}

	public function community_delete() {
		$this->check_token();
		$body = $this->get_request_data('community');
		if ($body && array_key_exists('id', $body)) {
			$result = D('Community')->community_delete($this->uid, $body['id']);
			if ($result === 3005) {
				$this->error(3005);
			} else {
				if ($result) {
					$this->success();
				} else {
					$this->error(9001);
				}
			}
		} else {
			$this->error(1001);
		}
	}

	public function community_search_post() {
		// $this->check_token();
		$body = $this->get_request_data('community');
		if ($body && array_key_exists('term', $body)) {
			if ($body['term'] != 'provinces' && $body['term'] != 'schools' && $body['term'] != 'colleges' && $body['term'] != 'majors') {
				$this->error(1001);
			}
			$school = D('School');
			$term = $body['term'];
			if ($term == 'provinces') {
				$provinces = $school->provinces();
				$this->success($provinces);
			}
			if ($term == "schools") {
				if (!array_key_exists('province_id', $body)) {
					$body['province_id'] = null;
				}
				if (array_key_exists('major_id', $body)) {
					$schools = $school->major_to_schools($body['major_id'], $body['province_id']);
					if ($schools === 3011) {
						$this->error(3011);
					}
					$this->success($schools);
				}
				if (array_key_exists('keywords', $body)) {
					$keywords = $body['keywords'];
					$schools = $school->schools_search($keywords, $body['province_id']);
					if ($schools === 3008) {
						$this->error(3008);
					}
					$this->success($schools);
				}
				$schools = $school->schools($body['province_id']);
				if ($schools === 3008) {
					$this->error(3008);
				} else {
					$this->success($schools);
				}
			}
			if ($term == "colleges") {
				if (!array_key_exists('school_id', $body)) {
					$this->error(1001);
				}
				if (array_key_exists('keywords', $body)) {
					$keywords = $body['keywords'];
					$colleges = $school->colleges_search($keywords, $body['school_id']);
					if ($colleges === 3009) {
						$this->error(3009);
					}
					$this->success($colleges);
				}
				$colleges = $school->colleges($body['school_id']);
				if ($schools === 3009) {
					$this->error(3009);
				} else {
					$this->success($colleges);
				}
			}
			if ($term == "majors") {
				if (!array_key_exists('college_id', $body)) {
					$this->error(1001);
				}
				if (array_key_exists('keywords', $body)) {
					$keywords = $body['keywords'];
					$schools = $school->majors_search($keywords, $body['college_id']);
					$this->success($schools);
				}
				$majors = $school->majors($body['college_id']);
				if ($majors === 3010) {
					$this->error(3010);
				} else {
					$this->success($majors);
				}
			}
		} else {
			$this->error(1001);
		}
	}

	/**
	 * 获取学校信息
	 * @param  integer $id 指定学校id
	 */
	public function school_get($id) {
		$this->check_token();
		$data = D('School')->school($id);
		if ($data === 3009) {
			$this->error(3009);
		}
		$this->success($data);
	}

	/**
	 * 获取学校信息
	 * @param  integer $id 指定学校id
	 */
	public function college_get($id) {
		$this->check_token();
		$data = D('School')->college($id);
		if ($data === 3010) {
			$this->error(3010);
		}
		$this->success($data);
	}

	/**
	 * 获取学校信息
	 * @param  integer $id 指定学校id
	 */
	public function major_get($id) {
		$this->check_token();
		$data = D('School')->major($id);
		if ($data === 3011) {
			$this->error(3011);
		}
		$this->success($data);
	}

}