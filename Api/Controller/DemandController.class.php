<?php
namespace Api\Controller;

class DemandController extends ApiBaseController {

	//申请试听
	public function create_test_listen_post() {
		$this->check_token();

		$body = $this->get_request_data('listen');

		$type = $body['listen_method']['type'];
		$number = $body['listen_method']['number'];
		$time = $body['time'];
		$content = $body['content'];
		$demand_id = $body['demand']['id'];

		if ($this->check_body_fields($body, array('time', 'content', 'listen_method', 'demand')) && $demand_id && $type && $number) {

			if (($time + 3600) < time()) {
				//时间错误 5001
				$this->error('5001');
			}

			$time = date('Y-m-d H:i:s', $time);

			switch (intval($type)) {
			case 1:
				$wap_listen_way = "YY" . $number;
				break;
			case 2:
				$wap_listen_way = "QQ" . $number;
				break;
			case 3:
				$wap_listen_way = "Skype" . $number;
				break;
			default:
				//试听方式不正确 5002
				$this->error('5002');
				break;
			}

			$dinfo = get_info('demand', array('id' => $demand_id)); //服务信息

			if (empty($dinfo)) {
				//申请试听服务不存在 5003
				$this->error('5003');
			}

			//检测用户是否已申请试听
			$ret = get_info('apply_to_listen', array(
				'member_id' => $this->uid,
				'demand_id' => $body['demand']['id'],
			));

			$info = M('member')->find($this->uid); //用户信息

			if (!empty($ret) && $ret['status'] != -1) {
				//已申请试听 5004
				$this->error('5004');

			} else if (!empty($ret)) {
				$_POST = array(
					'id' => $ret['id'],
					'time' => $time,
					'content' => $content,
					'listen_type' => $type,
					'listen_number' => $number,
					'status' => 1,
				);
				update_data('apply_to_listen');
				$bid_id = $ret['bid_id'];
			} else {

				//当试听不存在的话  先插入对应此服务的一条需求记录答疑
				$_POST = array(
					'member_id' => $info['id'],
					'member_name' => $info['nickname'],
					'role_type' => 1,
					'demand_type' => $dinfo['demand_type'],
					'description' => $dinfo['description'],
					'cost' => $dinfo['cost'],
					'profes_type' => $dinfo['profes_type'],
					'city' => $dinfo['city'],
					'university' => $dinfo['university'],
					'college' => $dinfo['college'],
					'major_code' => $dinfo['major_code'],
					'major' => $dinfo['major'],
					'qq' => $info['qq'],
					'mobile' => $info['phone'],
					'is_automatic' => 1,
				);

				//插入投标记录
				if ($demand_id = update_data('demand')) {
					$_POST = array(
						'demand_member_id' => $info['id'],
						'service_member_id' => $dinfo['member_id'],
						'demand_id' => $demand_id,
						'service_demand_id' => $dinfo['id'],
						'qq' => $info['qq'],
						'phone' => $info['phone'],
						'status' => 0,
					);

					if ($bid_id = update_data('bid')) {
						//插入试听记录
						$_POST = array(
							'member_id' => $info['id'],
							'demand_member_id' => $dinfo['member_id'],
							'demand_id' => $dinfo['id'],
							'bid_id' => $bid_id,
							'time' => $time,
							'content' => $content,
							'listen_type' => $type,
							'listen_number' => $number,
							'status' => 1,
						);
						$ret['id'] = update_data('apply_to_listen');
					}

				}

			}

			M('message')->addAll(array(
				array(
					"from_member_id" => 0,
					"to_member_id" => $info['id'],
					'content' => '您好！您已成功发送给' . $dinfo['member_name'] . '预约试听申请' . $dinfo['description'] . '，预约时间：' . $time . '，试听方式：' . $wap_listen_way,
					'table' => 'apply_to_listen',
					'table_id' => $ret['id'],
					'add_time' => date('Y-m-d H:i:s'),
					'role' => 1,
				),
				array(
					"from_member_id" => 0,
					"to_member_id" => $dinfo['member_id'],
					'content' => '您好！' . $info['nickname'] . '发送给您了预约申请' . $demand['description'] . '，预约时间：' . $time . '，试听方式：' . $wap_listen_way,
					'table' => 'apply_to_listen',
					'table_id' => $ret['id'],
					'add_time' => date('Y-m-d H:i:s'),
					'role' => 2,
				),
			));

			$this->success(
				array('listen_id' => $ret['id'], 'bid_id' => $bid_id)
			);

		} else {
			$this->error(1001);
		}

	}

	/**
	 * 试听申请列表列表
	 */
	public function listenLists_get() {
		$this->check_token();

		if ($this->role == 1) {
			$map['member_id'] = $this->uid;
		} else {
			$map['demand_member_id'] = $this->uid;
		}

		$p = I('get.p', 1);

		if (!empty($map)) {

			$listen = M('applyToListen');

			$count = $listen->where($map)->count();

			$page = new \Think\Page($count, 10);

			$list = $listen->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->where($map)->select();

			$pageCount = ceil($page->totalRows / $page->listRows);

			if ($p < 1 || $p > $pageCount) {
				$this->success(array(
					"prevPage" => null,
					"nextPage" => null,
					"list" => null,
				));
			}

			$url = U('/v1/demands/listen/lists@api');

			$this->success(array(
				"prevPage" => $p == 1 ? null : $url . '?p=' . ($p - 1),
				"nextPage" => $p == $pageCount ? null : $url . '?p=' . ($p + 1),
				"list" => $list,
			));
		}

		$this->success(array(
			"prevPage" => null,
			"nextPage" => null,
			"list" => null,
		));
	}

	/**
	 * 协议详情
	 */
	public function bidInfo_post() {
		$this->check_token();

		$body = $this->get_request_data('bid');

		if ($this->check_body_fields($body, array('type', 'id'))) {

			if (!in_array($body['type'], array('bid', 'demand'))) {
				// 5990 type 值只能为  bid demand
				$this->error(5990);
			}

			if ($body['type'] == 'bid') {
				$map['id'] = intval($body['id']);
			} elseif ($body['type'] == 'demand') {
				$map['demand_id|service_demand_id'] = intval($body['id']);
			}

			$bid = M('bid');

			$info = $bid->where($map)->find();

			if (!empty($info) && in_array($this->uid, array($info['demand_member_id'], $info['service_member_id']))) {
				$this->success($info);
			}

			$this->success();
		} else {
			$this->error(1001);
		}
	}

	/**
	 * 协议列表
	 */
	public function bidList_post() {

		$this->check_token();

		$id = I('get.id', 0);
		$p = I('get.p', 1);

		if (empty($id)) {
			$this->error(1001);
		} else {

			$map['demand_id'] = $id;

			$map['member_id|demand_member_id'] = $this->uid;

			$bid = M('bid');

			$count = $bid->where($map)->count();

			$page = new \Think\Page($count, 10);

			$list = $bid->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->where($map)->select();

			$pageCount = ceil($page->totalRows / $page->listRows);

			if ($p < 1 || $p > $pageCount) {
				$this->success(array(
					"prevPage" => null,
					"nextPage" => null,
					"list" => null,
				));
			}

			$url = U('/v1/demands/bid/lists/' . $id . '@api');

			$this->success(array(
				"prevPage" => $p == 1 ? null : $url . '?p=' . ($p - 1),
				"nextPage" => $p == $pageCount ? null : $url . '?p=' . ($p + 1),
				"list" => $list,
			));

		}
	}

	//同意试听申请
	public function yes_test_listen_post() {

	}

	/**
	 * 获取签约类型
	 */
	public function signType_get() {
		$this->success(array(
			array(
				'id' => 1,
				'title' => '单次答疑',
				'content' => array(
					'如复试的答疑/授课，可以选择单次答疑/授课。',
					'签订合同后，学生一次性支付款项。',
					'答疑/授课结束后，除非特殊情况，款项会立即支付给老师。',
				),
			),
			array(
				'id' => 2,
				'title' => '单期答疑',
				'content' => array(
					'双方约定期限，如一个月，并确定该期限内答疑/授课的次数。',
					'签订合同后，学生一次性付款。 在该期限内没有用完答疑/授课次数的，可以申请延期最多一个期限，但不能申请退款。（如果在该期限结束后的2个工作内没有申请的，系统会自动付款给老师）',
					'在该期限内提前用完答疑/授课次数的，还需要答疑/授课的，需要签订新的协议，支付款项后才能进行答疑/授课。',
					'延期期限结束后，无论是否用完答疑/授课次数，系统都会自动付款给老师。',
				),
			),
			array(
				'id' => 3,
				'title' => '多期答疑',
				'content' => array(
					'双方约定需要答疑/授课的阶段，以及每个阶段需要答疑/授课的次数和费用等。',
					'签订合同后，学生一次性付款到新助邦托管，由新助邦分阶段支付给老师。',
					'在每个阶段结束后，由老师提交答疑/授课记录，申请释放本阶段款项。',
					'如果学生对老师的答疑/授课不满意，可以在老师申请释放本阶段款项后的2天内提出终止协议。本阶段的费用会支付给老师，但余下阶段的款项会退回给学生。',
					'在老师提出申请后的2天内，学生没有答复的，视为满意。本阶段的费用会自动支付给老师，同时进入到下一阶段。',
					'一旦进入到下一阶段，双方都不能中途终止协议，否则视为违约。',
					'如果是老师违约，老师将拿不到该阶段的费用，无论答疑/授课过多少次。',
					'如果是学生违约，学生本阶段的费用都会全部支付给老师，无论答疑/授课过多少次。',
					'在每个阶段如有延期申请，最多只能延期一个周期。',
				),
			),
		));
	}

	//申请签约
	public function create_sign_post() {
		$this->check_token();

		if ($this->role != 1) {
			//不是学生
			$this->error(5981);
		}

		$body = $this->get_request_data('sign');

		$demand_id = $body['demand_id'];
		$sign_type = $body['sign_type'];
		$describe = $body['describe'];

		if ($this->check_body_fields($body, array('demand_id', 'sign_type', 'describe'))) {

			$user_info = M('member')->find($this->uid);

			//查找此服务
			$demandArr = get_info('demand', array(
				'id' => $demand_id,
			));

			//检测是否签约
			$where = array(
				'demand_member_id' => $this->uid,
				'service_demand_id' => $demand_id,
			);

			$res = get_info('bid', $where);

			if ($res && $res['status'] < 8) {
				if ($res['type'] > 0) {
					//你已经对此服务签约,此服务还没有结束
					$this->error(5980);
				} else {
					//更新签约状态
					$_POST = array(
						'id' => $res['id'],
						'status' => 2,
						'type' => $sign_type,
						'siging_remark' => $describe,
					);
					update_data('bid');
				}
				$result = $res['id'];
				$demamdid = $res['demand_id'];
			} else {
				//直接添加一条与此服务对应的需求对应需求
				if ($res && $res['status'] > 7) {
					$demamdid = $res['demand_id'];
				} else {
					$_POST = array(
						'member_id' => $this->uid,
						'member_name' => $user_info['nickname'],
						'role_type' => 1,
						'demand_type' => $demandArr['demand_type'],
						'description' => $demandArr['description'],
						'cost' => $demandArr['cost'],
						'profes_type' => $demandArr['profes_type'],
						'city' => $demandArr['city'],
						'university' => $demandArr['university'],
						'college' => $demandArr['college'],
						'major_code' => $demandArr['major_code'],
						'major' => $demandArr['major'],
						'qq' => $user_info['qq'],
						'mobile' => $user_info['phone'],
						'is_automatic' => 1,
					);
					$demamdid = update_data('demand');
				}
				$_POST = array(
					'demand_member_id' => $user_info['id'],
					'service_member_id' => $demandArr['member_id'],
					'demand_id' => $demamdid,
					'service_demand_id' => $demand_id,
					'price' => $demandArr['cost'],
					'qq' => $demandArr['qq'],
					'phone' => $demandArr['mobile'],
					'type' => $sign_type,
					'siging_remark' => $describe,
					'status' => 2,
				);
				$result = update_data('bid');
			}

			$con = query_sql("", "select
			bid.demand_member_id,
			bid.service_member_id ,
			b.nickname as demand_nickname,
			c.nickname as service_nickname,
			demand.description,
			demand.mobile
			from bid
			left join member b on bid.demand_member_id =b.id
			left join member c on  bid.service_member_id =c.id left
			join demand on bid.demand_id=demand.id where bid.id=" . $result);

			if ($result) {
				M('message')->addAll(array(
					array(
						"from_member_id" => 0,
						"to_member_id" => $con[0]['demand_member_id'],
						'content' => '您好！您已成功向' . $con[0]['service_nickname'] . '申请签订协议"' . $con[0]['description'] . '"',
						'table' => 'contract',
						'table_id' => $result,
						'add_time' => date('Y-m-d H:i:s'),
						'role' => 1,
						'is_contract' => 1,
					),
					array(
						"from_member_id" => 0,
						"to_member_id" => $con[0]['service_member_id'],
						'content' => '你好！' . $con[0]['demand_nickname'] . '发送给您了签订协议申请"' . $con[0]['description'] . '"',
						'table' => 'bid',
						'table_id' => $result,
						'add_time' => date('Y-m-d H:i:s'),
						'role' => 2,
						'is_contract' => 1,
					),
				));

				$smsto = new \Common\Lib\smsto();
				$phoneArr = array(
					$user_info['phone'],
					$con[0]['mobile'],
				);

				$content = array(
					'您好！您已成功向' . $con[0]['service_nickname'] . '申请签订协议"' . $con[0]['description'] . '"',
					'你好！' . $con[0]['demand_nickname'] . '发送给您了签订协议申请"' . $con[0]['description'] . '"',
				);

				$response = $smsto->SendToServer($phoneArr, $content);

				if (!$res || $res['status'] > 7) {
					$member_group = $demandArr['member_id'] < $user_info['id'] ? md5($demandArr['member_id'] . "|" . $user_info['id']) : md5($user_info['id'] . "|" . $demandArr['member_id']);
					$pillow_talk = get_info('pillow_talk', array(
						'demand_id' => 0,
						'service_id' => $demand_id,
						'member_group' => $member_group,
					));

					if ($pillow_talk) {
						$add_time = time();

						execute_sql('', "insert into pillow_talk (pid,fid,from_member_id,to_member_id,demand_id,service_id,add_time,content, role ,is_read ,is_delete,status,member_group, is_show) (select pid," . $pillow_talk['id'] . ",from_member_id,to_member_id," . $demamdid . ",service_id,'" . date('Y-m-d H:i:s') . "',content, role ,is_read ,is_delete,status,member_group, 1 from pillow_talk where member_group='" . $member_group . "'  and demand_id=0 and service_id=" . $demandArr['id'] . ")");

						$pids = query_sql("", "select id from pillow_talk where member_group='" . $member_group . "' and demand_id=" . $demamdid . " and service_id=" . $demandArr['id'] . " and pid=0 and UNIX_TIMESTAMP(add_time)>=" . $add_time . " order by id desc");
						$pid = $pids[0]['id'];
						if (!$pids) {
							$pid = 0;
						}
						execute_sql('', "update pillow_talk set pid=" . $pid . " where pid>0 and demand_id=" . $demamdid . " and service_id=" . $demandArr['id'] . " and  UNIX_TIMESTAMP(add_time)>=" . $add_time . "  order by id desc");
						execute_sql('', "update pillow_talk set is_show=0 where demand_id=0 and service_id=" . $demandArr['id']);
					}
				}

				//插入一条试听记录
				$_POST = array(
					'member_id' => $user_info['id'],
					'demand_member_id' => $demandArr['member_id'],
					'demand_id' => $demandArr['id'],
					'bid_id' => $result,
					'status' => '-1',
				);
				update_data('apply_to_listen');
				//成功
				$this->success();
			} else {
				//失败
				$this->error(9001);
			}
		} else {
			$this->error(1001);
		}

	}

	//添加合同(老师)
	public function create_contract_post() {
		$this->check_token();

		$body = $this->get_request_data('contract');

		$bid_id = $body['bid_id'];
		$title = $body['title'];
		$content = $body['content'];

		if ($this->check_body_fields($body, array('bid_id', 'title', 'content'))) {

			$info = M('member')->find($this->uid); //用户信息

			$_POST = array(
				'id' => $bid_id,
				'status' => 3,
			);

			if (update_data('bid')) {

				//获取投标签约状态
				$bidinfo = get_info('bid', array('id' => $bid_id));

				$_POST = array(
					'member_id' => $info['id'],
					'type' => $bidinfo['type'],
					'bid_id' => $bid_id,
					'title' => $title,
					'content' => $content,
					'pid' => 0,
				);

				if ($id = update_data('contract')) {

					$con = query_sql("", "select bid.demand_member_id,bid.service_member_id ,b.nickname as demand_nickname,c.nickname as service_nickname,demand.description from bid  left join member b on bid.demand_member_id =b.id left join member c on  bid.service_member_id =c.id left join demand on bid.demand_id=demand.id where bid.id=" . $bid_id);
					M('message')->addAll(array(
						array(
							"from_member_id" => 0,
							"to_member_id" => $con[0]['demand_member_id'],
							'content' => '您好！' . $con[0]['service_nickname'] . '已拟定了协议，请尽快回复！',
							'table' => 'contract',
							'table_id' => $id,
							'add_time' => date('Y-m-d H:i:s'),
							'role' => 1,
						),
						array(
							"from_member_id" => 0,
							"to_member_id" => $con[0]['service_member_id'],
							'content' => '你好！您的协议已发送给对方，等待对方回复。',
							'table' => 'contract',
							'table_id' => $id,
							'add_time' => date('Y-m-d H:i:s'),
							'role' => 2,
						),
					));

					$this->success(array(
						'contract_id' => $id,
					));

				} else {
					// 5007 合同创建失败
					$this->error(5007);
				}
			} else {
				// 5006 投标数据不存在
				$this->error(5006);
			}
		} else {
			$this->error(1001);
		}
	}

	//修改合同建议
	public function edit_contract_put() {
		$this->check_token();

		$body = $this->get_request_data('contract');

		$contract_id = $body['id'];
		$remark = $body['remark'];

		if ($this->check_body_fields($body, array('remark', 'id'))) {

			$info = M('member')->find($this->uid); //用户信息

			$bid_id = M('contract')->where(array('id' => $contract_id))->getField('bid_id');

			if (empty($bid_id)) {
				// 5009 标不存在
				$this->error(5009);
			}

			$_POST = array(
				'id' => $contract_id,
				'remark' => $remark,
			);

			if (update_data('contract')) {

				$bid = get_info('bid', array('id' => $bid_id));

				M('message')->addAll(array(
					array(
						"from_member_id" => 0,
						"to_member_id" => $bid['service_member_id'],
						'content' => $info['nickname'] . '提出修改协议，请查看！',
						'table' => 'contract',
						'table_id' => $contract_id,
						'add_time' => date('Y-m-d H:i:s'),
						'role' => 2,
					),
					array(
						"from_member_id" => 0,
						"to_member_id" => $info['id'],
						'content' => '你的修改协议已发送给对方，等待对方回复。 ',
						'table' => 'contract',
						'table_id' => $contract_id,
						'add_time' => date('Y-m-d H:i:s'),
						'role' => 1,
					),
				));

				//更新投标状态
				$_POST = array(
					'id' => $bid_id,
					'status' => 5,
				);

				update_data('bid');

				$this->success();

			} else {
				// 5008 合同修改失败
				$this->error(5008);
			}
		} else {
			$this->error(1001);
		}

	}

	//同意合同
	public function yes_contract_put() {
		$this->check_token();

		$body = $this->get_request_data('contract');

		if ($this->check_body_fields($body, array('id'))) {

			$contract_id = $body['id'];

			$bid_id = M('contract')->where(array('id' => $contract_id))->getField('bid_id');

			if (empty($bid_id)) {
				$this->error('5009');
			}

			$info = M('member')->find($this->uid); //用户信息

			$_POST = array(
				'id' => $bid_id,
				'status' => 4,
			);

			if (update_data('bid')) {

				$res = get_info('contract', array(
					'bid_id' => $bid_id,
					'status' => 1,
				));

				message_log(0, $res['member_id'], '学生已同意协议。' . $res['title'], 'contract', $res['id'], 2);
				$m = query_sql('', "select member.nickname,bid.demand_member_id from bid left join member on  member.id=bid.service_member_id where bid.id=" . $bid_id);
				M('message')->addAll(array(
					array(
						"from_member_id" => 0,
						"to_member_id" => $res['member_id'],
						'content' => '学生已同意协议"' . $res['title'] . '"。',
						'table' => 'contract',
						'table_id' => $res['id'],
						'add_time' => date('Y-m-d H:i:s'),
						'role' => 2,
					),
					array(
						"from_member_id" => 0,
						"to_member_id" => $info['id'],
						'content' => '你已同意' . $m[0]['nickname'] . '的协议"' . $res['title'] . '"。',
						'table' => 'contract',
						'table_id' => $res['id'],
						'add_time' => date('Y-m-d H:i:s'),
						'role' => 1,
					),
				));

				$this->success();

			} else {
				// 5010 修改数据失败
				$this->error(5010);

			}
		} else {
			$this->error(1001);
		}
	}

	//答疑详情
	public function info_get() {
		$this->check_token();
		$id = intval(I('get.id'));
		if (empty($id)) {
			$this->error(1001);
		}
		if ($info = M('demand')->where(array('demand_type' => 2, 'id' => $id))->find()) {
			$ret = get_info('apply_to_listen', array(
				'member_id' => $this->uid,
				'demand_id' => $id,
			));
			if (empty($ret) || $ret['status'] == -1) {
				//可以试听
				$info['isAudition'] = 1;
			} else {
				//已经试听
				$info['isAudition'] = 0;
			}
			$ret1 = get_info('bid', array(
				'demand_member_id' => $this->uid,
				'service_demand_id' => $id,
			));
			if (!empty($ret1) && $ret1['status'] < 2) {
				//可以签约
				$info['isSign'] = 1;
			} else {
				//不能签约
				$info['isSign'] = 0;
			}
			$this->success($info);
		} else {
			//数据不存在
			$this->error(5011);
		}
	}

	//资料详情
	public function bookinfo_get() {
		$this->check_token();
		$id = intval(I('get.id'));
		if (empty($id)) {
			$this->error(1001);
		}
		if ($info = M('demand')->where(array('demand_type' => 1, 'id' => $id))->find()) {
			$this->success($info);
		} else {
			//数据不存在
			$this->error(5011);
		}
	}

	/**
	 * 推荐列表
	 */
	public function recommend_get() {
		$this->check_token(false);
		$map['status'] = array('GT', 0);
		if (!!$this->uid) {
			$map['role_type'] = $this->role == 1 ? 2 : 1;
		}
		$type = trim(I('get.type'));
		$id = intval(I('get.id'));
		$p = intval(I('get.p', 1));
		$demand = M('demand');
		if ($type == 'demand_id' && ($id = $demand->getFieldById($id, 'demand_type'))) {
			$type = 'demand_type';
		}
		if ($type == 'demand_type') {
			$map['demand_type'] = $id;
			$count = $demand->where($map)->count();
			$page = new \Think\Page($count, 10);
			$list = $demand->order('id desc')->field('*')->where($map)->limit($page->firstRow . ',' . $page->listRows)->select();
			$pageCount = ceil($page->totalRows / $page->listRows);
			if ($p < 1 || $p > $pageCount) {
				$this->success(array(
					"prevPage" => null,
					"nextPage" => null,
					"list" => null,
				));
			}
			$url = U('/v1/demands/recommend/' . I("get.type") . '/' . I('get.id') . '@api');
			$this->success(array(
				"prevPage" => $p == 1 ? null : $url . '?p=' . ($p - 1),
				"nextPage" => $p == $pageCount ? null : $url . '?p=' . ($p + 1),
				"list" => $list,
			));
		}
		$this->success(array(
			"prevPage" => null,
			"nextPage" => null,
			"list" => null,
		));
	}

	/**
	 * 合作列表
	 */
	public function demandCollaborate_get() {
		$this->check_token();
		$page = intval(I('get.page'));
		$id = I('get.demand_id');
		$demand = D('Demand2');
		$result = $demand->collaborates($id, $page);
		//$this->error($demand->getLastSql());
		if ($result === 5997) {
			$this->error(5997);
		}
		$this->success($result);
	}

	/**
	 * 补充说明
	 */
	public function demandExplain_post() {
		$this->check_token();
		$body = $this->get_request_data('explain');
		if ($this->check_body_fields($body, array('demand_id', 'content'))) {
			if (!M('demand')->where(array('member_id' => $this->uid, 'id' => $body['demand_id']))->find()) {
				//5997记录不存在
				$this->error(5996);
			}
			$data = array(
				'member_id' => $this->uid,
				'demand_id' => $body['demand_id'],
				'content' => $body['content'],
				'add_time' => date('Y-m-d H:i:s'),
			);
			$explain = M('demandExplain');
			if ($id = $explain->add($data)) {
				$this->success($id);
			} else {
				//5995补充说明录入失败
				$this->error(5998);
			}
		} else {
			//缺少参数
			$this->error(1001);
		}
	}

	/**
	 * 补充说明列表
	 */
	public function demandExplainLists_get() {
		$this->check_token(false);
		$demand_id = intval(I('get.id', 0));
		$list = null;
		if (!empty($demand_id)) {
			$explain = M('demandExplain');
			$list = $explain->where(array('demand_id' => $demand_id))->select();
		}
		$this->success($list);
	}

	/**
	 * 删除
	 */
	public function demandDelete_delete() {
		$this->check_token();
		$id = intval(I('get.id'));
		if (empty($id)) {
			$this->error(1001);
		}
		$map = array(
			'id' => $id,
			'member_id' => $this->uid,
		);
		if (M('demand')->where($map)->save(array('status' => '-1'))) {
			$this->success();
		} else {
			//5011数据不存在
			$this->error(5011);
		}
	}

	/**
	 * 下架
	 */
	public function demandDown_get() {
		$this->check_token();
		$id = intval(I('get.id'));
		if (empty($id)) {
			$this->error(1001);
		}
		$map = array(
			'id' => $id,
			'member_id' => $this->uid,
		);
		if (M('demand')->where($map)->save(array('status' => '0'))) {
			$this->success();
		} else {
			//5011数据不存在
			$this->error(5011);
		}
	}

	/**
	 * 上架
	 */
	public function demandUp_get() {
		$this->check_token();
		$id = intval(I('get.id'));
		if (empty($id)) {
			$this->error(1001);
		}
		$map = array(
			'id' => $id,
			'member_id' => $this->uid,
		);
		if (M('demand')->where($map)->save(array('status' => '1'))) {
			$this->success();
		} else {
			//5011数据不存在
			$this->error(5011);
		}
	}

}