<?php
namespace Api\Controller;

class CollectionController extends ApiBaseController {

	/**
	 * 收藏列表
	 * @author yanxiangrui <yanruilamp@163.com>
	 */
	public function lists_get() {
		$this->check_token();

		$map = array(
			'member_id' => $this->uid,
		);

		$data = M('collection')->where($map)->select();
		foreach ($data as &$c) {
			if ($c['table_name'] == 'demand') {
				$c['title'] = '需求/服务';
				$c['content'] = M('demand')->where(array('id' => $c['table_id']))->find();
			}
			if ($c['table_name'] == 'book') {
				$c['title'] = '书籍';
				$c['content'] = M('book')->where(array('id' => $c['table_id']))->find();
			}
			if ($c['table_name'] == 'information') {
				$c['title'] = '答疑';
				$c['content'] = M('information')->where(array('id' => $c['table_id']))->find();
			}
		}
		$this->success($data);
	}

	/**
	 * 添加收藏
	 * @author yanxiangrui <yanruilamp@163.com>
	 */
	public function add_post() {
		$this->check_token();

		$body = $this->get_request_data('collection');

		if ($this->check_body_fields($body, array('table_name', 'table_id'))) {

			if (!in_array($body['table_name'], array('demand', 'information', 'book')) || !M($body['table_name'])->find($body['table_id'])) {
				//5997收藏商品部存在
				$this->error(5997);
			}

			$map = array(
				'member_id' => $this->uid,
				'table_id' => $body['table_id'],
				'table_name' => $body['table_name'],
			);

			$collection = M('collection');

			if (!$collection->where($map)->count()) {

				$map['add_time'] = date('Y-m-d H:i:s');

				if ($id = $collection->add($map)) {
					$this->success();
				} else {
					//5998收藏失败
					$this->error(5998);
				}

			} else {
				//5999已经收藏过
				$this->error(5999);
			}
		} else {
			//缺少参数
			$this->error(1001);
		}
	}
}