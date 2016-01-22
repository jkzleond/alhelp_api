<?php
namespace Api\Controller;

class GroupController extends ApiBaseController {

	/**
	 * 添加群
	 * route: im/group
	 */
	public function group_post() {
		$this->check_token();
		$group_info = $this->get_request_data();

		if (!$group_info) {
			$this->error('1001');
		}
		$group = D('Group');
		$create_success = $group->create($group_info);

		if (!$create_success) {
			$this->error('1500');
		}
		$group->owner_id = $this->uid;
		$group->startTrans();
		$add_success = $group->add();

		if (!$add_success) {
			$group->rollBack();
			$this->error('1500');
		}

		$group->id = $group->getLastInsID();
		$group->group_id = str_pad($group->getLastInsID(), 6, '0', STR_PAD_LEFT);
		$group_data = $group->data();
		$is_save = $group->save();
		if (!$is_save) {
			$group->rollBack();
			$this->error('1500');
		}

		$success = $group->commit();
		if (!$success) $this->error('1500');
		$this->success($group_data);
	}

	/**
	 * 删除群
	 * route: im/group/:id
	 */
	public function group_delete() {
		$this->check_token();
		$gid = I('get.id', null, 'intval');
		if(!$gid) {
			$this->error('1001');
		}
		$group = M('Group');
		$success = $group->where(array('id' => $gid, 'owner_id' => $this->uid))->delete(); //只能删除该用户拥有的群
		if($success){
			$this->success($success);
		}else{
			$this->error('1500');
		}
	}

	/**
	 * 更新群
	 * route: im/group/:id
	 */
	public function group_put(){
		$gid = I('get.id');
		if(!$gid){
			$this->error('1001');
		}
		$data = $this->get_request_data();
		$group = D('group');
		$success = $group->where(array('id' => $gid))->save($data);
		if($success !== false){
			$this->success();
		}else{
			$this->error($group->getLastSql());
			$this->error('1500');
		}
	}

	/**
	 * 获取指定ID群信息
	 * route im/group/:id
	 */
	public function group_get(){
		$gid = I('get.id', null, 'intval');
		if(!$gid){
			$this->error('1001');
		}
		$group_info = M('Group')->alias('g')
				->field('g.*, m.nickname as owner_nickname')
				->join('left join __MEMBER__ m on m.id = g.owner_id')
				->where(array('g.id' => $gid))
				->find();
		if(!$group_info){
			$this->error('8005');
		}else{
			$this->success($group_info);
		}

	}

	/**
	 * 获取群列表
	 * route: im/groups/[:uid]
	 */
	public function group_list_get() {

		$group_model = M('Group');
		$page_num = I('get.p', 1, 'intval');
		$page_size = I('get.ps', 10, 'intval');

		$condition = array();
		$owner_id = I('get.uid', null, 'intval');
		if ($owner_id) $condition['owner_id'] = $owner_id;

		$filters = $this->get_request_data('filters');
		empty($filters) or $condition = array_merge($condition, $filters);

		$group_total = $group_model->where($condition)->count();

		$page = new \Think\Page($group_total, $page_size);
		$page->totalPages = ceil($page->totalRows / $page->listRows);
		$group_list = $group_model->order('id desc')
				->where($condition)
				->limit($page->firstRow, $page->listRows)->select();

		if($page_num < 1 or $page_num > $page->totalPages) {
			$this->success(array(
				'prevPage' => null,
				'nextPage' => null,
				'list' => null
			));
		} else {
			$url = 'http://'.I('server.HTTP_HOST').'/'.__INFO__;
			$this->success(array(
				'prevPage' => $page_num == 1 ? null : $url.'?p='.($page_num - 1).'&ps='.$page_size,
				'nextPage' => $page_num == $page->totalPages ? null : $url.'?p='.($page_num + 1).'&ps='.$page_size,
				'list' => $group_list
			));
		}
	}

	/**
	 * 添加群成员
	 * route: im/group/:id\d/member
	 */
	public function member_post() {
		$gid = I('get.id', null, 'intval');
		if (!$gid) {
			$this->error('1001');
		}
		$member_id_list = $this->get_request_data('member_ids');
		$group = D('Group');
		$success = $group->addMembers($gid, $member_id_list);
		if (!$success) $this->error('1500');
		$this->success(array(
			'member_ids' => $member_id_list
		));
	}

	/**
	 * 删除群成员
	 * route: im/group/:id\d/member
	 */
	public function member_delete() {
		$gid = I('get.id', null, 'intval');
		if (!$gid) {
			$this->error('1001');
		}
		$member_id_list = $this->get_request_data('member_ids');
		$group = D('Group');
		$success = $group->removeMembers($gid, $member_id_list);
		if (!$success) $this->error('1500');
		$this->success(array(
			'member_ids' => $member_id_list
		));
	}

}