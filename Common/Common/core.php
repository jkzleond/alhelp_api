<?php
function get_phone_preg() {
	return '^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}$|17[0-9]{9}$';
}

function GetSmallAvatar($uid) {
	$member = M("member")
		->alias("m")
		->where(array("m.id" => $uid))
		->join("left join __MEMBER_BIND__ mb on mb.uid = m.id and mb.type='weixin'")
		->field("m.avatar3,mb.info as weixin")
		->find();

	if ($member) {

		if ($member['avatar']) {
			return GetImage($member['avatar3']);
		}
		if ($member['weixin']) {
			$array = json_decode($member['weixin'], true);
			if (!empty($array) && is_array($array) && isset($array['headimgurl']) && 0 != strlen($array['headimgurl'])) {
				return $array['headimgurl'];
			}
		}

		$_uid = sprintf("%09d", $uid);

		$dir1 = substr($_uid, 0, 3);
		$dir2 = substr($_uid, 3, 2);
		$dir3 = substr($_uid, 5, 2);
		$dir4 = substr($_uid, 7, 2);
		$home = $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4;

		$path = 'Ucenter/data/avatar/' . $home . '_avatar_small.jpg';
		if (file_exists($path)) {
			$avatar = MoveImage($path);
			M("member")->where("id=$uid")->save(array("avatar3" => $avatar));
			return GetImage($avatar);
		}
	}
	return 'http://www.alhelp.net/Ucenter/images/noavatar_small.gif';
}

//获取手机验证码
function get_phone_code($phone, $where) {

	$ip = $_SERVER['REMOTE_ADDR'];

	//$where = I ( 'where' ); //在哪里要求发送手机验证码,(找回密码|注册)

	$result = preg_match("/" . get_phone_preg() . "/", $phone, $m);
	if (!$result) {
		return array(
			'status' => 0,
			'msg' => '请填写正确手机号',
		);
	}

	if ('forget' == $where) //这个要最先判断
	{
		$res = get_info('member', array(
			'phone' => $phone,
		));
		if (!$res) {
			return (array(
				'status' => 0,
				'msg' => '此手机号没有注册',
			));
		}
	} else if ('register' == $where) {
		if ('18687456146' == $phone) {

		} else {
			$res = get_info('member', array(
				'phone' => $phone,
			));
			if ($res) {
				return (array(
					'status' => 0,
					'msg' => '此手机号已经注册过了',
				));
			}
		}

	} else if ('modify_phone' == $where) {
		if (GetLoginMember()->phone == $phone) {
			return array(
				'status' => 1,
				'msg' => '您要修改的手机号和您现在的手机号相同',
			);
		}

	}

	$model = M('log_sms');

	$count = $model->where("phone='%s' AND day='%s'", $phone, strtotime(date('Y-m-d')))->count();
	if (3 <= $count) {
		if ('18687456146' == $phone) {

		} else {
			return (array(
				'status' => 0,
				'msg' => '这个手机号今天已经发送三次了,请明天再来',
			));
		}

	}
	$sms = $model->where("phone='%s'", $phone)->order('time DESC')->find();

	$second = time() - $sms['time'];
	if ($second < 120) {
		if ('18687456146' == $phone) {

		} else {
			return (array(
				'status' => 0,
				'msg' => '还 要等待 ' . (120 - $second) . '秒才能重发信息',
				'second' => 120 - $second,
			));
		}

	}

	//删除随机验证码
	$str_obj = new \Org\Util\String();
	$code = $str_obj->randNumber(111111, 999999);

	$content = "验证码【" . $code . "】";

	$_SESSION[$phone] = $code;
	$_SESSION[$phone . 'time'] = time();

	if (IsDebug()) {
		if ('18687456146' == $phone) {
			return array(
				'status' => 1,
				'msg' => '信息已发送(测试中,实际没有发: ' . $code . ')',
			);

		} else {

		}

	}
	include_once "../AhLib/AhSmsSend.class.php";
	$send = new AhSmsSend();
	$smsArr[$phone] = array('%action%' => $types[$type], '%Code%' => $code);
	$response = $send->sendBy388($smsArr);

	return array(
		'status' => 1,
		'msg' => '信息已发送',
	);
}

function rand_password($len = 8) {
	$str = '';

	$chars = '0123456789';

	// 中文随机字

	for ($i = 0; $i < $len; $i++) {
		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	}

	return $str;
}
function rand_string($len = 8, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1:
			$chars = str_repeat('0123456789', 3);
			break;
		case 2:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3:
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default:
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) {
		//位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
	}
	if ($type != 4) {
		$chars = str_shuffle($chars);
		$str = substr($chars, 0, $len);
	} else {
		// 中文随机字
		for ($i = 0; $i < $len; $i++) {
			$str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
		}
	}
	return $str;
}

function addPic($images, $table, $table_id, $uid = null) {
	$picArr = explode(';', $images);

	foreach ($picArr as $sha1) {
		if (40 == strlen($sha1)) {
			$uid = $uid ? $uid : session("member_id");
			$model = M("attachments");
			$values["member_id"] = $uid;
			$values["table"] = $table;
			$values["table_id"] = $table_id;
			$values["add_time"] = date("Y-m-d H:i:s");
			$values["sha1"] = $sha1;
			$model->add($values);
		}

	}

}
function GetLogin() {
	return GetLoginMember();
}

function GetLoginMember() {

	$member = session('member');
	if ($member) {
		return $member;
	} else {
		return NULL;
	}
}

function GetMiddleAvatar($uid) {
	$member = M("member")
		->alias("m")
		->where(array("m.id" => $uid))
		->join("left join __MEMBER_BIND__ mb on mb.uid = m.id and mb.type='weixin'")
		->field("m.avatar3,mb.info as weixin")
		->find();

	if ($member) {
		if ($member['avatar2']) {
			return GetImage($member['avatar2']);
		}
		if ($member['weixin']) {
			$array = json_decode($member['weixin'], true);
			if (!empty($array) && is_array($array) && isset($array['headimgurl']) && 0 != strlen($array['headimgurl'])) {
				return $array['headimgurl'];
			}
		}

		$_uid = sprintf("%09d", $uid);

		$dir1 = substr($_uid, 0, 3);
		$dir2 = substr($_uid, 3, 2);
		$dir3 = substr($_uid, 5, 2);
		$dir4 = substr($_uid, 7, 2);
		$home = $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4;

		$path = 'Ucenter/data/avatar/' . $home . '_avatar_middle.jpg';
		if (file_exists($path)) {
			$avatar = MoveImage($path);
			M("member")->where("id=$uid")->save(array("avatar2" => $avatar));
			return GetImage($avatar);
		}
	}

	return 'http://www.alhelp.net/Ucenter/images/noavatar_middle.gif';
}
function GetBigAvatar($uid) {
	$member = M("member")
		->alias("m")
		->where(array("m.id" => $uid))
		->join("left join __MEMBER_BIND__ mb on mb.uid = m.id and mb.type='weixin'")
		->field("m.avatar3,mb.info as weixin")
		->find();

	if ($member) {
		if ($member['avatar1']) {
			return GetImage($member['avatar1']);
		}
		if ($member['weixin']) {
			$array = json_decode($member['weixin'], true);
			if (!empty($array) && is_array($array) && isset($array['headimgurl']) && 0 != strlen($array['headimgurl'])) {
				return $array['headimgurl'];
			}
		}

		$_uid = sprintf("%09d", $uid);

		$dir1 = substr($_uid, 0, 3);
		$dir2 = substr($_uid, 3, 2);
		$dir3 = substr($_uid, 5, 2);
		$dir4 = substr($_uid, 7, 2);
		$home = $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4;

		$path = 'Ucenter/data/avatar/' . $home . '_avatar_big.jpg';
		if (file_exists($path)) {
			$avatar = MoveImage($path);
			M("member")->where("id=$uid")->save(array("avatar1" => $avatar));
			return GetImage($avatar);
		}
	}

	return 'http://www.alhelp.net/ucenter/images/noavatar_big.gif';
}
function subtext($text, $length) {
	if (mb_strlen($text, 'utf8') > $length) {
		return mb_substr($text, 0, $length, 'utf8') . '...';
	}

	return $text;
}
function cookieLogin() {
	$uid = cookie('next_direct_login'); //在登陆的时候设置的cookie

	if ($uid) {
		$info = get_info('member', array(
			'id' => $uid,
			'status' => 1,
		));
		if ($info) {
			do_login($uid);
			return true;
		}

	}

	return false;
}

function cookie_login() {

	if (!GetLogin() && cookie('next_direct_login')) {
		$uid = $_COOKIE['sr_admin_next_direct_login'];
		do_login($uid);
	}

}

function do_logout() {
	cookie('next_direct_login', null);
	session('info', null);
	session('bindArr', null);
	session('member', null);
}

//登陆网站
function do_login($uid/*用户ID*/) {
	cookie('next_direct_login', $uid, 30 * 3600 * 24);

	$member = D('User/Member');

	$info = $member->_getById($uid);

	// $info = \Member::model()->findbypk($uid);

	// $member = $info;
	// $member->forget_code = '';
	// $member->last_login_time = date("Y-m-d H:i:s");
	// $member->last_login_ip = get_client_ip();
	if (!IsDebug()) {
		$member->save(array(
			'id' => $uid,
			'last_login_time' => date('Y-m-d H:i:s'),
			'last_login_ip' => get_client_ip(),
		));

		// $member->save();
	}

	$info['role'] = I('role', 1);

	/*
		$info ['group_admin_count'] = M ( 'school' )->where ( array ( //该用户是多少个圈子的圈主
		'group_member_id' => $info ['id']
		) )->count ();
	*/

	session('member', $member);
	session('member_id', $uid);
	session('info', $info);

	//update_data ( $this->table );
	//登入送积分
	$param = array(
		'uid' => $info['id'],
		'tablename' => 'member',
		'tableid' => $info['id'],
		'uname' => $info['nickname'],
		'action' => MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME,
		'ruleId' => 7,
		'field' => 'score',
		'isLimit' => true,
		'isdate' => true,
	);
	if (!IsDebug()) {
		tag('setScore', $param);
	}

	after_login();
}

/**
 *
 * 登陆之后
 * @param unknown_type $info
 */
function after_login() {
	$info = GetLogin();
	$dlist = query_sql('', "select order_id,max(refund.status) as m_status from refund left join orders on orders.id=refund.order_id where orders.status=5 and (orders.from_member_id=" . $info['id'] . " or orders.to_member_id=" . $info['id'] . ") and UNIX_TIMESTAMP(apply_time)<" . (time() - 3 * 24 * 3600) . "  group by order_id having m_status<2");

	//	$criteria = new CDbCriteria ();
	//	$criteria->alias = 'refund';
	//	$criteria->with = array (
	//		'orders'
	//	);
	//
	//	$criteria->compare ( 'orders.' . 'status', 5/*已申请退款*/ );
	//	$dlist = Refund::model ()->findAll ();

	if ($dlist) {
		$ids = '';
		foreach ($dlist as $v) {
			$ids .= $v['order_id'] . ',';
			order_record($v['order_id'], 7, $info['nickname'] . '->不同意申请退款');
		}
		execute_sql('', "update orders set status=7  where status=5 and id in (" . trim($ids, ',') . ")");

	}
	//老师发货后十五天之内学生没有确认收获
	$hlist = query_sql('', "select orders.* from orders where status=2 and (to_member_id =" . $info['id'] . " or from_member_id=" . $info['id'] . ") and UNIX_TIMESTAMP(send_time)<" . (time() - 15 * 24 * 3600));

	$ids = '';
	foreach ($hlist as $k => $v) {
		$ids .= $v['id'] . ',';
		$receiptname = get_info('member', array(
			'id' => $v['to_member_id'],
		));
		$re['info'] = $v;
		$this->extend($v['id'], $re, $receiptname);
	}
	if ($hlist) {
		execute_sql('', "update orders set status=3  where id in (" . trim($ids, ',') . ")");
	}

	//老师同意退款发货后3天之内学生没有发货,默认确认收获
	$slist = query_sql('', "select orders.* from orders where status=6 and (to_member_id =" . $info['id'] . " or from_member_id=" . $info['id'] . ") and id in ( select order_id from refund where refund_status=3  and UNIX_TIMESTAMP(agree_time)<" . (time() - 3 * 24 * 3600) . ")");

	$ids = '';

	foreach ($slist as $k => $v) {
		$ids .= $v['id'] . ',';
		$receiptname = get_info('member', array(
			'id' => $v['to_member_id'],
		));
		$re['info'] = $v;

		$this->extend($v['id'], $re, $receiptname, '老师同意退款3天之内学生没有发货学生默认确认收获');
	}
	if ($slist) {
		execute_sql('', "update orders set status=3  where id in (" . trim($ids, ',') . ")");
	}

	//学生退款发货后7天之内老师没有确认退款货收货,默认确认收货-7*24*3600
	$rlist = query_sql('', "select orders.* from orders where status=8 and (to_member_id =" . $info['id'] . " or from_member_id=" . $info['id'] . ") and UNIX_TIMESTAMP(send_time)<" . (time() - 7 * 24 * 3600));
	$ids = '';
	foreach ($rlist as $k => $v) {
		$ids .= $v['id'] . ',';
		$receiptname = get_info('member', array(
			'id' => $v['to_member_id'],
		));
		$re['info'] = $v;
		$this->extend_c($v['id'], $re, $receiptname);
	}
	if ($rlist) {
		execute_sql('', "update orders set status=9  where id in (" . trim($ids, ',') . ")");
	}
	//检测学生在7天内不予以答疑时，系统能否自动付款给老师，并默认进入到下一个阶段-7*24*3600

	$learning_periods = query_sql('', "select learning_periods.id,learning_periods.bid_id,learning_periods.member_id,learning_periods.price,learning_periods.title,bid.demand_member_id,bid.service_member_id,bid.service_demand_id from learning_periods left join bid on bid.id=learning_periods.bid_id where learning_periods.status=1 and UNIX_TIMESTAMP(release_time)<" . (time() - 7 * 24 * 3600) . " and (bid.service_member_id=" . $info['id'] . " or bid.demand_member_id=" . $info['id'] . " )");

	foreach ($learning_periods as $k => $v) {
		$learning_period = get_info('learning_periods', array(
			'bid_id' => $v['bid_id'],
			'id' => array(
				'gt',
				$v['id'],
			),
			'status' => 0,
		));

		$re = $info;
		if ($info['id'] == $v['sevice_member_id']) {
			$re = get_info('member', array(
				'id' => $v['demand_member_id'],
			));
		}
		if ($learning_period) {
			$v['status'] = 2;
			$this->extend_record_c($v['bid_id'], $re, $v);
			M('learning_periods')->where('id=' . $v['id'])->save(array(
				'status' => 2,
			));
		} else {
			M('learning_periods')->where('id=' . $v['id'])->save(array(
				'status' => 5,
			));
			M('bid')->where('id=' . $v['bid_id'])->save(array(
				'status' => 8,
			));
			$v['status'] = 5;
			$this->extend_record($v['bid_id'], $re, $v);

		}

	}
	return;
	$cart = get_info('cart', array(
		'member_id' => $info['id'],
	), 'json_content');

	if ($cart) {
		$list = json_decode(urldecode($cart['json_content']), true);
		$this->cart->clear();

		if ($list) {
			foreach ($list as $k => $v) {
				foreach ($v as $kk => $vv) {
					$this->cart->addItem($k, $kk, $vv['name'], $vv['price'], $vv['brand'], $vv['cover'], $vv['num'], $vv['shipping']);
				}
			}
		} else {
			//$this->cart->clear ();
		}
	} else {
		//$this->cart->clear ();
	}

}

function page($array = array('total', 'page_size')) {
	if (is_array($array)) {
		$total = $array['total'];
	} else {

		$total = $array;
	}

	$REQUEST = (array) I('request.');

	if (isset($array['page_size'])) {
		$listRows = $array['page_size'];
	} else {
		if (isset($REQUEST['r'])) {
			$listRows = (int) $REQUEST['r'];
		} else {
			$listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 8;
		}
	}

	$page = new \Think\Page($total, $listRows, $REQUEST);

	$p = $page->show();

	$p = $page->show();
	$data['_page'] = $p ? $p : '';
	$data['_total'] = $total;
	$data['offset'] = $page->firstRow;
	$data['rows'] = $page->listRows;
	$data['l'] = $page->listRows;
	$data['limit'] = $page->firstRow . ',' . $page->listRows;

	if ($data['_total'] <= ($data['offset'] + $data['rows'])) {
		$data['is_more'] = 0;
	} else {
		$data['is_more'] = 1;
	}

	return $data;
}
//获取数据的字段
function getField($table, $o = array()) {

	$model = M($table);

	$fields = $model->getDbFields();

	foreach ($fields as $f) {
		if (isset($o['nopre'])) //不要表前缀
		{
			$r[] = $table . '.' . $f . ' as `' . $f . '`';
		} else {
			$r[] = $table . '.' . $f . ' as `' . $table . '_' . $f . '`';
		}

	}

	return $r;
}

function HasAdmin($uid, $post_id) //判断一个用户是否能对某条说说进行管理
{
	//圈主可对圈子里面的说说进行管理

	//判断方法  member_post.community_id > community.table_id > school.id > school.group_member_id

	$post = M('member_post')->where(array(
		'id' => $post_id,
	))->find();

	if (!$post) {
		if (IsDebug()) {
			echo '//说说不存在';
		}
		return false;
	}

	if (0 != $post['pid']) //是回复
	{
		$post = M('member_post')->where(array(
			'id' => $post['pid'],
		))->find();
	}

	if (0 == $post['community_id']) //说说不属于任何圈子
	{
		if (IsDebug()) {
			echo '//说说不属于任何圈子';
		}
		return false;
	}

	$community = M('community')->where(array(
		'id' => $post['community_id'],
	))->find();

	if (!$community) {
		if (IsDebug()) {
			echo '圈子不存在';
		}
		return false;
	}

	$school = M('school')->where(array(
		'id' => $community['table_id'],
	))->find();

	if (!$school) {
		if (IsDebug()) {
			echo '学校不存在';
		}
		return false;
	}
	if ($uid == $school['group_member_id']) {
		return true;
	} else {
		if (3 == $school['type']) //学院,大学的圈主要可以删除学院的说说
		{

		}
		if (IsDebug()) {
			echo '不是学校的圈主uid: ' . $uid . 'group_member_id: ' . $school['group_member_id'];
		}
		return false;
	}

}

function new_get_result($Model, $map) {
	if (is_string($Model)) {
		$Model = M($Model);
	}
	$str = '$result=$Model->';
	foreach ($map as $key => $val) {
		$str .= $key . '("' . $val . '")->';
	}
	$str = $str . 'select();';
	eval($str);
	session('sql', $Model->getLastSql());
	return $result;
}

/*
 * 获取数据集
 * */
function get_result($Model, $map = array(), $field = '', $order = '', $limit = 0, $group = '', $having = '') {
	if (is_string($Model)) {
		$Model = M($Model);
	}
	if ($limit == 0) {
		$limit = '';
	}
	if (session('cache_name') && session('cache_name') != '') {
		$result = $Model->where($map)->cache(session('cache_name'))->field($field)->order($order)->group($group)->having($having)->limit($limit)->select();
		session('cache_name', null);
	} else {
		$result = $Model->where($map)->field($field)->order($order)->group($group)->having($having)->limit($limit)->select();
	}
	session('sql', $Model->getLastSql());
	return $result;
}

/*
 * 获取单条数据
 * */
function get_info($Model, $map = array(), $field = array()) {
	if (is_string($Model)) {
		$Model = M($Model);
	}

	$result = $Model->where($map)->field($field)->find();
	session('sql', $Model->getLastSql());
	return $result;
}
function debug_put($msg) {
	$debug = debug_backtrace();

	$file = $debug[0]['file'];
	$file = str_replace('\\', '_', $file);
	$file = str_replace(':', '_', $file);
	$name = $file . '_' . $debug[0]['line'];

	$debug = print_r($debug, true);

	$debug = str_replace("\n", "\r\n", $debug);
	$dir = '../debug/';
	if (!file_exists($dir)) {
		mkdir($dir);
	}

	$content = 'member_id:' . GetLoginMember()->id . "\r\n";
	$content .= 'nickname:' . GetLoginMember()->nickname . "\r\n";

	if (!empty($_POST)) {
		$post = print_r($_POST, true);
		$post = str_replace("\n", "\r\n", $post);
		$content .= 'post = ' . $post;
	}

	if (!empty($_GET)) {
		$get = print_r($_GET, true);
		$get = str_replace("\n", "\r\n", $get);
		$content .= 'get = ' . $get;
	}

	if (!empty($_FILES)) {
		$temp = print_r($_FILES, true);
		$temp = str_replace("\n", "\r\n", $temp);
		$content .= 'FILES = ' . $temp;
	}

	$content .= $msg;

	@file_put_contents($dir . $name . '_' . time() . '.txt', $content);
}
/*
 * 添加、修改数据
 * */
function update_data($table, $rules = array(), $map = array(), $no_id = false, $field = array() /*要更新的字段*/) {
	if (empty($rules)) {
		$debug = debug_backtrace();

		$file = $debug[0]['file'];
		$file = str_replace('\\', '_', $file);
		$file = str_replace(':', '_', $file);
		$name = $file . '_' . $debug[0]['line'];

		$debug = print_r($debug, true);

		$debug = str_replace("\n", "\r\n", $debug);
		$dir = '../update_data/';
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		$post = print_r($_POST, true);
		$post = str_replace("\n", "\r\n", $post);

		$get = print_r($_GET, true);
		$get = str_replace("\n", "\r\n", $get);

		$content = 'member_id:' . GetLoginMember()->id . "\r\n";
		$content .= 'nickname:' . GetLoginMember()->nickname . "\r\n";
		$content .= 'HTTP_REFERER = ' . $_SERVER['HTTP_REFERER'] . "\r\n";
		$content .= 'post = ' . $post;
		$content .= 'get = ' . $get;
		$content .= 'debug = ' . $debug;

		@file_put_contents($dir . $name . '.txt', $content);
	}

	$Model = M($table);
	//创建数据对象
	$data = $Model->validate($rules)->create();

	if (!$data) {
		//数据对象创建错误
		return $Model->getError();
	}

	/* 添加或更新数据 */
	if (empty($data['id']) || $no_id) {
		$data['add_time'] = date("Y-m-d H:i:s");
		$res = $Model->add($data);
	} else {
		$data['update_time'] = date("Y-m-d H:i:s");

		if (!empty($map)) {
			$res = $Model->where($map)->save($data);
		} else {
			$res = $Model->save($data);

		}
	}

	session('sql', $Model->getLastSql());
	return $res;
}
//批量添加
/*$table 表名
 * $data 要添加的数据  （二维数组）
 */
function addMore($table, $data) {
	if (empty($table) || empty($data)) {
		return false;
	}
	$Model = M($table);
	return $Model->addAll($data);
}

/*
 * 删除数据
 * */
function delete_data($table, $map = array()) {
	$Model = M($table);
	$res = $Model->where($map)->delete();
	//echo $Model->getLastSql().'<br/>';
	return $res;
}
/*
 * 查询数据的sql操作
 * */
function query_sql($table, $sql) {
	//查询数据的sql操作
	$Model = M($table);
	$result = $Model->query($sql);
	//echo $Model->getLastSql();
	return $result;
}

/*
 * 更新和写入数据的sql操作
 * */
function execute_sql($table, $sql) {
	//更新和写入数据的sql操作
	$Model = M($table);

	$result = $Model->execute($sql);
	session('sql', $Model->getLastSql());
	return $result;
}

/*
 * 获取某个表中的指定字段的结果集
 * $table 表名
 * $where 查询条件 可以一是数组  也可以是字符串  自己定义
 * $str 字段  多个用','隔开
 * $init  true 查询多个 false 查询单个
 */
function get_field($table, $where = array(), $str, $init = true, $group = 'id') {
	if (is_string($table)) {
		$model = M($table);
	} else {
		$model = $table;
	}
	if (empty($str)) {
		$str = 'id';
	}
	if (session('cache_name') && session('cache_name') != '') {
		$res = $model->where($where)->cache(session('cache_name'))->group($group)->getField($str, $init);
		session('cache_name', null);
	} else {
		$res = $model->where($where)->group($group)->getField($str, $init);
	}
	session('sql', $model->getLastsql());
	return $res;
}

/*
 * 更新表单中某个字段数量
 * $table 表名
 * $where 查询条件 可以一是数组  也可以是字符串  自己定义
 * $field 要更新的数量
 * $num   要跟新的值
 * $init  初始化 true 增加  false 减少
 */
function update_num($table, $where = array(), $field, $num = 1, $init = true) {
	$model = M($table);
	if (empty($where) || empty($field)) {
		return false;
	}
	if ($init) {
		return $model->where($where)->setInc($field, $num);
	} else {
		return $model->where($where)->setDec($field, $num);
	}
}

/*
获取汉字首字母
 */
function get_first_char($s0) {
	$fchar = ord(substr($s0, 0, 1));
	if (($fchar >= ord("a") and $fchar <= ord("z")) or ($fchar >= ord("A") and $fchar <= ord("Z"))) {
		return strtoupper(chr($fchar));
	}

	$s = iconv("UTF-8", "GBK", $s0);
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if ($asc >= -20319 and $asc <= -20284) {
		return "A";
	}

	if ($asc >= -20283 and $asc <= -19776) {
		return "B";
	}

	if ($asc >= -19775 and $asc <= -19219) {
		return "C";
	}

	if ($asc >= -19218 and $asc <= -18711) {
		return "D";
	}

	if ($asc >= -18710 and $asc <= -18527) {
		return "E";
	}

	if ($asc >= -18526 and $asc <= -18240) {
		return "F";
	}

	if ($asc >= -18239 and $asc <= -17923) {
		return "G";
	}

	if ($asc >= -17922 and $asc <= -17418) {
		return "H";
	}

	if ($asc >= -17417 and $asc <= -16475) {
		return "J";
	}

	if ($asc >= -16474 and $asc <= -16213) {
		return "K";
	}

	if ($asc >= -16212 and $asc <= -15641) {
		return "L";
	}

	if ($asc >= -15640 and $asc <= -15166) {
		return "M";
	}

	if ($asc >= -15165 and $asc <= -14923) {
		return "N";
	}

	if ($asc >= -14922 and $asc <= -14915) {
		return "O";
	}

	if ($asc >= -14914 and $asc <= -14631) {
		return "P";
	}

	if ($asc >= -14630 and $asc <= -14150) {
		return "Q";
	}

	if ($asc >= -14149 and $asc <= -14091) {
		return "R";
	}

	if ($asc >= -14090 and $asc <= -13319) {
		return "S";
	}

	if ($asc >= -13318 and $asc <= -12839) {
		return "T";
	}

	if ($asc >= -12838 and $asc <= -12557) {
		return "W";
	}

	if ($asc >= -12556 and $asc <= -11848) {
		return "X";
	}

	if ($asc >= -11847 and $asc <= -11056) {
		return "Y";
	}

	if ($asc >= -11055 and $asc <= -10247) {
		return "Z";
	}

	return substr($s0, 0, 1);
}
