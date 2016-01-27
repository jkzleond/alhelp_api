<?php

namespace Api\Controller;

class TalksController extends ApiBaseController {
	protected $is_check_token = FALSE; //默认不需要检查token

	public function _initialize()
	{
		//如果带有X_AUTH_TOKEN头则需要检测token
		$x_auth_token = $this->get_header('X_AUTH_TOKEN');
		if ( !empty($x_auth_token) ) {
			$this->is_check_token = true;
		}
	}

	// 获取说说详情
	public function talks_details_get() {

		//$this->check_token();
		$id = I ( 'get.id', 0, 'intval' );
		
		if ($id > 0) {
			$mp = M ( 'member_post' );
			$m = M ( 'member' );
			
			$mp_where = array (
					'mp.id' => $id,
					'mp.pid' => 0 
			);
			
			$mp->alias('mp')
					->join("inner join member m on m.id=mp.member_id")
					->join("left join community c on c.id = mp.community_id")
					->where($mp_where)
					->field("mp.id,mp.member_id,mp.member_nickname,mp.school_id,mp.content,mp.praise_num,mp.type,mp.add_time, c.table_type as community_table_type, c.table_id as community_table_id");

			$talk = $mp->find ();
			
			if (! $talk || $this->getTalkAllow ( $this->uid, $talk )) {
				$this->error ( 1404 );
			} else if ($talk ['status'] == '-1') {
				$this->error ( 4410 );
			}
			
			$talk ["self"] = $talk ["member_id"] == $this->uid;
			
			$talk ['attachments'] = $this->getImages( $talk ['id']);
			
			$talk ["community"] = D ( "Community" )->getcommunityInfo ( $talk ["community_id"] );
			$talk ["avatar"] = GetSmallAvatar ( $talk ['member_id'] );

			//如果在登陆状态,需要查询该用户是否赞过该说说
			if ($this->uid) {
				$is_praised = M('praise')->where(array(
					'table_name' => 'member_post',
					'member_id' => $this->uid,
					'catid' => $talk['id'],
					'status' => '1'
				))->count() ? '1' : '0';

				$talk['is_praised'] = $is_praised;

				$is_com_joined = M('Community')->where(array(
						'member_id' => $this->uid,
						'table_id' => $talk['community_table_id'],
						'table_type' => $talk['community_table_type'],
						'status' => '1'
				))->count() ? '1' : '0';

				$talk['is_com_joined'] = $is_com_joined;
			}
			
			$c_where = array (
					'mp.pid' => $id,
					'mp.status' => 1,
					'mpm.m_p_id' => $id 
			);
			
			if ($this->uid != $talk ['member_id']) {
				$_c_where = array ();
				$_c_where ['mp.hide'] = 0;
				$_c_where ["mp.member_id"] = $this->uid;
				$_c_where ["_logic"] = "or";
				
				$c_where ["_complex"] = $_c_where;
			}
			
			// 回复相关
			$comment = $mp->alias ( 'mp' )
					->join("left join member m on m.id=mp.member_id")
					->join("left join member_post_message mpm on mpm.new_id=mp.id and mpm.type=2")
					->join("left join ( select id, table_id from community where table_type = 'school' ) c on c.id = mp.community_id")
					->join("left join school s on s.id = c.table_id")
					->field("mp.id,mp.member_id,mp.member_nickname,mp.content,mp.add_time,mpm.to_member_id, if(s.group_member_id != 0 and s.group_member_id = mpm.from_member_id, '1', '0') as is_com_owner")
					->where($c_where)
					->order('mp.add_time asc')
					->select ();
			// dump($mp->_sql());
			$tmp = array ();
			foreach ( $comment as $c ) {
				$c ['avatar'] = GetSmallAvatar ( $c ['member_id'] );
				/*
				 * if ($c['type'] >= 2 && $c['to_member_id'] !=
				 * $talk['member_id']) {
				 * //$c['to'] = $c['to_member_id'];
				 * $c['to'] = $m->getFieldById($c['to_member_id'], 'nickname');
				 * } else {
				 * $c['to'] = null;
				 * }
				 */
				$c ['to'] = '';
				// unset($c['type']);
				// unset($c['to_member_id']);
				$tmp [] = $c;
				
				$a2 = $this->getChildComent ( $m, $mp, $id, $c ['id'] );
				if (! empty ( $a2 )) {
					$tmp = array_merge ( $tmp, $a2 );
				}
			}
			
			$data = array (
					'details' => $talk,
					'comment' => $tmp 
			);
			
			// echo $mp->getLastSql();
			$this->success ( $data );
		}
		
		$this->error ( 1001 );
	}

	private function getChildComent($m, $mp, $id, $m_p_id) {
		$c_where = array (
				'mp.pid' => $id,
				'mp.status' => 1,
				'mpm.m_p_id' => $m_p_id 
		);
		
		// 回复相关
		$comment = $mp->alias ( 'mp' )
				->join("left join ( select id, table_id from community where table_type = 'school' ) c on c.id = mp.community_id")
				->join("left join school s on s.id = c.table_id")
				->join("left join member m on m.id=mp.member_id")
				->join("left join member_post_message mpm on mpm.new_id=mp.id and mpm.type=2")
				->field("mp.id,mp.member_id,mp.member_nickname,mp.content,mp.add_time,mpm.to_member_id, if(s.group_member_id != 0 and s.group_member_id = mpm.from_member_id, '1', '0') as is_com_owner")
				->where($c_where)
				->order('mp.add_time asc')
				->select();
		// dump($mp->_sql());
		if (empty ( $comment )) {
			return;
		}
		$tmp = array ();
		foreach ( $comment as $c ) {
			$c ['avatar'] = GetSmallAvatar ( $c ['member_id'] );
			$c ['to'] = $m->getFieldById ( $c ['to_member_id'], 'nickname' );
			// unset($c['type']);
			// unset($c['to_member_id']);
			$tmp [] = $c;
			$a2 = $this->getChildComent ( $m, $mp, $id, $c ['id'] );
			if (! empty ( $a2 )) {
				$tmp = array_merge ( $tmp, $a2 );
			}
		}
		
		return $tmp;
	}
	
	// 评论说说
	public function talks_comment_post() {
		$this->check_token ();
		$id = I ( 'get.id', 0, 'intval' );
		$comment = $this->get_request_data ();
		
		if ($id > 0 && $this->check_body_fields ( $comment, array (
				"content",
				"pillow_talk" 
		) )) {
			
			$member = M ( 'member' );
			$mp = M ( 'member_post' );
			
			if (! $mp->where ( array (
					'id' => $id,
					'status' => 1 
			) )->count ()) {
				$this->error ( 1404 );
			}
			
			$nickname = $member->getFieldById ( $this->uid, 'nickname' );
			
			$comment_data = array (
					'member_id' => $this->uid,
					'member_nickname' => $nickname,
					'member_name' => $nickname,
					'add_time' => date ( 'Y-m-d H:i:s' ),
					'content' => $comment ['content'],
					'parent_id' => $id,
					'school_id' => max ( 0, $member->getFieldById ( $this->uid, 'university_id' ) ),
					'last_comment_time' => time (),
					'time_top' => 0,
					'time_hot' => 0,
					'time_announcement' => 0,
					'information_id' => 0,
					'show_in_all' => 0,
					'community_id' => max ( 0, $mp->getFieldById ( $this->uid, 'community_id' ) ) 
			);
			
			if ($pid = $mp->getFieldById ( $id, 'pid' )) {
				$comment_data ['pid'] = $pid;
			} else {
				$comment_data ['pid'] = $id;
			}
			
			// 私信给对方,其它人不可见.
			if (1 == $comment ['pillow_talk']) {
				$comment_data ['hide'] = 1;
			} else if (1 == $mp->getFieldById ( $id, 'hide' )) {
				$comment_data ['hide'] = 1;
			} else {
				$comment_data ['hide'] = 0;
			}
			
			if ($mp->add ( $comment_data )) {
				$res = $mp->getLastInsID ();
				
				$images = $comment ['images'];
				$where = array (
						'id' => $id 
				);
				
				$pid = $comment_data ['pid'];
				
				$where = array (
						'id' => $pid 
				);
				
				$mp->where ( $where )->setInc ( 'replies_num', 1 );
				
				$this->joinImages ( $images, 'member_post', $res, $this->uid );
				
				if (M ( 'member_post_message' )->add ( array (
						'm_p_id' => $id,
						'new_id' => $res,
						'to_member_id' => $mp->getFieldById ( $id, "member_id" ),
						'add_time' => date ( 'Y-m-d H:i:s' ),
						'is_read' => 0,
						'type' => 2,
						'from_member_id' => $this->uid 
				) )) {
					$this->success ( array (
							"comment_id" => $res 
					) );
				}
			}
			
			$this->error ( 1500 );
		}
		
		$this->error ( 1001 );
	}
	
	// 发表说说
	public function add_talk_post() {
		$this->check_token ();
		$talk_post = $this->get_request_data ();
		
		if (isset ( $talk_post ["content"] ) && isset ( $talk_post ["type"] ) && isset ( $talk_post ["community_id"] )) {
			$m = M ( 'member' );
			$mp = M ( 'member_post' );
			$fid = intval ( $talk_post ['fid'] );
			// 是否已转发过
			if ($fid) {
				$isHas = $mp->where ( array (
						"fid" => $talk_post ['fid'],
						"member_id" => $this->uid,
						"status" => 1 
				) )->count ();
				
				if ($isHas) {
					$this->error ( 4011 );
				}
				
				if (! $mp->where ( array (
						'id' => $fid 
				) )->count ()) {
					$this->error ( 4011 );
				}
			}
			
			if (empty ( $talk_post ["content"] )) {
				$this->error ( 1404 );
			}
			
			$nickname = $m->getFieldById ( $this->uid, 'nickname' );
			
			$talk_data = array (
					"member_id" => $this->uid,
					'member_nickname' => $nickname,
					'member_name' => $nickname,
					"fid" => max ( 0, $fid ),
					"type" => intval ( $talk_post ["type"] ),
					"content" => $talk_post ["content"],
					"community_id" => max ( 0, intval ( $talk_post ['community_id'] ) ),
					"add_time" => date ( "Y-m-d H:i:s" ),
					'time_top' => 0,
					'time_hot' => 0,
					'pid' => 0,
					'parent_id' => 0,
					'time_announcement' => 0,
					'information_id' => 0,
					'last_comment_time' => 0,
					'show_in_all' => $m->getFieldById ( $this->uid, "is_vip" ) ? 1 : 0,
					'hide' => 0 
			);
			
			if ($talk_data ['community_id']) // 只发送到圈子内,会把说说标记为当前默认圈子,
{
				$community = M ( "community" )->getById ( $talk_data ['community_id'] );
				if ($community) {
					if ('school' == $community->table_type) {
						$talk_data ['school_id'] = $community->table_id;
					} else {
						$talk_data ['school_id'] = 0;
					}
				} else if (! $talk_data ['show_in_all']) {
					$this->error ( 4013 );
				} else {
					$talk_data ['community_id'] = 0;
				}
			}
			
			if ($mp->add ( $talk_data )) {
				
				$id = $mp->getLastInsID ();
				$images = $talk_post ['images'];
				
				$this->joinImages ( $images, 'member_post', $id, $this->uid );
				
				if ($fid) {
					// 保存最后发布说说的时间
					$m->where ( array (
							'id' => $this->uid 
					) )->save ( array (
							'last_post_time' => time () 
					) );
					
					// 追加原创说说被转发的次数
					M ( 'member_post' )->where ( 'id=' . $fid )->setInc ( 'f_number', 1 );
				}
				
				if (is_array ( $talk_post ['remind'] )) {
					// 给被@的人发消息
					foreach ( $talk_post ['remind'] as $rid ) {
						// 在关注中找到,给$nickname发消息
						M ( 'member_post_message' )->add ( array (
								'm_p_id' => $fid ? $fid : $id, // 说说ID
								'new_id' => $fid ? $id : 0,
								'to_member_id' => $rid,
								'add_time' => date ( 'Y-m-d H:i:s' ),
								'is_read' => 0,
								'type' => $fid ? 1 : 4,
								'from_member_id' => $this->uid 
						) );
					}
				}
				
				$this->success ( array (
						"talks_id" => $id 
				) );
			}
			
			$this->error ( 1500 );
		}
		
		$this->error ( 1001 );
	}
	
	// 获取指定说说的相似度
	public function talk_similarity_get() {
		$this->check_token ();
		$mp = M ( "member_post" );
		
		$scws = scws_new ();
		$scws->set_charset ( 'utf8' );
		$scws->set_dict ( ini_get ( 'scws.default.fpath' ) . '/dict.utf8.xdb' );
		$scws->set_rule ( ini_get ( 'scws.default.fpath' ) . '/rules.utf8.ini' );
		
		$scws->set_multi ( 1 );
		$scws->set_ignore ( true );
		
		$id = I ( 'get.id', 0, 'intval' );
		
		$centent = strip_tags ( $mp->getFieldById ( $id, "content" ) );
		
		$scws->send_text ( $centent );
		
		$words = array ();
		
		// 汇总分词结果
		while ( $tmp = $scws->get_result () ) {
			foreach ( $tmp as $t ) {
				if (($t ['attr'] == "en" || $t ['attr'] == "n" || $t ['attr'] == "r") && ! in_array ( $t ['word'], $words ) && mb_strlen ( $t ['word'], "utf-8" ) > 1) {
					$words [] = $t ['word'];
				}
			}
		}
		
		if (count ( $words ) > 2) {
			$order = array ();
			$where = array (
					"id" => array (
							'neq',
							$id 
					),
					"pid" => 0 
			);
			$_where = array ();
			foreach ( $words as $w ) {
				$w = mysql_escape_string ( $w );
				$order [] = "(case when LOCATE('{$w}',content) > 0 then 1 else 0 end)";
				
				$_where [] = "content like '%{$w}%'";
			}
			
			$order = "add_time desc,(" . implode ( " + ", $order ) . ") desc";
			$_where = "(" . implode ( " or ", $_where ) . ")";
			
			$where ["_string"] = $_where;
			
			$data = $mp->where ( $where )->field ( "id,member_nickname,content" )->order ( $order )->limit ( 10 )->select ();
			
			$this->success ( $data );
		} else {
			$this->success ();
		}
	}
	
	// 获取说说列表
	public function talk_list_get() {
		// echo 1;
		$mp = M ( "member_post" );
		
		$uid = I ( 'get.uid', "self" );
		if ($uid == 'all') {
			$uid = - 1;
		} else if ($uid == "self") {
			$this->check_token ();
			$uid = $this->uid;
		}
		
		$lwhere = array (
				"mp.pid" => 0,
				"mp.status" => array (
						"neq",
						"-1" 
				) 
		);
		
		if ($uid >= 0) {
			$lwhere ['mp.member_id'] = $uid;
		}

		//提交了community_id则只查询指定圈子的说说
		$community_id = $this->get_request_data('community_id');

		echo json_encode($this->get_request_data());

		if ($community_id) {
			$lwhere['mp.community_id'] = $community_id;
		}

		$options = array (
				"alias" => "mp",
				"where" => $lwhere,
				"join" => array (
						"inner join member m on m.id = mp.member_id and m.status = 1",
						"left join ( select id, table_id from community where table_type = 'school' ) c on c.id = mp.community_id",
						"left join school s on s.id = c.table_id"
				),
				"field" => "mp.*, if(s.group_member_id != 0 and s.group_member_id = mp.member_id, '1', '0') as is_com_owner",
				"order" => "mp.time_announcement desc ,mp.time_top desc,mp.time_hot desc,mp.add_time desc"
		);

		$pageNum = intval ( I ( "get.page", 1 ) );
		
		if ($pageNum) {
			
			$mp->options = $options;
			
			$count = $mp->count ();
			
			$_GET ["p"] = max ( 1, $pageNum );
			
			$Page = new \Think\Page ( $count, 10 );
			
			$mp->limit ( $Page->firstRow . ',' . $Page->listRows );
			
			$pageCount = ceil ( $Page->totalRows / $Page->listRows );
		}
		
		$data = $mp->select ( $options );
		// var_dump($mp->_sql());
		
		foreach ( $data as &$talk ) {
			
			// 附件相关
			$talk ['attachments'] = $this->getImages( $talk ['id']);
			
			$talk ["community"] = D ( "Community" )->getcommunityInfo ( $talk ["community_id"] );
			$talk ["avatar"] = GetSmallAvatar ( $talk ['member_id'] );
			
			$c_where = array (
					'mp.pid' => $talk ["id"],
					'mp.status' => 1 
			);
			
			$talk ['is_hot'] = $talk ['time_hot'] > 0 ? 1 : 0;
			$talk ['is_top'] = $talk ['time_top'] > 0 ? 1 : 0;
			$talk ['is_announcement'] = $talk ['time_announcement'] > 0 ? 1 : 0;

			//如果在登陆状态,需要查询该用户是否赞过该说说
			if ($this->uid) {
				$is_praised = M('praise')->where(array(
						'table_name' => 'member_post',
						'member_id' => $this->uid,
						'catid' => $talk['id'],
						'status' => '1'
				))->count() ? '1' : '0';

				$talk['is_praised'] = $is_praised;
			}
			
			// if ($this->uid && $this->uid != $talk['member_id']) {
			// $_c_where = array();
			// $_c_where['mp.hide'] = 0;
			// $_c_where["mp.member_id"] = $this->uid;
			// $_c_where["_logic"] = "or";
			
			// $c_where["_complex"] = $_c_where;
			// } else {
			// $c_where['mp.hide'] = 0;
			// }
			
			// 回复相关
			// $comment = $mp
			// ->alias('mp')
			// ->join("left join member m on m.id=mp.member_id")
			// ->join("left join member_post_message mpm on mpm.new_id=mp.id and
			// mpm.type=2")
			// ->field("mp.*")
			// ->where($c_where)
			// ->order('mp.add_time desc')
			// ->select();
			// dump($mp->_sql());
			// foreach ($comment as &$c) {
			// $c['avatar'] = GetSmallAvatar($c['member_id']);
			// if ($c['type'] >= 2 && $c['to_member_id'] != $talk['member_id'])
			// {
			// $c['to'] = $c['to_member_id'];
			// } else {
			// $c['to'] = null;
			// }
			// unset($c['type']);
			// unset($c['to_member_id']);
			// }
			
			// $talk["comment"] = $comment;
		}
		
		$data = array (
				'list' => $data,
				'count' => $count,
				'next_page' => null 
		);
		
		if ($pageCount > 1 && $pageNum < $pageCount) {
			if ($uid == - 1) {
				$data ['next_page'] = $this->url ( "/v1/talks/list/all/page/" . (++ $pageNum) );
			} else if ($uid == $this->uid) {
				$data ['next_page'] = $this->url ( "/v1/talks/list/page/" . (++ $pageNum) );
			} else {
				$data ['next_page'] = $this->url ( "/v1/talks/list/{$uid}/page/" . (++ $pageNum) );
			}
		}
		
		$this->success ( $data );
	}
	
	// 根据学校学院专业获取说说说列表
	public function talk_where_get() {
		// echo 1;
		$type = I ( 'get.type' );
		// $uid = I('get.uid', 0, "intval");
		$type_id = I ( 'get.type_id', 0, 'intval' );
		
		if ($type && max ( 0, $type_id )) {
			
			$join = array (
					"inner join member m on m.id = mp.member_id and m.status = 1",
					"inner join community c on c.id = mp.community_id",
					"inner join school s on s.id = c.table_id and c.table_type = 'school'" 
			);
			
			$where = array (
					"mp.pid" => 0,
					"mp.status" => array (
							"neq",
							"-1" 
					) 
			);
			
			if ($type_id) {
				$where ["s.id"] = $type_id;
			}
			
			switch (strtolower ( $type )) {
				case 'university' :
					$where ["s.type"] = 2;
					break;
				case 'major' :
					$where ["s.type"] = 4;
					break;
				case 'school' :
					$where ["s.type"] = 3;
					break;
				case 'all' :
					break;
				
				default :
					$this->error ( 1001 );
					break;
			}
			
			$options = array (
					"alias" => "mp",
					"where" => $where,
					"join" => $join,
					// "field" => "mp.id,mp.member_nickname,mp.content",
					"field" => "mp.*, if(s.group_member_id != 0 and s.group_member_id = mp.member_id, '1', '0') as is_com_owner",
					"order" => "mp.time_announcement desc ,mp.time_top desc,mp.time_hot desc,mp.add_time desc" 
			);
			
			$pageNum = intval ( I ( "get.page", 1 ) );
			$mp = M ( 'member_post' );
			if ($pageNum) {
				
				$mp->options = $options;
				
				$count = $mp->count ();
				
				$_GET ["p"] = max ( 1, $pageNum );
				
				$Page = new \Think\Page ( $count, 10 );
				
				$mp->limit ( $Page->firstRow . ',' . $Page->listRows );
				
				$pageCount = ceil ( $Page->totalRows / $Page->listRows );
			}
			
			$data = $mp->select ( $options );
			
			foreach ( $data as &$talk ) {
				
				// 附件相关
				$talk ['attachments'] = $this->getImages( $talk ['id']);
				
				$talk ["community"] = D ( "Community" )->getcommunityInfo ( $talk ["community_id"] );
				
				$talk ["avatar"] = GetSmallAvatar ( $talk ['member_id'] );
				
				$talk ['is_hot'] = $talk ['time_hot'] > 0 ? 1 : 0;
				$talk ['is_top'] = $talk ['time_top'] > 0 ? 1 : 0;
				$talk ['is_announcement'] = $talk ['time_announcement'] > 0 ? 1 : 0;

				//如果在登陆状态,需要查询该用户是否赞过该说说
				if ($this->uid) {
					$is_praised = M('praise')->where(array(
							'table_name' => 'member_post',
							'member_id' => $this->uid,
							'catid' => $talk['id'],
							'status' => '1'
					))->count() ? '1' : '0';

					$talk['is_praised'] = $is_praised;
				}
				
				// $c_where = array(
				// 'mp.pid' => $talk["id"],
				// 'mp.status' => 1,
				// );
				
				// $c_where['mp.hide'] = 0;
				
				// //回复相关
				// $comment = $mp
				// ->alias('mp')
				// ->join("left join member m on m.id=mp.member_id")
				// ->join("left join member_post_message mpm on mpm.new_id=mp.id
				// and mpm.type=2")
				// ->field("mp.id,mp.member_id,mp.member_nickname,mp.content,mpm.to_member_id,mpm.type")
				// ->where($c_where)
				// ->order('mp.add_time desc')
				// ->select();
				
				// foreach ($comment as &$c) {
				// $c['avatar'] = GetSmallAvatar($c['member_id']);
				// if ($c['type'] >= 2 && $c['to_member_id'] !=
				// $talk['member_id']) {
				// $c['to'] = $c['to_member_id'];
				// } else {
				// $c['to'] = null;
				// }
				
				// unset($c['type']);
				// unset($c['to_member_id']);
				// }
				
				// $talk["comment"] = $comment;
			}
			
			$data = array (
					'list' => $data,
					'count' => count ( $data ),
					'next_page' => null 
			);
			
			if ($pageCount > 1 && $pageNum < $pageCount) {
				$data ['next_page'] = $this->url ( "/v1/talks/listby/{$type}/{$type_id}/page/" . (++ $pageNum) );
			}
			
			$this->success ( $data );
		}
		
		$this->error ( 1001 );
	}
	
	// 获取我的粉丝说说
	public function talk_fans_list_get() {
		$this->talk_follow_list ( "fans" );
	}
	
	// 获取我关注的人的说说
	public function talk_follow_list_get() {
		$this->talk_follow_list ( "follow" );
	}

	private function talk_follow_list($type) {
		$mp = M ( "member_post" );
		
		$this->check_token ();
		$uid = $this->uid;
		
		$lwhere = array (
				"mp.pid" => 0,
				"mp.status" => array (
						"neq",
						"-1" 
				) 
		);

		//提交了community_id则只查询指定圈子的说说
		$community_id = $this->get_request_data('community_id');

		if ($community_id) {
			$lwhere['mp.community_id'] = $community_id;
		}
		
		$options = array (
				"alias" => "mp",
				"where" => $lwhere,
				"join" => array (
						($type == "follow" ? "inner join follow f on f.from_member_id={$uid} and f.to_member_id=mp.member_id" : "inner join follow f on f.from_member_id=mp.member_id and f.to_member_id={$uid}"),
						"inner join member m on m.id = mp.member_id and m.status = 1",
						"left join community c on c.id = mp.community_id",
						"left join school s on s.id = c.table_id and c.table_type = 'school'"
				),
				"field" => "mp.*, if(s.group_member_id != 0 and s.group_member_id = mp.member_id, '1', '0') as is_com_owner",
				"order" => "mp.time_announcement desc ,mp.time_top desc,mp.time_hot desc,mp.add_time desc" 
		);
		
		$pageNum = intval ( I ( "get.page", 1 ) );
		
		if ($pageNum) {
			
			$mp->options = $options;
			
			$count = $mp->count ();
			
			$_GET ["p"] = max ( 1, $pageNum );
			
			$Page = new \Think\Page ( $count, 10 );
			
			$mp->limit ( $Page->firstRow . ',' . $Page->listRows );
			
			$pageCount = ceil ( $Page->totalRows / $Page->listRows );
		}
		
		$data = $mp->select ( $options );

		foreach ( $data as &$talk ) {
			
			// 附件相关
			$talk ['attachments'] = $this->getImages( $talk ['id']);
			
			$talk ["community"] = D ( "Community" )->getcommunityInfo ( $talk ["community_id"] );
			$talk ["avatar"] = GetSmallAvatar ( $talk ['member_id'] );
			
			$talk ['is_hot'] = $talk ['time_hot'] > 0 ? 1 : 0;
			$talk ['is_top'] = $talk ['time_top'] > 0 ? 1 : 0;
			$talk ['is_announcement'] = $talk ['time_announcement'] > 0 ? 1 : 0;

			//如果在登陆状态,需要查询该用户是否赞过该说说
			if ($this->uid) {
				$is_praised = M('praise')->where(array(
						'table_name' => 'member_post',
						'member_id' => $this->uid,
						'catid' => $talk['id'],
						'status' => '1'
				))->count() ? '1' : '0';

				$talk['is_praised'] = $is_praised;
			}
			
			// $c_where = array(
			// 'mp.pid' => $talk["id"],
			// 'mp.status' => 1,
			// );
			
			// if ($this->uid && $this->uid != $talk['member_id']) {
			// $_c_where = array();
			// $_c_where['mp.hide'] = 0;
			// $_c_where["mp.member_id"] = $this->uid;
			// $_c_where["_logic"] = "or";
			
			// $c_where["_complex"] = $_c_where;
			// } else {
			// $c_where['mp.hide'] = 0;
			// }
			
			// //回复相关
			// $comment = $mp
			// ->alias('mp')
			// ->join("left join member m on m.id=mp.member_id")
			// ->join("left join member_post_message mpm on mpm.new_id=mp.id and
			// mpm.type=2")
			// ->field("mp.*")
			// ->where($c_where)
			// ->order('mp.add_time desc')
			// ->select();
			// // dump($mp->_sql());
			// foreach ($comment as &$c) {
			// $c['avatar'] = GetSmallAvatar($c['member_id']);
			// if ($c['type'] >= 2 && $c['to_member_id'] != $talk['member_id'])
			// {
			// $c['to'] = $c['to_member_id'];
			// } else {
			// $c['to'] = null;
			// }
			// unset($c['type']);
			// unset($c['to_member_id']);
			// }
			
			// $talk["comment"] = $comment;
		}
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($pageCount > 1 && $pageNum < $pageCount) {
			if ($uid == $this->uid) {
				$data ['next_page'] = $this->url ( "/v1/talks/list/{$type}/page/" . (++ $pageNum) );
			} else {
				$data ['next_page'] = $this->url ( "/v1/talks/list/{$type}/{$uid}/page/" . (++ $pageNum) );
			}
		}
		
		$this->success ( $data );
	}

	private function getTalkAllow($uid, $talk) {
		$type = $talk ["type"];
		
		if ($type == 2) {
			return $talk ["member_id"] == $uid;
		} else if ($type == 3) {
			return FansController::isFans ( $uid, $talk ["member_id"] );
		} else if ($type == 4) {
			return FansController::isFans ( $talk ["member_id"], $uid );
		}
	}
	
	// 点赞
	public function praise_get() {
		$this->check_token ();
		$table = 'praise';
		$table_name = 'member_post';
		$catid = I ( 'get.id', 0, 'intval' );
		$uid = $this->uid;
		
		// 检测此赞是否存在
		$map = array (
				'table_name' => $table_name,
				'member_id' => $uid,
				'catid' => $catid 
		);
		$res = get_info ( $table, $map );
		if ($res) { // 取消赞
			$result = delete_data ( $table, array (
					'id' => $res ['id'] 
			) );
			if ($result) {
				// 更新赞的数量
				update_num ( $table_name, array (
						'id' => $catid 
				), 'praise_num', 1, false );
				$msg = array (
						'praise_status' => 2,
						'msg' => '取消成功' 
				);
			} else {
				$this->error ( 1500 );
			}
		} else { // 添加赞
			$_POST = array (
					'table_name' => $table_name,
					'member_id' => $uid,
					'catid' => $catid 
			);
			$result = update_data ( $table );
			
			if ($result) {
				// 更新赞的数量
				update_num ( $table_name, array (
						'id' => $catid 
				), 'praise_num', 1, true );
				
				$msg = array (
						'praise_status' => 1,
						'msg' => '添加成功' 
				);
			} else {
				$this->error ( 1500 );
			}
		}
		$this->success ( $msg );
	}
	
	// 获取名师说说列表
	public function talk_mslist_get() {
		$page = intval ( I ( 'get.page' ) );
		$page = empty ( $page ) ? 1 : $page;
		
		$mp = M ( "member_post" );
		
		$lwhere = array (
				"mp.pid" => 0,
				"mp.status" => array (
						"neq",
						"-1" 
				) 
		);
		$lwhere ['_string'] = " m.is_vip_order>0 ";

		//提交了community_id则只查询指定圈子的说说
		$community_id = $this->get_request_data('community_id');

		if ($community_id) {
			$lwhere['mp.community_id'] = $community_id;
		}
		
		$options = array (
				"alias" => "mp",
				"where" => $lwhere,
				"join" => array (
						"inner join member m on m.id = mp.member_id and m.status = 1",
						"left join community c on c.id = mp.community_id",
						"left join school s on s.id = c.table_id and c.table_type = 'school'"
				),
				"field" => "mp.*, if(s.group_member_id != 0 and s.group_member_id = mp.member_id, '1', '0') as is_com_owner",
				"order" => "mp.time_announcement desc ,mp.time_top desc,mp.time_hot desc,mp.add_time desc" 
		);
		
		$_GET ["p"] = $page;
		$mp->options = $options;
		
		$total = $mp->count ();
		
		$pageCount = ceil ( $total / $this->pageSize );
		if ($pageCount < $_GET ['p']) {
			$_GET ['p'] = $pageCount;
		}
		$pageObj = new \Think\Page ( $total, $this->pageSize );
		
		$options ['limit'] = $pageObj->firstRow . ',' . $pageObj->listRows;
		
		$data = $mp->select ( $options );
		// print_r($mp->_sql());
		
		foreach ( $data as &$talk ) {
			
			// 附件相关
			$att = M ( 'attachments' );
			$att = $att->where ( array (
					'table' => "member_post",
					'table_id' => $talk ['id'],
					'status' => 1 
			) )->field ( 'sha1' )->select ();
			foreach ( $att as &$a ) {
				$a = GetImage ( $a ['sha1'] );
			}
			$talk ['attachments'] = $att;
			
			$talk ["community"] = D ( "Community" )->getcommunityInfo ( $talk ["community_id"] );
			$talk ["avatar"] = GetSmallAvatar ( $talk ['member_id'] );
			
			$c_where = array (
					'mp.pid' => $talk ["id"],
					'mp.status' => 1 
			);
			
			$talk ['is_hot'] = $talk ['time_hot'] > 0 ? 1 : 0;
			$talk ['is_top'] = $talk ['time_top'] > 0 ? 1 : 0;
			$talk ['is_announcement'] = $talk ['time_announcement'] > 0 ? 1 : 0;

			//如果在登陆状态,需要查询该用户是否赞过该说说
			if ($this->uid) {
				$is_praised = M('praise')->where(array(
						'table_name' => 'member_post',
						'member_id' => $this->uid,
						'catid' => $talk['id'],
						'status' => '1'
				))->count() ? '1' : '0';

				$talk['is_praised'] = $is_praised;
			}
			
			// if ($this->uid && $this->uid != $talk['member_id']) {
			// $_c_where = array();
			// $_c_where['mp.hide'] = 0;
			// $_c_where["mp.member_id"] = $this->uid;
			// $_c_where["_logic"] = "or";
			
			// $c_where["_complex"] = $_c_where;
			// } else {
			// $c_where['mp.hide'] = 0;
			// }
			
			// 回复相关
			// $comment = $mp
			// ->alias('mp')
			// ->join("left join member m on m.id=mp.member_id")
			// ->join("left join member_post_message mpm on mpm.new_id=mp.id and
			// mpm.type=2")
			// ->field("mp.*")
			// ->where($c_where)
			// ->order('mp.add_time desc')
			// ->select();
			// dump($mp->_sql());
			// foreach ($comment as &$c) {
			// $c['avatar'] = GetSmallAvatar($c['member_id']);
			// if ($c['type'] >= 2 && $c['to_member_id'] != $talk['member_id'])
			// {
			// $c['to'] = $c['to_member_id'];
			// } else {
			// $c['to'] = null;
			// }
			// unset($c['type']);
			// unset($c['to_member_id']);
			// }
			
			// $talk["comment"] = $comment;
		}
		
		$data = array (
				'list' => $data,
				'count' => $total,
				'next_page' => null 
		);
		if ($pageCount > 1 && $page < $pageCount) {
			$data ['next_page'] = $this->url ( '/v1/talks/list/master/page/' . ++ $page );
		}
		
		$this->success ( $data );
	}

	/**
	 * 说说置顶
	 */
	public function top_put() {
		$id = I('get.id', 0, 'intval');
		if(!$id) $this->error(1001);
		$success = M('member_post')->setField('time_top', time())->where(array('id' => $id));
		if (!$success) {
			$this->error(1500);
		} else {
			$this->success();
		}
	}

	/**
	 * 说说设置热门
	 */
	public function hot_put() {
		$id = I('get.id', 0, 'intval');
		if(!$id) $this->error(1001);
		$success = M('member_post')->setField('time_hot', time())->where(array('id' => $id));
		if (!$success) {
			$this->error(1500);
		} else {
			$this->success();
		}
	}

	/**
	 * 说说设置公告
	 */
	public function announce_put() {
		$id = I('get.id', 0, 'intval');
		if(!$id) $this->error(1001);
		$success = M('member_post')->setField('time_announce', time())->where(array('id' => $id));
		if (!$success) {
			$this->error(1500);
		} else {
			$this->success();
		}
	}

	/**
	 * 删除说说
	 */
	public function talk_delete() {
		$this->check_token();
		$id = I('get.id', 0, 'intval');
		if($id) $this->error(1001);
		$talk_model = M('member_post');
		$talk = $talk_model->find($id);

		if(empty($talk)) $this->error(4004);

		$community = M('community')->field('member_id')->where(array('id' => $talk['community_id']))->find();

		if ($talk_model->member_id != $this->uid and $community and $community['member_id'] != $this->uid) {
			$this->error(1501);
		}

		$success = $talk_model->delete();

		if (!$success) {
			$this->error(1500);
		} else {
			$this->success();
		}
	}

	private function joinImages($imageIds, $table, $talks_id, $uid) {
		if (empty ( $imageIds )) {
			return false;
		}
		
		$arr = explode ( ',', $imageIds );
		foreach ( $arr as &$id ) {
			$id = intval ( $id );
		}
		$imageIds = implode ( ',', $arr );
		$map ['member_id'] = $uid;
		$map ['_string'] = "id in ($imageIds)";
		$data ['table'] = $table;
		$data ['table_id'] = $talks_id;
		return M ( 'attachments' )->where ( $map )->save ( $data );
	}

	private function getImages($talk_id) {
		// 附件相关
		$att = M ( 'attachments' )->where ( array (
				'table' => "member_post",
				'table_id' => $talk_id,
				'status' => 1 
		) )->select ();
		$tmp = array ();
		foreach ( $att as $a ) {
			$tmp [] = GetImage_new ( $a ['path'], $a ['sha1'] );
		}
		return $tmp;
	}
}