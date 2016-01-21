<?php

namespace Api\Controller;

use Think\Model;
use Think\Think;

class UserController extends ApiBaseController {
	
	/**
	 * token表名
	 *
	 * @var string
	 */
	private static $token_table = "api_tokens";
	
	// 注册，方法post
	// 3001：用户名已存在
	// 3002：手机号已存在
	// 3003：密码必须是6-30位
	public function register_post() {
		$body = $this->get_request_data ( "register" );
		if (is_array ( $body )) {
			
			if (self::check_body_fields ( $body, array (
					"username",
					"password",
					"phone",
					"verify",
					"rec_code" 
			) )) {
				$verify = $body ['verify'];
				$verify ['phone'] = $body ['phone'];
				
				// 验证短信验证码
				$status = VerifySmsController::verify_check ( $verify );
				
				if ($status === true) {
					
					$username = $body ['username'];
					$password = $body ['password'];
					$phone = $body ['phone'];
					
					$result = D ( 'User' )->register ( $username, $password, $phone, $body ['rec_code'] );
					if ($result && gettype ( $result ) == 'integer') {
						$data = array (
								'user_info' => array (
										'username' => $username,
										'phone' => $phone 
								) 
						);
						$this->success ( $data );
					}
					if (! $result) {
						$this->error ( 9001 );
					}
					
					if ($result && is_string ( $result )) {
						switch ($result) {
							case 'username' :
								$this->error ( 3001 );
								break;
							
							case 'phone' :
								$this->error ( 3002 );
								break;
							
							case 'password' :
								$this->error ( 3003 );
								break;
							case 'code' :
								$this->error ( 3024 );
						}
					}
				} else {
					$this->error ( $status );
				}
			}
		}
		
		$this->error ( 1001 );
	}

	public function find_password_put() {
		$body = $this->get_request_data ( "findPassword" );
		
		if (is_array ( $body ) && self::check_body_fields ( $body, array (
				'phone',
				'verify',
				'password',
				'confirmPassword' 
		) )) {
			// 验证密码是否一致
			if ($body ['password'] != $body ['confirmPassword']) {
				$this->error ( 2001 );
			}
			
			$verify = $body ['verify'];
			$verify ['phone'] = $body ['phone'];
			
			// 验证短信验证码
			$status = VerifySmsController::verify_check ( $verify );
			
			if ($status === true) {
				$ret = D ( 'User' )->edit_password ( array (
						'phone' => $body ['phone'] 
				), $body ['password'] );
				
				if ($ret === true) {
					// 修改密码成功
					$this->success ();
				} else if ($ret === false) {
					// 密码格式不合法
					$this->error ( 2002 );
				} else if ($ret === 0) {
					// 手机号码不存在
					$this->error ( 2003 );
				} else if ($ret === - 1) {
					// 新密码和原始密码一样
					$this->error ( 2004 );
				}
				
				// 操作数据库失败
				$this->error ( 9001 );
			}
			
			$this->error ( $status );
		}
		
		$this->error ( 1001 );
	}

	public function set_pwd_put() {
		$this->check_token ();
		$body = $this->get_request_data ( "passwordInfo" );
		
		if (is_array ( $body ) && self::check_body_fields ( $body, array (
				'oldPassword',
				'password',
				'confirmPassword' 
		) )) {
			
			// 验证密码是否一致
			if ($body ['password'] != $body ['confirmPassword']) {
				$this->error ( 2001 );
			}
			
			$ret = D ( 'User' )->edit_password ( array (
					'id' => $this->uid,
					'password' => md5 ( $body ['oldPassword'] ) 
			), $body ['password'] );
			
			if ($ret === true) {
				// 修改密码成功
				$this->success ();
			} else if ($ret === false) {
				// 密码格式不合法
				$this->error ( 2002 );
			} else if ($ret === 0) {
				// 手机号码不存在
				$this->error ( 2003 );
			} else if ($ret === - 1) {
				// 新密码和原始密码一样
				$this->error ( 2004 );
			}
			
			// 操作数据库失败
			$this->error ( 9001 );
		}
		
		$this->error ( 1001 );
	}

	public function set_user_status_put() {
		$this->check_token ();
		
		$body = $this->get_request_data ( "member" );
		
		if (is_array ( $body ) && self::check_body_fields ( $body, array (
				'status' 
		) )) {
			
			if ($body ['status'] == 'true') {
				$status = 1;
			} else if ($body ['status'] == 'false') {
				$status = 0;
			} else {
				$this->error ( 2005 );
			}
			
			$member = M ( 'member' );
			
			$memberStatus = $member->getFieldById ( $this->uid, 'status' );
			
			if ($memberStatus == $status || $member->where ( array (
					'id' => $this->uid 
			) )->setField ( array (
					'status' => $status 
			) )) {
				// 设置成功
				$this->success ();
			} else {
				// 设置失败
				$this->error ( 9001 );
			}
		}
		
		$this->error ( 1001 );
	}
	
	// 当前用户详细信息，方法get
	public function detail_get() {
		$this->check_token ();
		$this_user = D ( 'User' );
		$data = $this_user->detail ( $this->uid );
		$this->success ( $data );
	}
	
	// 指定用户详细信息,
	// 方法get
	public function user_get($id) {
		// $this->check_token();
		$this_user = D ( 'User' );
		$data = $this_user->detail ( $id );
		// unset($data['score']);
		$this->success ( $data );
	}
	
	// 更新用户信息，方法put
	public function update_put() {
		$this->check_token ();
		$body = $this->get_request_data ( "userInfo" );
		if (is_array ( $body )) {
			$this_user = D ( 'User' );
			$fields = $this_user->fields_can_update;
			$diff_keys = array_diff ( array_keys ( $body ), $fields );
			if (! $diff_keys) {
				if (isset ( $body ['nickname'] )) {
					$user_cnt = M ( 'member' )->where ( array (
							'nickname' => $body ['nickname'] 
					) )->count ();
					if ($user_cnt > 0) {
						$this->error ( 3001 );
					}
				}
				// 判断是否存在违法参数
				$result = $this_user->update ( $this->uid, $body );
				if (gettype ( $result ) == 'boolean') {
					if ($result) {
						$this->success ( $this_user->detail ( $this->uid ) );
					} else {
						$this->error ( 9001 );
					}
				} else if ($result == 3004) {
					$this->error ( $result );
				}
			} else {
				$this->error ( 1005 );
			}
		} else {
			$this->error ( 1001 );
		}
	}
	
	// 获取当前用户名片信息
	public function card_self_get() {
		$this->check_token ();
		$this_card = D ( 'Card' );
		$data = $this_card->info ( $this->uid );
		$this->success ( $data );
	}
	
	// 获取指定用户名片信息
	public function card_get($id) {
		$this->check_token ();
		$this_card = D ( 'Card' );
		$data = $this_card->info ( $id );
		$this->success ( $data );
	}
	
	// 当前用户名片信息更新
	public function card_put() {
		$this->check_token ();
		$card = $this->get_request_data ( "cardInfo" );
		$card_model = D ( 'Card' );
		if (is_array ( $card )) {
			$fields = $card_model->fields_can_update;
			$diff_keys = array_diff ( array_keys ( $card ), $fields );
			if (! $diff_keys) {
				$result = $card_model->update ( $this->uid, $card );
				if ($result) {
					$this->success ();
				} else {
					$this->error ( 9001 );
				}
			} else {
				$this->error ( 1005 );
			}
		} else {
			$this->error ( 1001 );
		}
	}

	/**
	 * 获取会员token(客户端)
	 */
	public function token_post() {
		$auth = $this->get_request_data ();
		// $auth['passwordCredentials']=array('username'=>'8876108190@qq.com','password'=>0);
		if ($auth && count ( $auth )) {
			$user = false;
			// 使用密码凭据登陆
			if (array_key_exists ( 'passwordCredentials', $auth )) {
				// 凭据数据
				$user = $this->___passwordCredentials ( $auth ['passwordCredentials'] );
			} else if (array_key_exists ( 'qqCredentials', $auth )) {
				// 凭据数据
				$user = $this->__credentials ( $auth ['qqCredentials'], 'Qq' );
			} else if (array_key_exists ( "weiboCredentials", $auth )) {
				$user = $this->__credentials ( $auth ['weiboCredentials'], 'Weibo' );
			} else if (array_key_exists ( "weixinCredentials", $auth )) {
				$user = $this->__credentials ( $auth ['weixinCredentials'], 'weixin' );
			}
			
			if ($user) {
				$this->return_user_info ( $user );
			}
		}
		// 无效请求
		$this->error ( 1001 );
	}

	/**
	 * 检查用户是否存在
	 */
	public function user_exists_post() {
		$query = $this->get_request_data ( 'query' );
		
		if (is_array ( $query ) && count ( $query ) == 1) {
			
			// 账号查找
			$nickname = $query ['nickname'];
			$phone = $query ['phone'];
			$email = $query ['email'];
			// 第三方关联查找
			$qq_open_id = $query ['qq'];
			$weibo_open_id = $query ['weibo'];
			$wechat_open_id = $query ['wechat'];
			
			$user_cnt = 0;
			
			if ($nickname || $phone || $email) {
				$user_cnt = M ( 'member' )->where ( array (
						'nickname' => $nickname,
						'email' => $email,
						'phone' => $phone,
						'_logic' => 'or' 
				) )->count ();
			} else if ($qq_open_id || $weibo_open_id || $wechat_open_id) {
				$user_cnt = M ( 'member_bin' )->where ( "uid>0 and ((type='Qq' and keyid='%s') or (type='Weibo' and keyid='%s') or (type='Wechat' and keyid='%s'))", $qq_open_id, $weibo_open_id, $wechat_open_id )->count ();
			}
			
			if ($user_cnt) {
				$this->sendHttpStatus ( 200 );
				exit ();
			} else {
				$this->error ( 1404 );
			}
		}
		$this->error ( 1001 );
	}

	/**
	 * 创建token
	 *
	 * @param array $user
	 *        	会员数据
	 * @return boolean 创建结果
	 */
	private function create_token($user) {
		$token_m = M ( self::$token_table );
		
		$str_obj = new \Org\Util\String ();
		$token = $str_obj->keyGen (); // 生成唯一GUID
		
		while ( $token_m->where ( array (
				'token' => $token 
		) )->count () ) {
			$token = $str_obj->keyGen (); // 如果GUID已存在，重新生成
		}
		
		$create_time = time (); // 创建时间
		$expires = strtotime ( "2 hours", $create_time ); // 2小时有效期
		
		$result = $token_m->add ( array (
				'token' => $token,
				'mid' => $user ['id'],
				'create_time' => $create_time,
				'role' => intval ( $user ["wap_role"] ),
				'expires' => $expires,
				'req_ip' => get_client_ip () 
		) );
		
		if ($result) {
			$mapp = M ( "member_app" );
			
			$app_data = $mapp->getByUid ( $user ['id'] );
			
			if (! $app_data) {
				$mapp->add ( array (
						"uid" => $user ['id'],
						"login_num" => 0 
				) );
				$login_num = 0;
			} else {
				$login_num = intval ( $mapp->getFieldByUid ( $user ['id'], "login_num" ) );
			}
			
			if (! $login_num) {
				
				$score = M ( "app_setting" )->getFieldByItem ( "first_login_coin", "value" );
				
				M ( "score_log" )->add ( array (
						"type" => "score",
						"uid" => $user ['id'],
						"uname" => $user ['nickname'],
						"score" => $score,
						"table_name" => "member",
						"table_id" => $user ['id'],
						"action" => "/v1/tokens",
						"msg" => "首次登陆APP客户端",
						"create_time" => time (),
						"status" => 1 
				) );
				
				M ( "member" )->where ( array (
						"id" => $user ['id'] 
				) )->setInc ( "coin", $score );
			}
			
			$mapp->where ( array (
					"uid" => $user ['id'] 
			) )->setInc ( "login_num" );
			
			return $token;
		}
		
		return false;
	}

	/**
	 * 返回会员数据与token
	 *
	 * @param array $user
	 *        	会员数据
	 */
	private function return_user_info($user) {
		$token = $user ['_auth-token_'];
		
		$com = D ( "Community" );
		
		$user ["community"] = $com->communities ( $user ['id'] );
		
		$user ["rec_code"] = convHex ( $user ["id"] );
		$user ['is_masters'] = 0;
		if ($user ['is_vip_order'] > 0) {
			$user ['is_masters'] = 1;
		}
		if (isset ( $user ['msg'] ))
			$msg = $user ['msg'];
			// 移除敏感或无用字段
			// unset($user['id']);
		unset ( $user ['password'] );
		unset ( $user ['email_verify_code'] );
		unset ( $user ['phone_verify_code'] );
		unset ( $user ['paypassword'] );
		unset ( $user ['reg_ip'] );
		unset ( $user ['_auth-token_'] );
		unset ( $user ['msg'] );
		
		// 构造回复数据
		$data = array (
				'member' => $user,
				'msg' => $msg,
				"X-Subject-Token" => $token 
		);
		$this->success ( $data );
	}

	public function user_binding_post() {
		$data = $this->get_request_data ( 'bindingCredentials' );
		$credential_keys = array (
				'key_id',
				'type',
				'open_info',
				'username',
				'password' 
		);
		$credentials_types = array (
				'Qq',
				'Weibo',
				'weixin' 
		);
		if (! $data or ! self::check_body_fields ( $data, $credential_keys ) or ! in_array ( $data ['type'], $credentials_types )) {
			$this->error ( 1001 );
		}
		$this_user = M ( 'member' )->where ( "nickname='%s' or email='%s' or phone='%s' and password='%s'", $data ['username'], $data ['username'], $data ['username'], md5 ( $username ['password'] ) )->find ();
		if (! is_array ( $this_user ) || $this_user ['status'] == - 1) {
			$this->error ( 1002 ); // 账号不存在（或已标记为删除）
		} else if ($this_user ['password'] != md5 ( $data ['password'] )) {
			$this->error ( 1003 ); // 密码不正确
		} else if ($this_user ['status'] == 0) {
			$this->error ( 1004 ); // 账号已被禁用
		}
		$is_binded = M ( 'member_bind' )->where ( array (
				'uid' => $this_user ['id'],
				'type' => $data ['type'] 
		) )->find ();
		if ($is_binded) {
			$this->error ( 1015 );
		}
		$result = M ( 'member_bind' )->data ( array (
				'uid' => $this_user ['id'],
				'type' => $data ['type'],
				'info' => $data ['open_info'],
				'keyid' => $data ['key_id'] 
		) )->add ();
		if ($result) {
			$user = $this->__credentials ( array (
					'key_id' => $data ['key_id'] 
			), $data ['type'] );
			if ($user) {
				$this->return_user_info ( $user );
			}
		}
		
		$this->error ( 9001 );
	}

	public function first_third_login_post() {
		$bind = D ( "member_bind" );
		$m = M ( "member" );
		
		$data = $this->get_request_data ();
		
		if ($this->check_body_fields ( $data, array (
				"type",
				"key_id",
				"open_info" 
		) )) {
			$type = $data ["type"];
			$key_id = $data ["key_id"];
			$open_info_jsonstr = $data ["open_info"];
			
			if ($bind->where ( array (
					"keyid" => $key_id,
					"type" => $type 
			) )->count ()) {
				$this->error ( 1014 );
			}
			
			$nickname = "";
			$avatar = "";
			
			$m->field ( "id" )->order ( "id desc" )->find ();
			
			$open_info = json_decode ( $open_info_jsonstr, true );
			
			if ($open_info && in_array ( $type, array (
					"Weibo",
					"Qq",
					"weixin" 
			) )) {
				
				$bind->add ( array (
						"uid" => 0,
						"type" => $type,
						"keyid" => $key_id,
						"info" => $open_info_jsonstr 
				) );
				
				$bind_id = $bind->getLastInsID ();
				
				$username = rand_string ( 3, 2 ) . '_' . rand_string ( 8 );
				
				$password = rand_password ( 6 );
				
				$member_data = array (
						"pid" => 0,
						"password" => md5 ( $password ),
						"nickname" => $username,
						"status" => 1,
						"school_id" => - 1,
						"area_id" => - 1,
						"university_id" => - 1,
						"college_id" => - 1,
						"major_id" => - 1,
						"is_vip_order" => 0,
						"hot" => 0,
						"last_post_time" => 0,
						"last_server_school" => - 1,
						"last_server_school_id" => - 1,
						"change_nickname" => 0 
				);
				
				if ($m->add ( $member_data )) {
					$uid = $m->getLastInsID ();
					
					for($i = 2; true; $i ++) {
						$nickname = 'xzb' . $i . $uid;
						
						$temp = $m->where ( array (
								'nickname' => $nickname 
						) )->find ();
						
						if ($temp) {
							continue;
						} else {
							$m->where ( array (
									"id" => $uid 
							) )->save ( array (
									"nickname" => $nickname 
							) );
							break;
						}
					}
					
					$bind->where ( "id={$bind_id}" )->setField ( "uid", $uid );
					
					$user = $this->__credentials ( array (
							'key_id' => $key_id 
					), $type );
					$user ['msg'] = "感谢您使用新助邦，您的新助邦用户名\"{$nickname}\"，登录密码\"{$password}\"";
					$this->sendsysMsg ( $uid, $nickname, $password );
					if ($user) {
						$this->return_user_info ( $user );
					}
				}
				$this->error ( 9001 );
			}
		}
		
		$this->error ( 1001 );
	}

	/**
	 * 第三方登录
	 *
	 * @param array $auth
	 *        	第三方账号信息
	 * @param string $type
	 *        	第三方账号类型，Qq,
	 *        	weixin,
	 *        	Weibo
	 */
	private function __credentials($auth, $type) {
		if (is_array ( $auth ) && array_key_exists ( "key_id", $auth )) {
			$keyid = $auth ['key_id'];
			
			// 查询用户数据
			$openid = M ( 'member_bind' )->where ( "type='%s' and keyid='%s'", $type, $keyid )->find ();
			if (! $openid) {
				$this->error ( 1013 );
			}
			$user = M ( 'member' )->where ( array (
					'id' => $openid ['uid'] 
			) )->find ();
			
			if (! is_array ( $user ) || $user ['status'] == - 1) {
				$this->error ( 1002 ); // 账号不存在（或已标记为删除）
			} else if ($user ['status'] == 0) {
				//账号被禁用时连同手机号一起返回
				$this->response(array(
						'success' => false,
						'code' => '1004',
						'message' => L('E_1004'),
						'phone' => $user['phone']
				), 'json');
			}
			
			$result = $this->create_token ( $user );
			if ($result) {
				$user ['_auth-token_'] = $result;
				return $user;
			} else {
				$this->error ( 9001 );
			}
		}
		
		return false;
	}

	/**
	 * 密码验证
	 *
	 * @param array $auth
	 *        	验证数据
	 * @return false/array
	 */
	private function ___passwordCredentials($auth) {
		if (is_array ( $auth ) && array_key_exists ( "username", $auth ) && array_key_exists ( "password", $auth )) {
			$username = $auth ['username'];
			$password = $auth ['password'];
			
			// 查询用户数据
			$user = M ( 'member' )->where ( "nickname='%s' or email='%s' or phone='%s'", $username, $username, $username )->find ();
			
			if (! is_array ( $user ) || $user ['status'] == - 1) {
				$this->error ( 1002 ); // 账号不存在（或已标记为删除）
			} else if ($user ['password'] != md5 ( $password )) {
				$this->error ( 1003 ); // 密码不正确
			} else if ($user ['status'] == 0) {
				//账号被禁用时连同手机号一起返回
				$this->response(array(
					'success' => false,
					'code' => '1004',
					'message' => L('E_1004'),
					'phone' => $user['phone']
				), 'json');
			}
			
			$result = $this->create_token ( $user );
			if ($result) {
				$user ['_auth-token_'] = $result;
				return $user;
			} else {
				$this->error ( 9001 );
			}
		}
		return false;
	}

	/**
	 * 获取token信息
	 */
	public static function get_token_info($token) {
		$obj = new Model ();
		if (! empty ( $token )) {
			$data = M ( self::$token_table )->getByToken ( $token );
			
			if (is_array ( $data )) {
				// 验证有效期
				if ($data ['expires'] - time () <= 0) {
					return false;
				}
				
				return $data;
			}
		}
		
		return false;
	}

	/**
	 * 添加说说
	 */
	public function switchRole_put() {
		$this->check_token ();
		
		$role = $this->get_request_data ( 'role' );
		
		if ($role == null) {
			$this->error ( 1001 );
		}
		
		$role = max ( 1, min ( 2, intval ( $role ) ) );
		
		if (M ( self::$token_table )->where ( array (
				"token" => self::$token->token 
		) )->save ( array (
				"role" => $role 
		) ) !== false) {
			if (M ( "member" )->where ( array (
					"id" => $this->uid 
			) )->save ( array (
					"wap_role" => $role 
			) ) !== false) {
				$this->success ();
			}
		}
		
		$this->error ( 1012 );
	}

	/*
	 * 退出登录
	 */
	public function logout_get() {
		$this->check_token ();
		if (M ( $this->token_table )->delete ( self::$token->token )) {
			$this->success ();
		} else {
			$this->error ();
		}
	}

	private function sendsysMsg($uid, $nickname, $password) {
		$content = '欢迎加入新助邦！你的初始注册名是：' . $nickname . ' 密码是：' . $password . '。你有一次修改注册名的机会，请到个人中心点击头像后即可修改。请同时修改密码！';
		$_POST = array (
				'content' => $content,
				'to_member_id' => $uid 
		);
		return update_data ( 'message' );
	}

	public function unlock_put() {
		$verify = $this->get_request_data ();
		
		// 验证短信验证码
		$status = VerifySmsController::verify_check ( $verify );
		
		if ($status === true) {
			$ret = M ( "member" )->where ( array (
					"phone" => $verify ["phone"] 
			) )->save ( array (
					"status" => 1 
			) );
			
			if ($ret !== false) {
				$this->success ();
			} else {
				$this->error ( 9001 );
			}
		} else {
			$this->error ( $status );
		}
	}

	public function chk_ephone_binding_get() {
		$this->check_token ();
		$uid = $this->uid;
		$options = array (
				"alias" => "mp",
				"where" => array (
						"id" => $uid 
				),
				"field" => "id,nickname,email,email_verified,phone,phone_verified" 
		);
		$row = M ( "member" )->find ( $options );
		$this->success ( $row );
	}

	public function phone_binding_put() {
		$this->check_token ();
		$uid = $this->uid;
		
		$verify = $this->get_request_data ();
		
		// 验证短信验证码
		$status = VerifySmsController::verify_check ( $verify );
		
		if ($status === true) {
			$User = M ( "member" ); // 实例化User对象
			                        // 要修改的数据对象属性赋值
			$data ['phone_verified'] = 1;
			$data ['phone'] = $verify ["phone"];
			$ret = $User->where ( array (
					'id' => $uid 
			) )->save ( $data ); // 根据条件更新记录
			
			if ($ret !== false) {
				$options = array (
						"alias" => "mp",
						"where" => array (
								"id" => $uid 
						),
						"field" => "id,nickname,email,email_verified,phone,phone_verified" 
				);
				$row = $User->find ( $options );
				$this->success ( $row );
			} else {
				$this->error ( 9001 );
			}
		} else {
			$this->error ( $status );
		}
	}
}