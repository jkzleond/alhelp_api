<?php
namespace Api\Controller;

class AccountController extends ApiBaseController {

	protected $is_check_token = true;

	public function paypassword_post() {
		$info = D('User')->chk_paypwd($this->uid);
		if ($info['paypassword'] != '' && $info['paypassword'] != Null) {
			$this->error(2007, '支付密码已存在');
		}

		$paypassword = $this->get_request_data('paypassword');
		if (!$paypassword) {
			$this->error(2006, '设置支付密码不能为空');
		}
		if (!preg_match("/((?=.*\d)(?=.*\D)|(?=.*[a-zA-Z])(?=.*[^a-zA-Z]))^.{8,16}$/", $paypassword)) {
			$this->error(2008);
		}

		$arr['paypassword'] = md5(md5($paypassword));
		if (D('User')->set_paypwd($this->uid, $arr)) {
			$this->success();
		}

	}

	public function paypassword_put() {
		$info = D('User')->chk_paypwd($this->uid);
		if ($info['paypassword'] != '' && $info['paypassword'] != Null) {
			$this->error(2007, '支付密码已存在');
		}

		$paypassword = $this->get_request_data('paypassword');
		if (!$paypassword) {
			$this->error(2006, '设置支付密码不能为空');
		}
		if (!preg_match("/((?=.*\d)(?=.*\D)|(?=.*[a-zA-Z])(?=.*[^a-zA-Z]))^.{8,16}$/", $paypassword)) {
			$this->error(2008);
		}

		$arr['paypassword'] = md5(md5($paypassword));
		if (D('User')->set_paypwd($this->uid, $arr)) {
			$this->success();
		}

	}

	public function get_banklist_get() {
		$banklist = C('BANK_LIST');
		foreach ($banklist as $k => $v) {
			$banklist[$k]['id'] = $k;
		}
		$this->success($banklist);
	}

	public function set_bankcard_post() {
		$data = $this->get_request_data();
		// P($data);
		if (empty($data['card_type'])) {
			$this->error(2009, '银行ID不能为空');
		}
		if (empty($data['card_num'])) {
			$this->error(2010, '银行卡号不能为空');
		}
		if (empty($data['card_name'])) {
			$this->error(2011, '开户名不能为空');
		}
		$data['member_id'] = $this->uid;
		$data['add_time'] = date("Y-m-d H:i:s");
		$bankcard = M('bankcard');
		if ($id = $bankcard->add($data)) {
			$arr['status'] = 1;
			$where['id'] = array('neq', $id);
			$where['member_id'] = array('eq', $this->uid);
			$where['card_type'] = array('eq', $data['card_type']);
			$bankcard->where($where)->save($arr);
			$this->success('提交成功，请等待审核');
		}
	}

}