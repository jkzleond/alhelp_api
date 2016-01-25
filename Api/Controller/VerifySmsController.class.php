<?php
namespace Api\Controller;

class VerifySmsController extends ApiBaseController {

	public function verify_code_post() {
		$phone = $this->get_request_data("phone");
		$type = $this->get_request_data("type");

		$types = array(
			"reg" => "注册会员",
			"ser" => "发布服务",
			"unlock" => "激活账号",
		);

		$tk = array_keys($types);

		if (!empty($phone) && in_array($type, $tk)) {
			if ($phone) {
				//删除随机验证码
				$str_obj = new \Org\Util\String();
				$code = $str_obj->randNumber(111111, 999999);

				//验证码插件的时间
				$create_time = time();
				//验证码有效期（20分钟）
				$expires = $create_time + (60 * 20);

				//存入session
				session('[start]');
				session("vsms_" . $phone, array("code" => $code, "expires" => $expires));

				//获取session id
				$sign_key = session_id();

				//构造输出数据
				$data = array(
					"code" => $code, //调试时输出
					"signKey" => $sign_key,
					"create_time" => $create_time,
					"expires_at" => $expires,
				);

				//移除开启session后那些无用的header
				header_remove("Set-Cookie");
				header_remove("Expires");
				header_remove("Pragma");
				header_remove("Cache-Control");
				header_remove("Transfer-Encoding");

				$SmsSend = ahlib('AhSmsSend');
				$smsArr[$phone] = array('%action%' => $types[$type], '%Code%' => $code);
				if ($SmsSend->sendBy388($smsArr)) {
					$this->success($data);
				} else {
					$this->error('1006');
				}

			}
		}

		$this->error(1001);
	}

	public static function verify_check($verify) {

		if (is_array($verify)) {
			if (self::check_body_fields($verify, array("phone", "code", "signKey"))) {

				$phone = $verify['phone'];
				$code = $verify['code'];
				$sign_key = $verify['signKey'];

				//启动session
				session_id($sign_key);
				session("[start]");
				//session变量名
				$sess_name = "vsms_" . $phone;
				//读取session
				$vsms = session($sess_name);

				if (is_array($vsms)) {
					if (session('vsms_err_num') < 3 && $code == $vsms['code'] && time() <= $vsms['expires']) {
						session('[destroy]');
						//验证成功
						return true;
					} else {
						session('vsms_err_num', (session('vsms_err_num') + 1));
					}
				}

				//验证失败
				return 1030;
			}
		}

		return 1001;
	}
}