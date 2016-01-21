<?php
namespace Api\Controller;

class AboutController extends ApiBaseController {

	public function updata_version_get() {
		$info = D('AppUpdate')->get_new_update();
		$info = $info[0];
		$this->success($info);
	}

}