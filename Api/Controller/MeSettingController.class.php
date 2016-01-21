<?php
namespace Api\Controller;

class MeSettingController extends ApiBaseController {

	protected $is_check_token = true;

	public function security_get() {

		$map = array('id' => $this->uid);
		$info = M('Member')->field('id,phone,phone_verified,email,email_verified,qq,status')->where($map)->find();
		$this->success($info);
	}

}