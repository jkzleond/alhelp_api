<?php
namespace Api\Model;
use Think\Model;

class Account2Model extends BaseModel {

	protected $tableName = 'payment_record';

	// 消费列表
	public function account_list($uid, $type, $expire_string = '') {

		switch ($type) {
			case 'all':
				$condition = array('_string' => "from_member_id = {$uid} or to_member_id = {$uid}");
				break;
			case 'pay_in':
				$condition = array('to_member_id' => $uid);
				break;

			case 'pay_out':
				$condition = array('from_member_id' => $uid);
				break;

			case 'income_in':
				$condition = array('to_member_id' => $uid, 'income_type' => 1);
				break;

			case 'income_out':
				$condition = array('from_member_id' => $uid, 'income_type' => 0);
				break;
		}

		if ($expire_string) {
			$expire_count = intval(substr($expire_string, 0, -1));
			$expire_type  = substr($expire_string, -1);
			$expire_types = array('d', 'w', 'm', 'y', 'c');
			if (!in_array($expire_type, $expire_types) or !$expire_count) {
				return 3018;
			}

			if ($expire_type == 'd') {
				$expire_day = $expire_count;
			} else if ($expire_type == 'w') {
				$expire_day = $expire_count * 7;
			} else if ($expire_type == 'm') {
				$expire_day = $expire_count * 31;
			} else if ($expire_type == 'y') {
				$expire_day = $expire_count * 366;
			} else if ($expire_type == 'c') {
				$expire_day = $expire_count * 366 * 100;
			}
			$now         = time();
			$expire_time = $now - 3600 * 24 * $expire_day;

			$now_date_string    = date('Y-m-d H:i:s', $now);
			$expire_time_string = date('Y-m-d H:i:s', $expire_time);

			$condition['add_time'] = array(array('EGT', $expire_time_string), array('ELT', $now_date_string));
		}

		$data = $this->where($condition)->select();
		return $data;
	}

	public function get_cash($uid, $card_id, $balance, $address) {
		$payment_number = date("YmdHis") . $uid;
		$this_user      = M('member')->where(array('id' => $uid))->find();
		$admin_user     = M('member')->where(array('id' => 1))->find();

		if (intval($this_user['balance']) < intval($balance)) {
			return 3020;
		}
		if (!$this_user) {
			return 3019;
		}
		$model = M();
		$model->startTrans();
		$is_cash = M('cash')->data(array(
			'serial_number'  => $payment_number,
			'card_id'        => $card_id,
			'balance'        => $balance,
			'member_id'      => $uid,
			'type'           => 1,
			'address'        => $address,
			'add_time'       => date('Y-m-d H:i:s'),
			'balance_now'    => $this_user['balance'] - $balance,
			'balance_frozen' => $this_user['balance_frozen'],
		))->add();

		$is_decrease  = M('member')->where(array('id' => $uid))->save(array('balance' => $this_user['balance'] - $balance));
		$is_increase  = M('member')->where(array('id' => 1))->save(array('balance' => $admin_user['balance'] + $balance));
		$json_content = json_encode(array(
			'content' => urlencode("平台申请提现！"),
			'remark'  => urlencode("提现订单号" . $payment_number),
			'info'    => array(
				'id' => $is_cash,
			),
		));
		$a          = M('payment_record');
		$is_payment = $a->data(array(
			'type'           => 1,
			'from_member_id' => $uid,
			'to_member_id'   => 1,
			'table_name'     => 'cash',
			'table_id'       => $is_cash,
			'payment_number' => $payment_number,
			'title'          => '平台申请提现！',
			'field'          => 'balance',
			'price'          => $balance,
			'paid'           => 1,
			'paid_time'      => date('Y-m-d H:i:s'),
			'add_time'       => date('Y-m-d H:i:s'),
			'json_content'   => $json_content,
		))->add();
		if ($is_cash && $is_decrease && $is_increase && $is_payment) {
			$model->commit();
			return true;
		} else {
			dump($is_payment);
			$model->rollback();
			return false;
		}

	}

}