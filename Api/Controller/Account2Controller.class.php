<?php
namespace Api\Controller;

error_reporting(E_ALL);

class Account2Controller extends ApiBaseController {
	protected $account_list_type = array (
			'all',
			'pay_in',
			'pay_out',
			'income_in',
			'income_out' 
	);
	protected $order_type = array (
			0,
			1,
			2,
			3,
			4,
			5,
			6,
			7,
			8,
			9,
			10,
			11,
			77,
			88,
			99 
	);
	protected $banklist = array ();

	function __construct() {
		$banklist = C ( 'BANK_LIST' );
		foreach ( $banklist as $key => $val ) {
			$this->banklist [$key] = $val ['title'];
		}
		parent::__construct ();
	}

	public function account_list_get() {
		$this->check_token ();
		$type = I ( 'get.type' );
		$this->_check_account_type ( $type );
		$data = D ( 'Account2' )->account_list ( $this->uid, $type );
		$this->success ( $data );
	}

	public function account_expire_get() {
		$this->check_token ();
		$expire_string = I ( 'get.expire' );
		$result = D ( 'Account2' )->account_list ( $this->uid, 'all', $expire_string );
		if ($result == 3018) {
			$this->error ( 3018 );
		}
		$this->success ( $result );
	}

	protected function _check_account_type($type) {
		if (! in_array ( $type, $this->account_list_type )) {
			$this->error ( 3015 );
		}
	}

	public function orders_get() {
		$this->check_token ();
		$type = intval ( I ( 'get.type' ) );
		$this->_check_order_type ( $type );
		$result = D ( 'Order' )->orders ( $this->uid, $type );
		$this->success ( $result );
	}

	public function orders_expire_get() {
		$this->check_token ();
		$expire_string = I ( 'get.expire' );
		$result = D ( 'Order' )->orders ( $this->uid, 99, $expire_string );
		if ($result == 3018) {
			$this->error ( 3018 );
		}
		$this->success ( $result );
	}

	protected function _check_order_type($type) {
		if (! in_array ( $type, $this->order_type )) {
			$this->error ( 3016 );
		}
	}

	public function order_get($id) {
		$this->check_token ();
		$id = intval ( $id );
		$result = D ( 'Order' )->order ( $id, $this->uid );
		if ($result == 3017) {
			$this->error ( $result );
		}
		$this->success ( $result );
	}

	public function get_cash_post() {
		$this->check_token ();
		$body = $this->get_request_data ( "get_cash_info" );
		if (! is_array ( $body ) || ! self::check_body_fields ( $body, array (
				'address',
				'card_id',
				'balance' 
		) )) {
			$this->error ( 1001 );
		}
		$result = D ( 'Account2' )->get_cash ( $this->uid, $body ['card_id'], $body ['balance'], $body ['address'] );
		if ($result === 3019) {
			$this->error ( 3019 );
		}
		if ($result === 3020) {
			$this->error ( 3020 );
		}
		if ($result) {
			$this->success ();
		} else {
			$this->error ( 9001 );
		}
	}

	public function product_detail_get() {
		$type = I ( 'get.product_type' );
		$id = I ( 'get.product_id' );
		if (! self::check_body_fields ( $type, array (
				'learning_periods',
				'demand',
				'book',
				'bid' 
		) )) {
			$this->error ( 3021 );
		}
		$result = D ( 'Order' )->product_detail ( $type, $id );
		if ($result === 3022) {
			$this->error ( 3022 );
		}
		$this->success ( $result );
	}

	public function available_banklist_get() {
		$this->check_token ();
		
		$result = M ( 'bankcard' )->where ( array (
				'member_id' => $this->uid,
				'status' => 2 
		) )->select ();
		// $result = M ( 'bankcard' )->where ( array ('member_id' => 51,'status'
		// => 2 ) )->select ();
		
		int_to_string ( $result, array (
				'card_type' => $this->banklist 
		) );
		
		$this->success ( $result );
	}

	public function cash_recording_get() {
		$this->check_token ();
		$page = intval ( I ( 'get.page' ) );
		$page = empty ( $page ) ? 1 : $page;
		
		$status = intval ( I ( 'get.status' ) );
		
		$expire_string = I ( 'get.expire' );
		$expire_string = empty ( $expire_string ) ? '1w' : $expire_string;
		if (! in_array ( $status, array (
				0,
				1,
				2,
				3 
		) )) {
			$this->error ( 2005 );
		}
		if (empty ( $status )) {
			$map ['status'] = array (
					'egt',
					0 
			);
		} else {
			$map ['status'] = $status;
		}
		$map ['type'] = 1;
		$map ['member_id'] = $this->uid;
		
		if ($expire_string) {
			$expire_count = intval ( substr ( $expire_string, 0, - 1 ) );
			$expire_type = substr ( $expire_string, - 1 );
			$expire_types = array (
					'd',
					'w',
					'm',
					'y' 
			);
			if (! in_array ( $expire_type, $expire_types ) or ! $expire_count) {
				$this->error ( 3018 );
			}
			
			if ($expire_type == 'd') {
				$expire_day = $expire_count;
			} else if ($expire_type == 'w') {
				$expire_day = $expire_count * 7;
			} else if ($expire_type == 'm') {
				$expire_day = $expire_count * 31;
			} else if ($expire_type == 'y') {
				$expire_day = $expire_count * 366;
			}
			
			$now = time ();
			$expire_time = $now - 3600 * 24 * $expire_day;
			
			$now_date_string = date ( 'Y-m-d H:i:s', $now );
			$expire_time_string = date ( 'Y-m-d H:i:s', $expire_time );
			
			$map ['add_time'] = array (
					array (
							'EGT',
							$expire_time_string 
					),
					array (
							'ELT',
							$now_date_string 
					) 
			);
		}
		
		$_GET ["p"] = $page;
		$list = $this->lists ( D ( 'CashView' ), $map, 'id desc' );
		list ( $totals, $pageSize, $pageCount, $row ) = $list;
		
		int_to_string ( $row, array (
				'card_type' => $this->banklist 
		) );
		int_to_string ( $row, array (
				'status' => array (
						'1' => '待审核',
						'2' => '审核通过',
						'3' => '审核不通过' 
				) 
		) );
		$data = array (
				'list' => $row,
				'count' => $totals,
				'next_page' => null 
		);
		if ($pageCount > 1 && $page < $pageCount) {
			$data ['next_page'] = $this->url ( '/v1/account/cash_recording/' . $status . '/' . $expire_string . '/' . ++ $page );
		}
		$this->success ( $data );
	}

	public function coin_recording_get() {
		$this->check_token();
		$page = intval ( I ( 'get.page' ) );
		$page = empty ( $page ) ? 1 : $page;
		
		$expire_string = I ( 'get.expire' );
		$expire_string = empty ( $expire_string ) ? '1w' : $expire_string;
		$uid=$this->uid;
		$map ['status'] = 1;
	    $map ['uid'] = $uid;
		$map ['_string'] = " type='coin' or type='_coin'";
		
		if ($expire_string) {
			$expire_count = intval ( substr ( $expire_string, 0, - 1 ) );
			$expire_type = substr ( $expire_string, - 1 );
			$expire_types = array (
					'd',
					'w',
					'm',
					'y' 
			);
			if (! in_array ( $expire_type, $expire_types ) or ! $expire_count) {
				$this->error ( 3018 );
			}
			
			if ($expire_type == 'd') {
				$expire_day = $expire_count;
			} else if ($expire_type == 'w') {
				$expire_day = $expire_count * 7;
			} else if ($expire_type == 'm') {
				$expire_day = $expire_count * 31;
			} else if ($expire_type == 'y') {
				$expire_day = $expire_count * 366;
			}
			
			$now = time ();
			$expire_time = $now - 3600 * 24 * $expire_day;
			
			// $now_date_string = date ( 'Y-m-d H:i:s', $now );
			// $expire_time_string = date ( 'Y-m-d H:i:s', $expire_time );
			
			$map ['create_time'] = array (
					array (
							'EGT',
							$expire_time 
					),
					array (
							'ELT',
							$now 
					) 
			);
		}
		$options = array (
				"alias" => "sl",
				"where" => $map,
				"field" => "sl.*, FROM_UNIXTIME(create_time) as add_time",
				"order" => "id desc" 
		);
		
		$_GET ["p"] = $page;
		$model = M ( 'score_log' );
		$total = $model->where ( $options ['where'] )->count ();
		
		$pageCount = ceil ( $total / $this->pageSize );
		if ($pageCount < $_GET ['p']) {
			$_GET ['p'] = $pageCount;
		}
		
		$pageObj = new \Think\Page ( $total, $this->pageSize );
		
		$options ['limit'] = $pageObj->firstRow . ',' . $pageObj->listRows;
		
		$model->setProperty ( 'options', $options );
		
		$row = $model->select ();
		
		int_to_string ( $row, array (
				'type' => array (
						'coin' => '获得金币',
						'_coin' => '消耗金币'
				)
		) );
		// if (IsDebug()) {
		// echo $model->getLastSql();
		// }
		
		$coin = M ( 'member' )->getFieldById ( $uid, 'coin' );
		$data = array (
				'list' => $row,
				'count' => $total,
				'now_coin' => $coin,
				'next_page' => null 
		);
		if ($pageCount > 1 && $page < $pageCount) {
			$data ['next_page'] = $this->url ( '/v1/account/coin_recording/' . $expire_string . '/' . ++ $page );
		}
		$this->success ( $data );
	}
}