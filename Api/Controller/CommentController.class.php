<?php
namespace Api\Controller;

class CommentController extends ApiBaseController {

	/**
	 * 发表评论
	 */
	public function add_post() {
		$this->check_token();
		$body = $this->get_request_data('comment');
		if ($this->check_body_fields($body, array('table', 'table_id', 'content'))) {
			$pid = intval($body['pid']);
			if (!in_array($body['table'], array('demand', 'information', 'listen'))) {
				//评论数据不存	在
				$this->error(5011);
			}
			$comment = M('comment');
			if ($id = $comment->add(array(
				'pid' => $pid,
				'table' => $body['table'],
				'table_id' => $body['table_id'],
				'member_id' => $this->uid,
				'content' => $body['content'],
				'add_time' => date('Y-m-d H:i:s'),
			))) {
				$this->success($id);
			} else {
				$this->error(9001);
			}
		} else {
			//缺少参数
			$this->error(1001);
		}
	}

	/**
	 * 评论列表
	 */
	public function list_post() {
		$this->check_token(false);
		$body = $this->get_request_data('comment');
		if ($this->check_body_fields($body, array('table', 'table_id'))) {
			$comment = M('comment');
			$map = array(
				'table' => $body['table'],
				'table_id' => $body['table_id'],
				'pid' => 0,
			);
			$list = $comment->where($map)->select();
			foreach ($list as &$v) {
				$v['child'] = $comment->find($v['id']);
			}
			$this->success($list);
		} else {
			//缺少参数
			$this->error(1001);
		}
	}

	/**
	 * 评论点赞
	 */
	public function praise_get() {
		$this->check_token();
		$id = I('get.id');
		if (!empty($id)) {
			$m = M();
			$info = $m->table('comment')->find($id);
			if (empty($info)) {
				//数据不存在
				$this->error(5011);
			}
			$map = array(
				'member_id' => $this->uid,
				'table_name' => $info['table'],
				'catid' => $info['table_id'],
			);
			if ($praise = $m->table('praise')->where($map)->find()) {
				//取消点赞
				$m->table('comment')->where(array('id' => $info['id']))->setDec('praise_num');
				$m->table('praise')->delete($praise['id']);
			} else {
				//点赞
				$m->table('comment')->where(array('id' => $info['id']))->setInc('praise_num');
				$m->table('praise')->add(array_merge($map, array(
					'status' => 1,
					'add_time' => date('Y-m-d'),
				)));
			}
			$this->success();
		} else {
			//缺少参数
			$this->error(1001);
		}
	}
}