<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// OneThink常量定义

const ONETHINK_VERSION = '1.0.131218';
const ONETHINK_ADDON_PATH = './Addons/';
function P($arr) {
	dump($arr, 1, '', 0);
	exit;
}
//根据member_id获取用户等级
function get_level($member_id = 0) {
	$level_id = M('member')->where(array(
		'id' => $member_id,
	))->getField('level');
	return M('member_level')->where(array(
		'id' => $level_id,
	))->getField('title');
}

//radio选中
function radio_checked($name, $value) {
	if ($name == $value) {
		echo "checked = 'checked'";
	}
}
//加载AhLib的类
function ahlib($classname) {

	include "../AhLib/" . $classname . ".class.php";
	// dump(file_exists($classname));
	// echo $classname;die;
	return new $classname();
}
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */
function home_is_login() {
	if (!session('info')) {
		return 0;
	} else {
		return 1;
	}
}

/**
 * 主要定义系统获取语言函数库
 */
function get_lists_language() {
	$map['status'] = 1;
	$language_list = get_result(D('language'), $map, '', 'sort');
	return $language_list;
}

/**
 * 主要定义系统获取语言函数库
 */
function get_lists_currency() {
	$map['status'] = 1;
	$currency_list = get_result(D('currency'), $map, '', 'id');
	return $currency_list;
}

/* ===============================获取图片路径及缩略图===================== */

function get_image_thumb($image, $width, $height, $prefix) {
	if (file_exists($image)) {
		$name = basename($image);
		$folder = str_replace($name, '', $image);
		$new_image = $folder . $prefix . $name;
		image_thumb($image, $width, $height, $new_image);
		return '/' . $new_image;
	} else {
		$config = C('TMPL_PARSE_STRING');
		return $config['__IMG__'] . '/transparent.png';
	}
}

/*
 * 产生php缓存文件
 * folder 文件夹
 * filename 缓存文件名
 * data 数据数组或数据集
 * */

function php_cache_file($folder, $filename, $data, $arr_name = 'data_array') {
	if (!file_exists($folder)) {
		mkdir($folder);
	}
	$filename = $folder . '/' . $filename;
	$content = "\$" . $arr_name . " = " . var_export($data, True) . ';';
	$content = "<?php\n//该文件是系统自动生成的缓存文件，请勿修改\n//创建时间：" . date('Y-m-d H:i:s', time()) . "\n\n" . $content . "\n\n?>";
	$len = file_put_contents($filename, $content);
}

/*
 * 单独上传
 * */

function single_file_upload($picture_ids, $folder, $table, $table_key_field, $table_key_value, $image_field = 'image', $attr = 'picture') {
	if (!file_exists($folder)) {
		mkdir($folder);
	}
	$picture_ids = addslashes($picture_ids);

	if ($picture_ids == '') {
		$picture_ids = '0';
	}

	$attr_info = get_info($attr, array(
		'id' => $picture_ids,
	), array(
		'path',
	));
	$path = ltrim($attr_info['path'], '/');
	$file_name = basename($path);
	$new_path = $folder . '/' . $file_name;
	copy($path, $new_path);
	@unlink($path);
	$_POST = null;
	$_POST[$image_field] = $new_path;
	$_POST[$table_key_field] = $table_key_value;

	update_data($table);
	delete_data($attr, array(
		'id' => $picture_ids,
	));
}

/*
 * 多图上传
 * $picture_ids 临时图片ID
 * $folder 上传目录
 * $table 图片保存数据表
 * $table_key_field 图片保存数据表对应字段名ID
 * $table_key_value 图片保存数据表的ID值
 * $image_field
 * */

function multi_file_upload($picture_ids, $folder, $table, $table_key_field, $table_key_value, $image_field = 'image', $attr = 'picture') {
	if (!file_exists($folder)) {
		mkdir($folder);
	}
	if (is_array($picture_ids)) {
		$picture_ids = implode(',', $picture_ids);
	}
	$picture_ids = addslashes($picture_ids);

	if ($picture_ids == '') {
		$picture_ids = '0';
	}

	$result = get_result($attr, array(
		'id' => array(
			'in',
			$picture_ids,
		),
	), array(
		'path',
	));
	$msg = '';
	foreach ($result as $row) {
		$path = ltrim($row['path'], '/');
		$file_name = basename($path);
		$new_path = $folder . '/' . $file_name;
		copy($path, $new_path);
		@unlink($path);
		$_POST = null;
		$_POST[$image_field] = $new_path;
		$_POST[$table_key_field] = $table_key_value;

		update_data($table);
	}
	delete_data($attr, array(
		'id' => array(
			'in',
			$picture_ids,
		),
	));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',') {
	return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',') {
	return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = base64_encode($data);
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire ? $expire + time() : 0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
	}
	return str_replace(array(
		'+',
		'/',
		'=',
	), array(
		'-',
		'_',
		'',
	), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
	$key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = str_replace(array(
		'-',
		'_',
	), array(
		'+',
		'/',
	), $data);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data = substr($data, 10);

	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array($list)) {
		$refer = $resultSet = array();
		foreach ($list as $i => $data) {
			$refer[$i] = &$data[$field];
		}

		switch ($sortby) {
		case 'asc': // 正向排序
			asort($refer);
			break;
		case 'desc': // 逆向排序
			arsort($refer);
			break;
		case 'nat': // 自然排序
			natcasesort($refer);
			break;
		}
		foreach ($refer as $key => $val) {
			$resultSet[] = &$list[$key];
		}

		return $resultSet;
	}
	return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0, $key = '') {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $k => $data) {
			$refer[$data[$pk]] = &$list[$k];
		}
		foreach ($list as $k => $data) {
			// 判断是否存在parent

			$parentId = $data[$pid];
			if ($root == $parentId) {
				if ($key != '') {
					$tree[$data[$key]] = &$list[$k];
				} else {
					$tree[] = &$list[$k];
				}
			} else {
				if (isset($refer[$parentId])) {
					$parent = &$refer[$parentId];
					if ($key != '') {
						$parent[$child][$data[$key]] = &$list[$k];
					} else {
						$parent[$child][] = &$list[$k];
					}
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array($tree)) {
		$refer = array();
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if (isset($reffer[$child])) {
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby = 'asc');
	}
	return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array(
		'B',
		'KB',
		'MB',
		'GB',
		'TB',
		'PB',
	);
	for ($i = 0; $size >= 1024 && $i < 5; $i++) {
		$size /= 1024;
	}

	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url) {
	cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url() {
	$url = cookie('redirect_url');
	return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array()) {
	\Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name) {
	$class = "Addons\\{$name}\\{$name}Addon";
	return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name) {
	$class = get_addon_class($name);
	if (class_exists($class)) {
		$addon = new $class();
		return $addon->getConfig();
	} else {
		return array();
	}
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()) {
	$url = parse_url($url);
	$case = C('URL_CASE_INSENSITIVE');
	$addons = $case ? parse_name($url['scheme']) : $url['scheme'];
	$controller = $case ? parse_name($url['host']) : $url['host'];
	$action = trim($case ? strtolower($url['path']) : $url['path'], '/');

	/* 解析URL带的参数 */
	if (isset($url['query'])) {
		parse_str($url['query'], $query);
		$param = array_merge($query, $param);
	}

	/* 基础参数 */
	$params = array(
		'_addons' => $addons,
		'_controller' => $controller,
		'_action' => $action,
	);
	$params = array_merge($params, $param); //添加额外参数

	return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	$time = $time === NULL ? NOW_TIME : intval($time);
	return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if (empty($list)) {
		$list = S('sys_active_user_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset($list[$key])) {
		//已缓存，直接使用
		$name = $list[$key];
	} else {
		//调用接口获取用户信息
		$User = new User\Api\UserApi();
		$info = $User->info($uid);
		if ($info && isset($info[1])) {
			$name = $list[$key] = $info[1];
			/* 缓存用户 */
			$count = count($list);
			$max = C('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			S('sys_active_user_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if (empty($list)) {
		$list = S('sys_user_nickname_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset($list[$key])) {
		//已缓存，直接使用
		$name = $list[$key];
	} else {
		//调用接口获取用户信息
		$info = M('Member')->field('nickname')->find($uid);
		if ($info !== false && $info['nickname']) {
			$nickname = $info['nickname'];
			$name = $list[$key] = $nickname;
			/* 缓存用户 */
			$count = count($list);
			$max = C('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			S('sys_user_nickname_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null) {
	static $list;

	/* 非法分类ID */
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = S('sys_category_list');
	}

	/* 获取分类名称 */
	if (!isset($list[$id])) {
		$cate = M('Category')->find($id);
		if (!$cate || 1 != $cate['status']) {
			//不存在分类，或分类被禁用
			return '';
		}
		$list[$id] = $cate;
		S('sys_category_list', $list); //更新缓存
	}
	return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */

function get_category_name($id) {
	return get_category($id, 'name');
}

/* 根据ID获取分类名称 */

function get_category_title($id) {
	return get_category($id, 'title');
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null) {
	static $list;

	/* 非法分类ID */
	if (!(is_numeric($id) || is_null($id))) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = S('DOCUMENT_MODEL_LIST');
	}

	/* 获取模型名称 */
	if (empty($list)) {
		$map = array(
			'status' => 1,
			'extend' => 1,
		);
		$model = M('Model')->where($map)->field(true)->select();
		foreach ($model as $value) {
			$list[$value['id']] = $value;
		}
		S('DOCUMENT_MODEL_LIST', $list); //更新缓存
	}

	/* 根据条件返回数据 */
	if (is_null($id)) {
		return $list;
	} elseif (is_null($field)) {
		return $list[$id];
	} else {
		return $list[$id][$field];
	}
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data) {
	//TODO: 待完善，目前返回原始数据
	return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null) {

	//参数检查
	if (empty($action) || empty($model) || empty($record_id)) {
		return '参数不能为空';
	}
	if (empty($user_id)) {
		$user_id = is_login();
	}

	//查询行为,判断是否执行
	$action_info = M('Action')->getByName($action);
	if ($action_info['status'] != 1) {
		return '该行为被禁用或删除';
	}

	//插入行为日志
	$data['action_id'] = $action_info['id'];
	$data['user_id'] = $user_id;
	$data['action_ip'] = ip2long(get_client_ip());
	$data['model'] = $model;
	$data['record_id'] = $record_id;
	$data['create_time'] = NOW_TIME;

	//解析日志规则,生成日志备注
	if (!empty($action_info['log'])) {
		if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
			$log['user'] = $user_id;
			$log['record'] = $record_id;
			$log['model'] = $model;
			$log['time'] = NOW_TIME;
			$log['data'] = array(
				'user' => $user_id,
				'model' => $model,
				'record' => $record_id,
				'time' => NOW_TIME,
			);
			foreach ($match[1] as $value) {
				$param = explode('|', $value);
				if (isset($param[1])) {
					$replace[] = call_user_func($param[1], $log[$param[0]]);
				} else {
					$replace[] = $log[$param[0]];
				}
			}
			$data['remark'] = str_replace($match[0], $replace, $action_info['log']);
		} else {
			$data['remark'] = $action_info['log'];
		}
	} else {
		//未定义日志规则，记录操作url
		$data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
	}

	M('ActionLog')->add($data);

	if (!empty($action_info['rule'])) {
		//解析行为
		$rules = parse_action($action, $user_id);

		//执行行为
		$res = execute_action($rules, $action_info['id'], $user_id);
	}
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 * field->要操作的字段；
 * condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 * rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 * cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 * max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self) {
	if (empty($action)) {
		return false;
	}

	//参数支持id或者name
	if (is_numeric($action)) {
		$map = array(
			'id' => $action,
		);
	} else {
		$map = array(
			'name' => $action,
		);
	}

	//查询行为信息
	$info = M('Action')->where($map)->find();
	if (!$info || $info['status'] != 1) {
		return false;
	}

	//解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	$rules = $info['rule'];
	$rules = str_replace('{$self}', $self, $rules);
	$rules = explode(';', $rules);
	$return = array();
	foreach ($rules as $key => &$rule) {
		$rule = explode('|', $rule);
		foreach ($rule as $k => $fields) {
			$field = empty($fields) ? array() : explode(':', $fields);
			if (!empty($field)) {
				$return[$key][$field[0]] = $field[1];
			}
		}
		//cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
		if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
			unset($return[$key]['cycle'], $return[$key]['max']);
		}
	}

	return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null) {
	if (!$rules || empty($action_id) || empty($user_id)) {
		return false;
	}

	$return = true;
	foreach ($rules as $rule) {

		//检查执行周期
		$map = array(
			'action_id' => $action_id,
			'user_id' => $user_id,
		);
		$map['create_time'] = array(
			'gt',
			NOW_TIME - intval($rule['cycle']) * 3600,
		);
		$exec_count = M('ActionLog')->where($map)->count();
		if ($exec_count > $rule['max']) {
			continue;
		}

		//执行数据库操作
		$Model = M(ucfirst($rule['table']));
		$field = $rule['field'];
		$res = $Model->where($rule['condition'])->setField($field, array(
			'exp',
			$rule['rule'],
		));

		if (!$res) {
			$return = false;
		}
	}
	return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files) {
	foreach ($files as $key => $value) {
		if (substr($value, -1) == '/') {
			mkdir($value);
		} else {
			@file_put_contents($value, '');
		}
	}
}

if (!function_exists('array_column')) {

	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array();
		if (null === $indexKey) {
			if (null === $columnKey) {
				$result = array_values($input);
			} else {
				foreach ($input as $row) {
					$result[] = $row[$columnKey];
				}
			}
		} else {
			if (null === $columnKey) {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row;
				}
			} else {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row[$columnKey];
				}
			}
		}
		return $result;
	}

}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null) {
	if (empty($model_id)) {
		return false;
	}
	$Model = M('Model');
	$name = '';
	$info = $Model->getById($model_id);
	if ($info['extend'] != 0) {
		$name = $Model->getFieldById($info['extend'], 'name') . '_';
	}
	$name .= $info['name'];
	return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true) {
	static $list;

	/* 非法ID */
	if (empty($model_id) || !is_numeric($model_id)) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = S('attribute_list');
	}

	/* 获取属性 */
	if (!isset($list[$model_id])) {
		$map = array(
			'model_id' => $model_id,
		);
		$extend = M('Model')->getFieldById($model_id, 'extend');

		if ($extend) {
			$map = array(
				'model_id' => array(
					"in",
					array(
						$model_id,
						$extend,
					),
				),
			);
		}
		$info = M('Attribute')->where($map)->select();
		$list[$model_id] = $info;

		//S('attribute_list', $list); //更新缓存
	}

	$attr = array();
	foreach ($list[$model_id] as $value) {
		$attr[$value['id']] = $value;
	}

	if ($group) {
		$sort = M('Model')->getFieldById($model_id, 'field_sort');

		if (empty($sort)) {
			//未排序
			$group = array(
				1 => array_merge($attr),
			);
		} else {
			$group = json_decode($sort, true);

			$keys = array_keys($group);
			foreach ($group as &$value) {
				foreach ($value as $key => $val) {
					$value[$key] = $attr[$val];
					unset($attr[$val]);
				}
			}

			if (!empty($attr)) {
				$group[$keys[0]] = array_merge($group[$keys[0]], $attr);
			}
		}
		$attr = $group;
	}
	return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name, $vars = array()) {
	$array = explode('/', $name);
	$method = array_pop($array);
	$classname = array_pop($array);
	$module = $array ? array_pop($array) : 'Common';
	$callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
	if (is_string($vars)) {
		parse_str($vars, $vars);
	}
	return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null) {
	if (empty($value) || empty($table)) {
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info = M(ucfirst($table))->where($map);
	if (empty($field)) {
		$info = $info->field(true)->find();
	} else {
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url') {
	$link = '';
	if (empty($link_id)) {
		return $link;
	}
	$link = M('Url')->getById($link_id);
	if (empty($field)) {
		return $link;
	} else {
		return $link[$field];
	}
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null) {
	if (empty($cover_id)) {
		return false;
	}
	$picture = M('Picture')->where(array(
		'status' => 1,
	))->getById($cover_id);
	return empty($field) ? $picture : $picture[$field];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0) {
	if (empty($pos) || empty($contain)) {
		return false;
	}

	//将两个参数进行按位与运算，不为0则表示$contain属于$pos
	$res = $pos & $contain;
	if ($res !== 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */
function get_stemma($pids, Model &$model, $field = 'id') {
	$collection = array();

	//非空判断
	if (empty($pids)) {
		return $collection;
	}

	if (is_array($pids)) {
		$pids = trim(implode(',', $pids), ',');
	}
	$result = $model->field($field)->where(array(
		'pid' => array(
			'IN',
			(string) $pids,
		),
	))->select();
	$child_ids = array_column((array) $result, 'id');

	while (!empty($child_ids)) {
		$collection = array_merge($collection, $result);
		$result = $model->field($field)->where(array(
			'pid' => array(
				'IN',
				$child_ids,
			),
		))->select();
		$child_ids = array_column((array) $result, 'id');
	}
	return $collection;
}

/*
$table 更新删除操作针对的表
$key_id 操作表对应ID的字段数据表列名
$key_id_value 针对数据的ID值
$filed 操作表对应的字段
$type 修改类型：（删除delete）、（置空字段update）
 */

function fileDelete($table, $key_id, $key_id_value, $filed = 'image', $type = "update") {
	/*
		用法示例：
		删case表图片，参数为('case','id','1');
		删case_image表,参数为('case_image','case_id','1','image','delete');
	*/
	$map = array(
		$key_id => $key_id_value,
	);
	$result = get_result($table, $map);
	foreach ($result as $row) {
		if ($row[$filed]) {
			@unlink($row[$filed]);
		}
	}
	if ($type == "delete") {
		delete_data($table, $map);
	} else if ($type == "update") {
		$_POST[$key_id] = $key_id_value;
		$_POST[$filed] = '';
		update_data($table, $map);
	}
}

/*
 * 缩略图
 * */

function image_thumb($img, $width, $height, $new_img) {
	if (!file_exists($new_img) && file_exists($img)) {
		$image = new \Think\Image();
		$image->open($img);
		// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
		$image->thumb($width, $height)->save($new_img);
	}
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null) {
	$config = C('THINK_EMAIL');
	vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件

	$mail = new PHPMailer(); //PHPMailer对象
	$mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	$mail->IsSMTP(); // 设定使用SMTP服务
	$mail->SMTPDebug = 0;
	// $mail->SMTPDebug = 1;                     // 关闭SMTP调试功能
	// 1 = errors and messages
	// 2 = messages only
	$mail->SMTPAuth = true; // 启用 SMTP 验证功能
	//$mail->SMTPAuth = false;                  // 启用 SMTP 验证功能 如果为false则不用填写用户名密码也可以发送Email
	//$mail->SMTPSecure = 'ssl';                 // 使用安全协议
	$mail->Host = $config['SMTP_HOST']; // SMTP 服务器
	$mail->Port = $config['SMTP_PORT']; // SMTP服务器的端口号
	$mail->Username = $config['SMTP_USER']; // SMTP服务器用户名
	$mail->Password = $config['SMTP_PASS']; // SMTP服务器密码

	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);

	$mail->FromName = $config['FROM_NAME'];
	$mail->From = $config['FROM_EMAIL'];
	/*
		//添加邮件回复
		$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
		$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
		$mail->AddReplyTo($replyEmail, $replyName);
	*/

	$mail->Subject = $subject;
	$mail->MsgHTML($body);

	$mail->AddAddress($to, $name);

	/*
		if(is_array($attachment)){ // 添加附件
		foreach ($attachment as $file){
		is_file($file) && $mail->AddAttachment($file);
		}
		}
	*/
	$return_info = $mail->Send() ? true : $mail->ErrorInfo;
	//echo $return_info;
	return $return_info;
}

/**
 * 判断数组是否为空
 * @author MyMelody <1753290024@qq.com>
 */
function checkArray($array) {
	foreach ($array as $value) {
		if (is_array($value)) {
			if (count($value)) {
				if (!checkArray($value)) {
					return false;
				}
			}
		} else {
			$value = trim($value);
			if (!empty($value)) {
				return false;
			}
		}
		$i++;
	}
	return true;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

//二维数组去掉重复值
function array_unique_fb($array2D) {
	foreach ($array2D as $v) {
		$v = join(",", $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
		$temp[] = $v;
	}
	$temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
	foreach ($temp as $k => $v) {
		$temp[$k] = explode(",", $v); //再将拆开的数组重新组装
	}
	return $temp;
}

/* ================================创建缓存文件开始================================= */

/**
 * 当文件发生改变时，删除缓存文件
 */

/**
 * 创建网站信息缓存文件
 */
function create_site_cache() {
	$info = get_info('config', array(
		'id' => 1,
	));
	php_cache_file('Cache/site', 'site.dat', $info, '_SITE');
}

function uploadFile($id, $path = '', $subpath = '') {
	$path = (empty($path)) ? C('DOWNLOAD_UPLOAD.rootPath') : $path;
	//设置文件保存路径
	C('DOWNLOAD_UPLOAD.rootPath', $path);
	C('DOWNLOAD_UPLOAD.subName', $subpath . $id);
	/* 调用文件上传组件上传文件 */
	$File = new \Common\Model\FileModel();
	$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
	$info = $File->upload($_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG"));
	if (!empty($info)) {
		$return['status'] = 1;
		$return = array_merge($info['filename'], $return);
	} else {
		$return['status'] = 0;
		$return['info'] = $File->getError();
	}

	return $return;
}

//图片上传
function uploadPicture($id, $path = '', $subpath = '') {
	$path = (empty($path)) ? C('PICTURE_UPLOAD.rootPath') : $path;
	/* 返回标准数据 */
	$return = array(
		'status' => 1,
		'info' => '上传成功',
		'data' => '',
	);

	//设置文件保存路径
	C('PICTURE_UPLOAD.rootPath', $path);
	C('PICTURE_UPLOAD.subName', $subpath . '/' . $id);
	/* 调用文件上传组件上传文件 */
	$model = new \Common\Model\PictureModel();
	$pic_driver = C('PICTURE_UPLOAD_DRIVER');
	$info = $model->upload($_FILES, C('PICTURE_UPLOAD'), C('PICTURE_UPLOAD_DRIVER'), C("UPLOAD_{$pic_driver}_CONFIG")); //TODO:上传到远程服务器

	$temp = print_r($info, true);
	$temp = str_replace("\n", "\r\n", $temp);
	$content = 'info = ' . $temp;

	debug_put($content);

	/* 记录图片信息 */

	if ($info) {
		$return['status'] = 1;
		$return = array_merge($info['img'], $return);
	} else {
		$return['status'] = 0;
		$return['info'] = $model->getError();
	}

	/* 返回JSON数据 */
	return $return;
}

/*
 * 获取地区模板
 * */

function get_area_template($province, $city, $area) {
	if ($province == 0) {
		$province = '';
	}
	if ($city == 0) {
		$city = '';
	}
	if ($area == 0) {
		$area = '';
	}
	//$Model=M('area');
	session('cache_name', 'area');
	$result = get_result('area', array(
		'status' => 1,
	), 'id,pid,title');
	//$result=$Model->where(array('status'=>1))->field('id,pid,title')->select();
	$html = "<div style='display:none'><select id='default_option'>";
	foreach ($result as $row) {
		$html .= "<option value='" . $row['id'] . "' pid='" . $row['pid'] . "'>" . $row['title'] . "</option>";
	}
	$html .= '</select></div>';
	$html .= "<script>
				$('#default_option option[pid=0]').clone().appendTo($('#province'));
				$('#province').change(function(){
					var pid=$(this).val();
					$('#city').html('<option value=\"\">城市</option>');
					$('#default_option option[pid='+pid+']').clone().appendTo($('#city'));
				});
				$('#city').change(function(){
					var pid=$(this).val();
					$('#area').html('<option value=\"\">区县</option>');
					$('#default_option option[pid='+pid+']').clone().appendTo($('#area'));
				});

				$('#province').val('" . $province . "').change();
				$('#city').val('" . $city . "').change();
				$('#area').val('" . $area . "');
			</script>";
	return $html;
}

/*
 * 获取地区数组结果集
 * */

function get_area_arr() {
	$Model = M('area');
	session('cache_name', 'area');
	$result = get_result('area', array(
		'status' => 1,
	), 'id,pid,title');
	//$result=$Model->where(array('status'=>1))->field('id,pid,title')->select();
	$arr = array();
	foreach ($result as $row) {
		$arr[$row['id']] = $row['title'];
	}
	return $arr;
}

/*
 * 获取省市区显示格式
 * */

function get_area_format($province, $city, $area) {
	$str = '';
	if ($province) {
		$str .= $province;
		if ($city) {
			$str .= '-';
		}
	}
	if ($city) {
		$str .= $city;
		if ($area) {
			$str .= '-';
		}
	}
	if ($area) {
		$str .= $area;
	}
	return $str;
}

/*
 * 获取订单号码
 * */

function get_order_number() {
	$order_number = microtime();
	$order_number = str_replace(' ', '', $order_number);
	$order_number = str_replace('0.', '', $order_number);
	$order_number .= rand(0, 9) . rand(0, 9);
	return $order_number;
}

/*
 * 获取订单状态
 * */

function get_order_status($key = '') {
	$arr = array(
		'0' => '未付款',
		'1' => '已付款',
		'2' => '已发货',
		'3' => '已收货',
		'4' => '已评价',
		'5' => '申请退款',
		'6' => '同意退款',
		'7' => '退款已发货',
		'8' => '已收货退款',
		'9' => '申请换货',
		'10' => '同意换货',
	);
	if ($key == '') {
		return $arr;
	} else {
		if (isset($arr[$key])) {
			return $arr[$key];
		} else {
			return '';
		}
	}
}
function get_bid_order_status($key = '') {
	//未付款、已付款、上课中、已结束、已评价
	$arr = array(
		'0' => '未付款',
		'1' => '已付款',
		'4' => '上课中',
		'5' => '已结束',
		'8' => '已评价',
		'9' => '申请仲裁',
		'10' => '提交仲裁',
		'11' => '取消仲裁',
		'12' => '仲裁结束',
	);
	if ($key == '') {
		return $arr;
	} else {
		if (isset($arr[$key])) {
			return $arr[$key];
		} else {
			return '';
		}
	}
}

function get_bid_status($key = '') {
	$arr = array(
		'0' => '老师未释放',
		'1' => '已释放',
		'2' => '学生同意',
		'3' => '同意但不打算继续合作',
		'4' => '提交仲裁',
		'5' => '已完成',
	);
	if ($key == '') {
		return $arr;
	} else {
		if (isset($arr[$key])) {
			return $arr[$key];
		} else {
			return '';
		}
	}
}

/*
 *
 * 获取快递公司名称
 * */

function get_shipping_company($id = '') {
	if ($id == '') {
		$result = get_result('shipping_company', array(
			'status' => '1',
		), array(
			'id',
			'title',
		));
		return $result;
	} else {
		$result = get_info('shipping_company', array(
			'status' => '1',
			'id' => $id,
		), array(
			'id',
			'title',
		));
		return $result['title'];
	}
}

//获得个人信息，如专业
function getPersonInfo($list = array(), $type = 1) {
	if (!empty($list)) {
		$eduInfos = get_field('school', array(
			'status' => 1,
		), 'id,title,type,pid');
		if ($type == 1) {
			foreach ($list as $k => $v) {
				$list[$k]['cityname'] = $eduInfos[$v['city']]['title'];
				$list[$k]['uniname'] = $eduInfos[$v['university']]['title'];
				$list[$k]['collegename'] = $eduInfos[$v['college']]['title'];
				$list[$k]['majorname'] = $eduInfos[$v['major']]['title'];
			}
		} else {
			$list['cityname'] = $eduInfos[$list['city']]['title'];
			$list['uniname'] = $eduInfos[$list['university']]['title'];
			$list['collegename'] = $eduInfos[$list['college']]['title'];
			$list['majorname'] = $eduInfos[$list['major']]['title'];
		}
	}
	return $list;
}

/*
 * 当前用户登录的状态
 */

function getStatus($demand_id = 0, $bid = 0) {
	//对应的需求的记录
	if ($demand_id) {
		$demand_result = get_info('demand', 'id=' . $demand_id);
	} else {
		$bid_result = get_info('bid', 'id=' . $bid);
		$demand_result = get_info('demand', 'id=' . $bid_result['demand_id']);
	}
	//对应的投标ID
	$bid_result = get_info('bid', 'id=' . $bid);
	$current_member['id'] = $_SESSION['sr_admin']['info']['id'];
	$current_member['role'] = $_SESSION['sr_admin']['info']['role'];
	if (!empty($demand_result) && $demand_result['role_type'] == 1) {
		//学生发的需求
		if ($current_member['role'] == 1) {
			//学生
			if ($demand_result['member_id'] == $current_member['id']) {
				$status = 1; //自己发的
			} else {
				$status = 2; //其他学生
			}
		} else {
			if ($bid_result['service_member_id'] == $current_member['id']) {
				$status = 3; //老师身份，投标的人
			} else {
				$status = 4; //自己以老师身份
			}
		}
	} else {
		//老师发的服务
		if ($current_member['role'] == 1) {
			if ($demand_result['member_id'] == $current_member['id']) {
				$status = 5; //该ID用户以学生身份登录
			} else {
				$status = 6; //其他学生签约
			}
		} else {
			if ($bid_result['service_member_id'] == $current_member['id']) {
				$status = 7; //自己的老师身份
			} else {
				$status = 8; //其他老师身份
			}
		}
	}
	return $status;
}

/*
 *  投标状态判定  1、是学生,2是老师
 * $status表示投标历史状态,$role_type表示角色属性,投标的id
 */

function getBidStatus($status, $role_type, $bid_id, $demand_type) {
	if ($role_type == 1) {
		switch ($status) {
		case 1:
			$contract = $demand_type == 1 ? '<span class="btn-green" data_id="' . $bid_id . '"  data_role="' . $role_type . '"  data_status="2" data_url="' . U('Home/Publish/upstatus') . '" >我要购买</span>' : '<span class="btn-green codebid" data_id="' . $bid_id . '"  data_role="' . $role_type . '"  data_status="2" data_url="' . U('Home/Publish/upstatus') . '" >我要签约</span>';
			break;
		case 2:
			$contract = '<span class="btn-gray">等待老师拟定合同</span>';
			break;
		case 3:
			$contract = '<span class="btn-green code_show_agree" data_id="' . $bid_id . '" data_status="' . $status . '" >查看最新合同</span>';
			break;
		case 4:
			$contract = '<span class="btn-green code_agree_pay" data_id="' . $bid_id . '" >已同意此合同,请先去付款.</span><span class="btn-green code_new_agree" data_id="' . $bid_id . '"  >查看最新合同</span>';
			break;
		case 5:
			$contract = '<span class="btn-gray">合同已修改,等待老师回复</span><span class="btn-green code_new_agree" data_id="' . $bid_id . '" data_status="' . $status . '"  >查看最新合同</span>';
			break;
		case 6:
			$contract = '<span class="btn-green code_show_agree" data_id="' . $bid_id . '" data_status="' . $status . '"  >查看最新合同</span>';
			break;
		case 7:
			$contract = '<span class="btn-gray" data_id="' . $bid_id . '"  >已付款成功,进入学习管理</span><span class="btn-green code_show_agree" data_id="' . $bid_id . '"  data_status="' . $status . '"  >查看最新合同</span> ';
		}
	} else {
		switch ($status) {
		case 1:
			$contract = ($demand_type == 1) ? '<span class="btn-gray">等待学购买</span>' : '<span class="btn-gray">等待学生签约</span>';
			break;
		case 2:
			$contract = '<span class="btn-green codeagree" data_id="' . $bid_id . '"  data_role="' . $role_type . '"  data_status="3" data_url="' . U('Home/Publish/contract') . '" >同意并拟定合同</span>';
			break;
		case 3:
			$contract = '<span class="btn-gray">合同已拟定，等待学生回复</span><span class="btn-green code_new_agree" data_id="' . $bid_id . '" data_status="' . $status . '"  >查看最新合同</span>';
			break;
		case 4:
			$contract = '<span class="btn-gray">已同意此合同，等待付款</span><span class="btn-green code_new_agree" data_id="' . $bid_id . '" data_status="' . $status . '"  >查看最新合同</span>';
			break;
		case 5:
			$contract = '<span class="btn-green codeagree" data_status="6"  data_id="' . $bid_id . '" data_role="' . $role_type . '"  >提出建议,请重新拟定</span>';
			break;
		case 6:
			$contract = '<span class="btn-gray" data_id="' . $bid_id . '" >合同已修改,等待学生回复</span><span class="btn-green code_new_agree" data_id="' . $bid_id . '" data_status="' . $status . '"  >查看最新合同</span>';
			break;
		case 7:
			$contract = '<span class="btn-gray" data_id="' . $bid_id . '"  >已付款成功,进入学习管理</span><span class="btn-green code_new_agree" data_id="' . $bid_id . '" data_status="' . $status . '"  >查看最新合同</span>';
		}
	}
	return $contract;
}

/*
 *
 */

/*
 * 获得树下所有的子节点
 */

function getChildTree($id = 0, $table) {
	static $cate = array();
	$map['pid'] = $id;
	$res = get_result($table, $map);
	if (!empty($res)) {
		foreach ($res as $v) {
			$cate[] = $v;
			getChildTree($v['id'], $table);
		}
	}
	return $cate;
}

//  积分，金币获取/消费   $sid => 积分规则表score_rule.id;  $table => 关联操作表名; $table_id => 关联操作表id; $mid => 用户id
//  $currency 货币类型   1 => 积分;  2 => 金币  默认为 1;
function createSoreRecord($sid, $table, $table_id, $mid, $currency) {
	if (!isset($sid) || !isset($table) || !isset($table_id)) {
		return false;
	} else {
		$currency = !empty($currency) ? intval($currency) : 1;
		$mid = !empty($mid) ? intval($mid) : $_SESSION['sr_admin']['info']['id'];
		$map['id'] = intval($sid);
		$data = get_info('score_rule', $map, 'score');
		$_POST = array(
			'member_id' => $mid,
			'score_rule_id' => intval($sid),
			'table' => $table,
			'table_id' => $table_id,
			'score' => $data['score'],
			'status' => 1,
		);
		$result = intval(update_data('score_record'));
		if ($result > 0) {
			if ($currency == 1) {
				M('member')->where('id=' . $mid)->setInc('score', $data['score']);
			} elseif ($currency == 2) {
				M('member')->where('id=' . $mid)->setInc('coin', $data['score']);
			}
			return true;
		} else {
			return false;
		}
	}
}

//   消息日志 （系统消息，投标消息，视听消息，支付消息，公告，留言等）
//   $fid => 消息发送者ID  $tid => 消息接收者ID  $com => 消息描述  $table => 关联操作表名; $table_id => 关联操作表id;
function message_log($fid, $tid, $com, $table, $table_id, $role = 1) {
	$_POST = array(
		'from_id' => !empty($fid) ? $fid : 0,
		'to_id' => $tid,
		'content' => $com,
		'table' => $table,
		'table_id' => $table_id,
		'role' => $role,
	);
	$result = update_data('message_log');
	if (is_numeric($result)) {
		return true;
	} else {
		return false;
	}
}

function message_log_r($fid, $tid, $com, $table, $table_id, $role = 1) {

	$result = M('message')->add(array(
		"from_member_id" => $fid,
		"to_member_id" => $tid,
		'content' => $com,
		'table' => $table,
		'role' => $role,
		'table_id' => $table_id,
		'add_time' => date('Y-m-d H:i:s'),
	));
	if (is_numeric($result)) {
		return true;
	} else {
		return false;
	}
}

//需求或服务类型
function get_type($type) {
	$type = empty($type) ? 1 : $type;
	switch ($type) {
	case 1:
		$tpl = '<span class="ico ico-zl"></span>';
		break;
	case 2:
		$tpl = '<span class="ico ico-dy"></span>';
		break;
	case 3:
		$tpl = '<span class="ico ico-sk"></span>';
		break;
	case 4:
		$tpl = '<span class="ico ico-gkk"></span>';
		break;
	default:
		$tpl = '';
	}
	return $tpl;
}

function randCode($length = 5, $type = 0) {
	$arr = array(
		1 => "0123456789",
		2 => "abcdefghijklmnopqrstuvwxyz",
		3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
	);
	if ($type == 0) {
		array_pop($arr);
		$string = implode("", $arr);
	} elseif ($type == "-1") {
		$string = implode("", $arr);
	} else {
		$string = $arr[$type];
	}
	$count = strlen($string) - 1;
	$code = '';
	for ($i = 0; $i < $length; $i++) {
		$code .= $string[rand(0, $count)];
	}
	return $code;
}

//获取交易状态
function getTrade($vo = 0) {
	if ($vo['bid_id']) {
		return getBidTrade($vo);

	} else {
		switch ($vo['status']) {
		case -1:
			$str = '已删除';
			break;
		case 0:
			$str = '未付款';
			break;
		case 1:
			$str = '已托付款';
			break;
		case 2:
			$str = '已发货';
			break;
		case 3:
			$str = '已收货';
			break;
		case 4:
			$str = '已评价';
			break;
		case 5:
			$str = '已申请退货';
			break;
		case 6:
			$str = '已同意退货';
			break;
		case 7:
			$str = '不同意退货';
			break;
		case 8:
			$str = '退货已发出';
			break;
		case 9:
			$str = '已收到退货';
			break;
		case 10:
			$str = '已申请仲裁';
			break;
		}
		return $str;
	}
}

function getBidTrade($vo) {
	switch ($vo['status']) {
	case -1:
		$str = '已删除';
		break;
	case 0:
		$str = '未付款';
		break;
	case 1:
		$str = '已托付款';
		break;
	case 2:
		$str = '未知';
		break;
	case 3:
		$str = '未知';
		break;
	case 4:
		if ($vo['bid_status'] == 0) {
			$str = '上课中';
		}
		if ($vo['bid_status'] == 1) {
			$str = "申请释放阶段条款";
		}
		if ($vo['bid_status'] == 2) {
			$str = "同意释放阶段条款并继续合作";
		}
		if ($vo['bid_status'] == 3) {
			$str = "同意释放阶段条款但不继续合作";
		}
		if ($vo['bid_status'] == 4) {
			$str = "申请仲裁";
		}
		if ($vo['bid_status'] == 5) {
			$str = "合作完成";
		}
		break;
	case 5:
		$str = '已结束';
		break;
	case 6:
		$str = '已评价';
		break;
	case 7:
		$str = '已评价';
		break;
	case 8:
		$str = '对方已评价';
		break;
	case 9:
		$str = '申请仲裁 ';
		break;
	case 10:
		$str = '提交仲裁';
		break;
	case 11:
		$str = '取消仲裁';
		break;
	case 12:
		$str = '仲裁结束';
		break;
	}
	return $str;
}
//试听状态
function getListenStatus($role, $status, $listen_id) {
	if ($role == 1) {
		//学生
		switch ($status) {
		case 1:
			$str = '<span class="btn-gray">你已经申请试听,等待老师确认</span>';
			break;
		case 2:
			$str = '<span class="btn-gray">老师同意你的申请</span>';
			break;
		}
	} else {
		switch ($status) {
		case 1:
			$str = '<span class="btn-green code_agree" data_id="' . $listen_id . '">同意</span>';
			break;
		case 2:
			$str = '<span class="btn-gray">同意申请</span>';
			break;
		}
	}
	return $str;
}

//当前用户试听信息
function getListen($uid, $demand_id) {
	if (empty($uid) || empty($demand_id)) {
		return false;
	}
	return get_info('apply_to_listen', array(
		'member_id' => $uid,
		'demand_id' => $demand_id,
	));
}

/*
 * 多图上传
 * $picture_ids 临时图片ID
 * $folder 上传目录
 * $table 图片保存数据表
 * $table_key_field 图片保存数据表对应字段名ID
 * $table_key_value 图片保存数据表的ID值
 * $image_field
 * */

function upload_file_to_attachments($picture_ids, $folder, $table, $table_id, $customer_id, $size) {
	if (!file_exists($folder)) {
		mkdir($folder);
	}
	if (is_array($picture_ids)) {
		$picture_ids = implode(',', $picture_ids);
	}
	$picture_ids = addslashes($picture_ids);

	if ($picture_ids == '') {
		$picture_ids = '0';
	}

	$result = get_result('file', array(
		'id' => array(
			'in',
			$picture_ids,
		),
	), array(
		'path',
	));
	$msg = '';
	foreach ($result as $row) {
		$path = ltrim($row['path'], '/');
		$file_name = basename($path);
		$new_path = $folder . '/' . $file_name;
		copy($path, $new_path);
		@unlink($path);
		$_POST = null;

		$_POST['path'] = $new_path;
		$_POST['table_id'] = $table_id;
		$_POST = array(
			'table' => $table,
			'table_id' => $table_id,
			'path' => $new_path,
			'name' => $file_name,
			'customer_id' => $customer_id,
			'size' => $size,
		);
		update_data('attachments');
	}
	delete_data('file', array(
		'id' => array(
			'in',
			$picture_ids,
		),
	));
}

//文件上传移动
function upload_file_to_att($picture_ids, $folder, $table, $table_id, $customer_id, $size) {
	if (!file_exists($folder)) {
		mkdir($folder);
	}
	if (is_array($picture_ids)) {
		$picture_ids = implode(',', $picture_ids);
	}
	$picture_ids = addslashes($picture_ids);

	if ($picture_ids == '') {
		$picture_ids = '0';
	}

	$result = get_result('file', array(
		'id' => array(
			'in',
			$picture_ids,
		),
	), array(
		'savepath',
	));
	$msg = '';
	foreach ($result as $row) {
		$path = ltrim($row['savepath'], '/');
		$file_name = basename($path);
		$new_path = $folder . '/' . $file_name;

		//move_uploaded_file($path,$new_path);
		copy($path, $new_path);
		@unlink($path);
		$_POST = null;

		$_POST['path'] = $new_path;
		$_POST['table_id'] = $table_id;
		$_POST = array(
			'table' => $table,
			'table_id' => $table_id,
			'path' => $new_path,
			'name' => $file_name,
			'customer_id' => $customer_id,
			'size' => $size,
		);
		update_data('attachments');
	}
	delete_data('file', array(
		'id' => array(
			'in',
			$picture_ids,
		),
	));
}

function setArray($name, $data) {
	$fileName = DATA_PATH . "Array/" . $name . ".js";
	if (!file_exists($fileName)) {
		file_put_contents($fileName, "//schools数据  \n return array();");
	}
	file_put_contents($fileName, "//schools数据  \n  " . stripslashes(var_export($data, true)) . ";", LOCK_EX);
	@unlink(RUNTIME_FILE);
	return true;
}

/**
 * 加载数组数据
 */
function loadArrayData($arryFileName, $selectedKey = '', $onlySelect = false) {
	$fileName = DATA_PATH . "Array/" . $arryFileName . ".php";
	if (!is_file($fileName)) {
		return array();
	}
	$arrayData = require $fileName;
	if ($selectedKey !== '' && !empty($arrayData[$selectedKey])) {
		$arrayData[$selectedKey]['selected'] = 1;
		if ($onlySelect) {
			return $arrayData[$selectedKey];
		}
	}

	return $arrayData;
}

//时间格式转换
function t_format($t) {
	return date('m-d', strtotime($t));
}

function get_profess_type($profess_type, $arr, $listschool) {
	//学业类型
	$professiontype = C('PROFESSIONTYPE');
	$publish = C('PUBLISHTYPE');
	$html = '<span class="profess_type">' . $professiontype[$profess_type] . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
	$ids = $arr['city'] . ',' . $arr['university'] . ',' . $arr['college'] . ',' . $arr['major'];
	$map['id'] = array(
		'in',
		$ids,
	);
	$listschool = M('school')->where($map)->getField('id,pid,code,title', true);
	if ($profess_type == 1) {
		$html .= '<span class="code_city">' . $listschool[$arr['city']]['title'] . '</span>&nbsp;&nbsp;';
		$html .= '<a style="color:#acacac;" href="' . U('Home/Circle/index', array(
			'id' => $arr['university'],
		)) . '">';
		$html .= $listschool[$arr['university']]['title'] . '&nbsp;' . $listschool[$arr['college']]['title'] . '&nbsp;' . $listschool[$arr['major']]['title'];
		$html .= '</a>';
	} else if ($profess_type == 2) {
		$unified = query_sql("", "select unified.*,unified_classify.title from unified left join unified_classify on unified.cid=unified_classify.id where unified.id=" . $arr["major"]);
		$html .= '<span class="ft-gray code_city">' . $unified[0]['title'] . '</span>';
		$html .= ' <span class="ft-gray code_city">' . $unified[0]['cname'] . '</span>';
	} else if ($profess_type == 3) {
		$html .= '<span class="ft-gray code_city">' . $publish[$arr["major"]] . '</span>';
	}
	return $html;
}
function w_get_school_info($demand) {
	$ids = $demand['city'] . ',' . $demand['university'] . ',' . $demand['college'] . ',' . $demand['major'];
	$map['id'] = array(
		'in',
		$ids,
	);
	//F('sql4',$demand['profes_type'] );
	if ($demand['profes_type'] == 1) {
		$a = M('school');
		$listschool = M('school')->where($map)->getField('id,pid,code,title', true);

		if ($demand['college']) {
			$url = U('User/Circle/wap_index', array(
				'id' => $demand['college'],
				'critype' => 2,
			));
		} else {
			$url = U('User/Circle/wap_index', array(
				'id' => $demand['university'],
				'critype' => 1,
			));
		}
		$html .= '<a style="color:#acacac;" href="' . $url . '">';
		$html .= $listschool[$demand['university']]['title'] . '&nbsp;' . $listschool[$demand['college']]['title'] . '&nbsp;' . $listschool[$demand['major']]['title'];
		$html .= '</a>';
	} else if ($demand['profes_type'] == 2) {
		$unified = query_sql("", "select unified.*,unified_classify.title from unified left join unified_classify on unified.cid=unified_classify.id where unified.id=" . $arr["major"]);
		$html .= '<span>' . $unified[0]['title'] . '</span>';
		$html .= ' <span>' . $unified[0]['cname'] . '</span>';
	} else if ($demand['profes_type'] == 3) {
		$publish = S('Home/openClass');
		$html .= '<span>' . $publish[$demand["major"]] . '</span>';
	}
	return $html;
}
function w_get_circle_info($community) {

	if ($community['critype'] == 1) {

		$res = get_info('school', array(
			'id' => $community['table_id'],
		));
		$circle = $res['title'];
	} else if ($community['critype'] == 2) {
		$res = query_sql('', "select s.title as s_title,c.title as c_title from school s left join school c on s.id =c.pid where c.id=" . $community['table_id'] . ' limit 1');

		$circle = $res[0]['s_title'] . ' ' . $res[0]['c_title'];
	} else if ($community['critype'] == 3) {
		$res = query_sql('', "select school.* ,degree_course.title as d_title from school left join degree_course on degree_course.code =substring(school.code,1,4) where school.id=" . $val['table_id'] . " limit 1");
		$circle = $res[0]['d_title'] . ' ' . $res[0]['title'];
	}
	return $circle;
}
function w_get_newest_pillow_talk($pillow_talk, $type, $w = 0) {
	$new_pillow_talk = query_sql('', "select content,add_time from pillow_talk where status=1 and  pid=" . $pillow_talk['id'] . ' order by id desc limit 1');
	if ($new_pillow_talk) {
		$content = $new_pillow_talk[0]['content'];
		$add_time = $new_pillow_talk[0]['add_time'];
	} else {
		$content = $pillow_talk['content'];
		$add_time = $pillow_talk['add_time'];
	}
	if ($type == 1) {
		if ($w == 1) {
			preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $content, $match);
			if ($match[0]) {
				$content = preg_replace('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', "i_w", $content);
				$sub_content = mb_substr($content, 0, 10, 'utf-8');
				$regex = '/^\w+/';
				$matches = array();
				if (preg_match($regex, substr($content, strlen($sub_content)), $matches)) {
					$sub_content .= $matches[0];
				}
				$content = $sub_content . '....';
				$content = str_replace("i_w", $match[0], $content);
			}
		}
		return $content;
	} else {
		return $add_time;
	}
}
function w_get_newest_pillow_talk_info($pillow_talk) {
	$new_pillow_talk = query_sql('', "select * from pillow_talk where status=1 and  pid=" . $pillow_talk['id'] . ' order by id desc limit 1');
	if ($new_pillow_talk) {
		return $new_pillow_talk[0];
	} else {
		return $pillow_talk;
	}

}
function w_get_newest_pillow_talk_extra($pillow_talk, $type, $w = 0) {
	$content = $pillow_talk['content'];
	$add_time = $pillow_talk['add_time'];
	if ($type == 1) {
		if ($w == 1) {
			preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $content, $match);
			if ($match[0]) {
				$content = preg_replace('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', "i_w", $content);
				$sub_content = msubstr($content, 0, 30);
				$regex = '/^\w+/';
				$matches = array();
				if (preg_match($regex, substr($content, strlen($sub_content)), $matches)) {
					$sub_content .= $matches[0];
				}
				$content = $sub_content; //.'....';
				$content = str_replace("i_w", $match[0], $content);
			}
		}
		return $content;
	} else {
		return $add_time;
	}
}
function get_profess_school($profess_type, $city, $university, $college, $major) {
	$professiontype = C('PROFESSIONTYPE');
	$unified = C('UNIFIED');
	$ids = $city . ',' . $university . ',' . $college;
	if (!empty($major)) {
		$ids .= ',' . $major;
	}
	//获取school
	$map['id'] = array(
		'in',
		$ids,
	);
	$schoolArr = M('school')->where($map)->getField('id,path,title', true);
	$html = '<span class="ft-green profess_type">' . $professiontype[$profess_type] . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
	if ($profess_type == 1) {
		$html .= '<span class="ft-green code_city">' . $schoolArr[$city]['title'] . '</span>&nbsp;&nbsp;';
		$html .= '<a class="ft-gray code_gray" href="' . U('Home/Circle/index', array(
			'id' => $university,
			'critype' => 1,
		)) . '">';
		$html .= $schoolArr[$university]['title'] . '&nbsp;' . $schoolArr[$college]['title'] . '&nbsp;' . $schoolArr[$major]['title'];
		$html .= '</a>';
	} else if ($profess_type == 2) {
		$html .= '<span class="ft-gray code_city">' . $unified[$major] . '</span>';
	} else if ($profess_type == 3) {
		$html .= '<span class="ft-gray code_city">' . $publish[$major] . '</span>';
	}
	return $html;
}

function user_avatar($uid, $type = 'virtual', $returnhtml = 1) {
	$uid = intval($uid);
	$uc_input = uc_api_input("uid=$uid");
	$uc_avatarflash = UC_API . '/images/camera.swf?inajax=1&appid=' . UC_APPID . '&input=' . $uc_input . '&agent=' . md5($_SERVER['HTTP_USER_AGENT']) . '&ucapi=' . urlencode(str_replace('http://', '', UC_API)) . '&avatartype=' . $type . '&uploadSize=2048';
	if ($returnhtml) {
		return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="253" id="mycamera" align="middle">
			<param name="allowScriptAccess" value="always" />
			<param name="scale" value="exactfit" />
			<param name="wmode" value="transparent" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="movie" value="' . $uc_avatarflash . '" />
			<param name="menu" value="false" />
			<embed src="' . $uc_avatarflash . '" quality="high" bgcolor="#ffffff" width="450" height="253" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
	} else {
		return array(
			'width',
			'450',
			'height',
			'253',
			'scale',
			'exactfit',
			'src',
			$uc_avatarflash,
			'id',
			'mycamera',
			'name',
			'mycamera',
			'quality',
			'high',
			'bgcolor',
			'#ffffff',
			'menu',
			'false',
			'swLiveConnect',
			'true',
			'allowScriptAccess',
			'always',
		);
	}
}

function user_api_input($data) {
	$s = urlencode(uc_authcode($data . '&agent=' . md5($_SERVER['HTTP_USER_AGENT']) . "&time=" . time(), 'ENCODE', UC_KEY));
	return $s;
}

//发送消息
function sendMessage($fuid, $cuid, $content, $table, $tableid) {
	if (empty($cuid) || empty($content) || empty($table) || empty($tableid)) {
		return false;
	}
	$_POST = array(
		'from_member_id' => $fuid,
		'to_member_id' => $cuid,
		'content' => $content,
		'table' => $table,
		'table_id' => $tableid,
	);
	return update_data('message');
}

//记录订单操作记录
function order_record($id, $status, $content, $bid_status = 0) {
	if (empty($id)) {
		return false;
	}
	$_POST = array(
		'order_id' => $id,
		'status' => $status,
		'content' => $content,
		'bid_status' => $bid_status,
	);
	return update_data('order_status_history');
}

/**
 * $str 原始中文字符串
 * $encoding 原始字符串的编码，默认GBK
 * $prefix 编码后的前缀，默认"&#"
 * $postfix 编码后的后缀，默认";"
 */
function unicode_encode($str, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';') {
	$str = iconv($encoding, 'UCS-2', $str);
	$arrstr = str_split($str, 2);
	$unistr = '';
	for ($i = 0, $len = count($arrstr); $i < $len; $i++) {
		$dec = hexdec(bin2hex($arrstr[$i]));
		$unistr .= $prefix . $dec . $postfix;
	}
	return $unistr;
}

/**
 * $str Unicode编码后的字符串
 * $decoding 原始字符串的编码，默认GBK
 * $prefix 编码字符串的前缀，默认"&#"
 * $postfix 编码字符串的后缀，默认";"
 */
function unicode_decode($unistr, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';') {
	$arruni = explode($prefix, $unistr);
	$unistr = '';
	for ($i = 1, $len = count($arruni); $i < $len; $i++) {
		if (strlen($postfix) > 0) {
			$arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
		}
		$temp = intval($arruni[$i]);
		$unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
	}
	return iconv('UCS-2', $encoding, $unistr);
}

function add_template_value($id, $city_ids_arr, $first_arr, $first_price_arr, $plus_arr, $plus_price_arr, $shipping_type, $shopping_id = 2) {

	foreach ($city_ids_arr as $k => $city_ids) {
		$_POST = null;
		$info = get_info('shipping_template_value', array(
			'city_ids' => $city_ids,
			'shipping_type' => $shipping_type,
		), array(
			'id',
		));
		if ($info) {
			delete_data('shipping_template_value', array(
				'city_ids' => $city_ids,
				'shipping_type' => $shipping_type,
			));
		}
		$_POST['shipping_template_id'] = $id;
		$_POST['shipping_type'] = $shipping_type;
		$_POST['shopping_id'] = $shopping_id;
		$_POST['first'] = $first_arr[$k];
		$_POST['first_price'] = $first_price_arr[$k];
		$_POST['plus'] = $plus_arr[$k];
		$_POST['plus_price'] = $plus_price_arr[$k];
		$_POST['city_ids'] = $city_ids;
		update_data('shipping_template_value');
	}

}

//裁剪图片
function imagecropper($source_path, $target_width, $target_height, $fileName) {
	$source_info = getimagesize($source_path);
	$source_width = $source_info[0];
	$source_height = $source_info[1];
	$source_mime = $source_info['mime'];
	if ($target_width == 0) {
		if ($source_height > $source_width) {
			$target_height = $source_width > 700 ? 700 : $source_width;
			$target_width = $source_width > 700 ? 700 : $source_width;
		} else {
			$target_height = $source_height;
			$target_width = $source_height;
		}
	}
	$source_ratio = $source_height / $source_width;
	$target_ratio = $target_height / $target_width;
	// 源图过高
	if ($source_ratio > $target_ratio) {
		$cropped_width = $source_width;
		$cropped_height = $source_width * $target_ratio;
		$source_x = 0;
		$source_y = ($source_height - $cropped_height) / 2;
	} // 源图过宽
	elseif ($source_ratio < $target_ratio) {
		$cropped_width = $source_height / $target_ratio;
		$cropped_height = $source_height;
		$source_x = ($source_width - $cropped_width) / 2;
		$source_y = 0;
	} // 源图适中
	else {
		$cropped_width = $source_width;
		$cropped_height = $source_height;
		$source_x = 0;
		$source_y = 0;
	}
	switch ($source_mime) {
	case 'image/gif':
		$source_image = imagecreatefromgif($source_path);
		break;

	case 'image/jpeg':
		$source_image = imagecreatefromjpeg($source_path);
		break;

	case 'image/png':
		$source_image = imagecreatefrompng($source_path);
		break;

	default:
		return '';
		break;
	}

	$target_image = imagecreatetruecolor($target_width, $target_height);
	$cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

	// 裁剪
	imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
	// 缩放
	imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

	switch ($source_mime) {
	case 'image/gif':
		imagegif($target_image, $fileName);
		break;
	case 'image/jpeg':
		imagejpeg($target_image, $fileName);
		break;

	case 'image/png':
		imagepng($target_image, $fileName);
		break;
	}
	imagedestroy($target_image);

	//保存图片到本地(两者选一)
	//    $randNumber = mt_rand(00000, 99999). mt_rand(000, 999);
	//    $fileName = substr(md5($randNumber), 8, 16) .".png";
	//    imagepng($target_image,'./'.$fileName);
	//imagedestroy($target_image);
	//直接在浏览器输出图片(两者选一)
	//    header('Content-Type: image/jpeg');
	//    imagepng($target_image);
	//    imagedestroy($target_image);
	//    imagejpeg($target_image);
	//    imagedestroy($source_image);
	//    imagedestroy($target_image);
	//    imagedestroy($cropped_image);
}
function getordcode() {
	$Ord = M('order');
	$numbers = range(10, 99);
	shuffle($numbers);
	$code = array_slice($numbers, 0, 4);
	$ordcode = $code[0] . $code[1] . $code[2] . $code[3];
	$oldcode = $Ord->where("order_number='" . $ordcode . "'")->getField('order_number');
	if ($oldcode) {
		return false;
	} else {
		return $ordcode;
	}
}

function get_date_time($type = 1) {
	$rs = FALSE;
	$now = time(); //当前时间戳
	switch ($type) {
	case 1: //今天
		$rs['begin_time'] = date('Y-m-d 00:00:00', $now);
		$rs['end_time'] = date('Y-m-d 23:59:59', $now);
		break;
	case 2: //本周
		$time = '1' == date('w') ? strtotime('Monday', $now) : strtotime('last Monday', $now);
		$rs['begin_time'] = date('Y-m-d 00:00:00', $time);
		$rs['end_time'] = date('Y-m-d 23:59:59', strtotime('Sunday', $now));
		break;
	case 3: //本月
		$rs['begin_time'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $now), '1', date('Y', $now)));
		$rs['end_time'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
		break;
	case 4: //三个月
		$time = strtotime('-2 month', $now);
		$rs['begin_time'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
		$rs['end_time'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
		break;
	case 5: //半年内
		$time = strtotime('-5 month', $now);
		$rs['begin_time'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
		$rs['end_time'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
		break;
	case 6: //今年内
		$rs['begin_time'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, 1, 1, date('Y', $now)));
		$rs['end_time'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, 12, 31, date('Y', $now)));
		break;
	}
	return $rs;
}

function time_search($start_time = '', $end_time = '') {
	if ($start_time != '' and $end_time == '') {
		$map = array(
			'egt',
			"$start_time",
		);
	}
	if ($start_time != "" and $end_time != '') {
		$map = array(
			'between',
			array(
				"$start_time",
				"$end_time",
			),
		);
	}
	if ($start_time == '' and $end_time == '') {
		$map = '';
	}
	if ($start_time == "" and $end_time != '') {
		$map = array(
			'elt',
			"$end_time",
		);
	}
	return $map;
}
function get_current_learning_period($learn_result) {
	$current_learning_period_id = 0;
	foreach ($learn_result as $k => $v) {
		if ($k == 0 && $v['status'] == 0) {
			$current_learning_period_id = $v['id'];
			break;
		}
		if ($v['status'] == 0 && $learn_result[$k - 1]['status'] == 2) {
			$current_learning_period_id = $v['id'];
			break;
		}
	}
	return $current_learning_period_id;
}
function get_current_learning_period_s($learn_result) {
	$current_learning_period_id = 0;
	foreach ($learn_result as $k => $v) {
		if ($v['status'] == 1) {
			$current_learning_period_id = $v['id'];
			break;
		}
	}
	return $current_learning_period_id;
}

//将10进制转换为16进制，其中做了简单混淆处理
function convHex($dec, $confuse = 0x1e240) {
	$confuse = max(123, $confuse);

	$conv = $dec + $confuse;
	$temp = ($conv % 10) + 6;
	return base_convert($conv, 10, 16) . base_convert($temp, 10, 16);
}
//将convHex所得的16进制结果 转换会10进制
function convDec($hex, $confuse = 0x1e240) {
	$hex_a = base_convert(substr($hex, 0, -1), 16, 10);
	$hex_b = base_convert(substr($hex, -1), 16, 10);

	if ($hex_a % 10 == $hex_b - 6) {
		return $hex_a - $confuse;
	} else {
		//非正确格式的16进制值
		return false;
	}
}
