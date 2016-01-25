<?php

namespace Api\Controller;

class InformationController extends ApiBaseController {
	protected $type = array (
			'information',
			'notice',
			'yylive',
			'group' 
	);

	public function index_get() {
		$information = D ( 'Information' )->articles ( 'information', 1, null, true );
		$notice = D ( 'Information' )->articles ( 'notice', 1, null, true );
		$yylive = D ( 'Information' )->articles ( 'yylive', 1, null, true );
		$group = D ( 'Information' )->articles ( 'group', 1, null, true );
		$data = array (
				'information' => $information,
				'notice' => $notice,
				'yylive' => $yylive,
				'group' => $group 
		);
		$this->success ( $data );
	}

	public function article_get() {
		$id = intval ( I ( 'get.id' ) );
		$data = D ( 'Information' )->article ( $id );
		$this->success ( $data );
	}

	public function articles_get() {
		$type = I ( 'get.type' );
		$page = intval ( I ( 'get.page' ) );
		$self = intval ( I ( 'get.self' ) );
		if ($self) {
			$this->check_token ();
			$uid = $this->uid;
		} else {
			$uid = null;
		}
		$this->_type_check ( $type );
		$data = D ( 'Information' )->articles ( $type, $page, $uid );
		$this->success ( $data );
	}

	protected function _type_check($type) {
		if (! in_array ( $type, $this->type )) {
			$this->error ( 3014 );
		}
	}
	
	// 点赞
	public function praise_get() {
		$this->check_token ();
		$table = 'praise';
		$table_name = 'information';
		$catid = I ( 'get.id', 0, 'intval' );
		$uid = $this->uid; // 27192;
		                   
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

	function comment_post() {
		$this->check_token ();
		$id = I ( 'get.id', 0, 'intval' );
		$comment = $this->get_request_data ();
		
		if ($id > 0 && $this->check_body_fields ( $comment, array (
				"content" 
		) )) {
			if (! M ( 'information' )->where ( array (
					'id' => $id,
					'status' => 1 
			) )->count ()) {
				$this->error ( 1405 );
			}
			$_POST ['member_id'] = $this->uid;
			$rules = array (
					array (
							'content',
							'require',
							'内容不能为空！',
							1,
							'' 
					) 
			);
			$_POST ['content'] = checkhtml ( $comment ['content'] );
			$_POST ['table'] = 'information';
			$_POST ['table_id'] = $id;
			
			$result = update_data ( 'comment', $rules );
			if (is_numeric ( $result )) {
				$this->success ( array (
						"comment_id" => $result 
				) );
			} else {
				$this->error ( $result );
			}
		}
		$this->error ( 1001 );
	}

	public function attr_download_get() {
		$this->check_token();
		$id = I ( 'get.id' );
		$score = intval ( I ( 'get.score' ) );
		$fileArr = get_info ( 'attachments', array (
				'id' => $id 
		) );
		
		if (empty ( $fileArr )) {
			$this->error ( 1404 );
		}
		$filename = WEB_PATH . $fileArr ['path'];
		if (! is_file ( $filename )) {
			$this->error ( 1404 );
		}
		
		$uid = $this->uid;
		$score_log = M ( 'score_log' );
		$msg = "下载免费资料无需消耗金币或积分";
		
		
		$count = $score_log->where ( array (
				'table_id' => $id,
				'uid' => $uid 
		) )->count ();
		
		if ($count > 0) {
			$score=0;
			$msg = "重复在下载专区下载无需消耗金币";
		}
		
		$member = M ( 'member' );
		
		if ($score > 0) {
			
			if ($member->getFieldById ( $uid, 'coin' ) < $score) {
				$this->error ( 1031 );
			}
			$result = $member->where ( array (
					'id' => $uid 
			) )->setDec ( 'coin', $score );
			$msg = "下载专区资料消耗金币";
		}
		
		
		$score_log->add ( array (
				'uid' => $uid,
				'table_name' => 'attachments',
				'table_id' => $id,
				'uname' => $member->getFieldById ( $uid, 'nickname' ),
				'action' => MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME,
				'type' => '_coin',
				'score' => $score,
				'msg' => $msg,
				'create_time' => time () 
		) );

		update_num ( $fileArr ['table'], array (
				'id' => $fileArr ['table_id'] 
		), 'download_num', 1, true );
		
		$http = new \Org\Util\Http ();
		$ext = substr ( $filename, strrpos ( $filename, '.' ) + 1 );
		$res = $http->download ( $filename, $fileArr ['name'] );
	}
}