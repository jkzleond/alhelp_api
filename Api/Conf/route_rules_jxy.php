<?php
return array(
	'v1/security' => 'MeSetting/security', //账户与安全页面
	'v1/account/paypassword' => function () {
		if (I('server.REQUEST_METHOD') == 'POST') {
			R('Account/paypassword_post');
		} elseif (I('server.REQUEST_METHOD') == 'PUT') {
			R('Account/paypassword_put');
		} else {
			send_http_status(404);
		}

	},
	'v1/account/get_banklist' => 'Account/get_banklist', //获取银行列表
	'v1/account/set_bankcard' => 'Account/set_bankcard', //获取银行列表
	'v1/about/updata_version' => 'About/updata_version', //获取版本更新
	'v1/test' => 'UserProfile/user', //获取群聊用户
);