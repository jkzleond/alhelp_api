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

		$new_group_id = $group->getLastInsID();
		$group->id = $new_group_id;
		$group->group_id = str_pad($group->getLastInsID(), 6, '0', STR_PAD_LEFT);
		$group_data = $group->data();
		$is_save = $group->save();
		if (!$is_save) {
			$group->rollBack();
			$this->error('1500');
		}
		//将群主加入群
		$add_member_success = $group->addMembers($new_group_id, array($this->uid));
		if (!$add_member_success) {
			$group->rollback();
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
		$group_info = M()->table(array('group' => 'g'))
				->field('g.*, m.nickname as owner_nickname')
				->join('left join member m on m.id = g.owner_id')
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
	 * route: im/groups/[:uid]/[:is_owner]
	 */
	public function group_list_get() {
		$this->check_token();

		$group_model = M('Group');
		$page_num = I('get.p', 1, 'intval');
		$page_size = I('get.ps', 10, 'intval');

		$uid = I('get.uid', null, 'intval');
        $is_owner = I('get.is_owner', 0, 'intval');
        
		$options = array();
        if ($is_owner and $uid) {
            $options['where']['owner_id'] = $uid;
        } elseif ($uid) {
            $options['where']['gm.member_id'] = $uid;
            $options['where']['g.owner_id'] = $uid;
            $options['where']['_logic'] = 'OR';
            $options['join'][] = 'left join group_member gm on gm.group_id = g.id';
        }

		$filters = $this->get_request_data('filters');
		if ( !empty($filters) and is_array($options['where']) ) {
			$options['where'] = array_merge($options);
		} elseif ( !empty($filters) ) {
			$options['where'] = $filters;
		}

        $group_model->options = $options;
		$group_total = $group_model->table(array('group' => 'g'))->count();
        
		$page = new \Think\Page($group_total, $page_size);
		$page->totalPages = ceil($page->totalRows / $page->listRows);
        
        $group_model->options = $options; //重新设置options
		$group_list = $group_model->table(array('group' => 'g'))
		        ->field("g.*, m.nickname as owner_nickname, if(msg.no_read_count is null, '0', msg.no_read_count) as no_read_count")
				->join('left join member m on m.id = g.owner_id')
				->join("left join (
					select gm.group_id, count( case when msg.from_member_id != '{$this->uid}' and (msg.add_time > gm.last_read_time or gm.last_read_time is null ) then 1 end ) as no_read_count
					from imessage as msg
					left join group_member as gm on gm.group_id = msg.to_id
					and msg.is_to_group = 1
					where gm.member_id = '{$this->uid}'
					group by gm.group_id
				) msg on msg.group_id = g.id")
				->order('id desc')
				->limit($page->firstRow, $page->listRows)->select();

		if($page_num < 1 or $page_num > $page->totalPages) {
			$this->success(array(
				'prev_page' => null,
				'next_page' => null,
				'total_rows' => $page->totalRows,
				'total_pages' => $page->totalPages,
				'list' => null
			));
		} else {
			$url = 'http://'.I('server.HTTP_HOST').'/'.__INFO__;
			$this->success(array(
				'prev_page' => $page_num == 1 ? null : $url.'?p='.($page_num - 1).'&ps='.$page_size,
				'next_page' => $page_num == $page->totalPages ? null : $url.'?p='.($page_num + 1).'&ps='.$page_size,
				'total_rows' => $page->totalRows,
                'total_pages' => $page->totalPages,
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