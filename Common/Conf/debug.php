<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
* debug用系统配文件
 * 所有系统级别的配置
 */
$array = array(

	'DB_TYPE' => 'pdo',
	'DB_USER' => 'alhelp',
	'DB_PWD' => 'tSfquCes5UyxAmnU',
	'DB_PDO_TYPE' => 'mysql',
	'DB_DSN' => 'mysql://alhelp:tSfquCes5UyxAmnU@120.25.161.67:3306/alhelp#utf8',

	'URL_ROUTER_ON' => true, //启用路由
	'API_ROUTE_FILE' => 'route_rules_sg,route_rules_xr,route_rules_lzl,route_rules_jxy,route_rules_im', //要加载的路由规则文件

	'APP_SUB_DOMAIN_DEPLOY' => 1, // 开启子域名配置
	'APP_SUB_DOMAIN_RULES' => array(
		'localhost:8850' => 'Api',
	),
	'APP_DOMAIN' => array(
		'Home' => 'http://v1.alhelp.net',
		'Backend' => 'http://adm.alhelp.net',
		'Adm' => 'http://admin.alhelp.net',
		'User' => 'http://user.alhelp.net',
		'Api' => 'http://api.alhelp.net',
		'_Home' => 'v1.alhelp.net',
		'_Backend' => 'adm.alhelp.net',
		'_Adm' => 'admin.alhelp.net',
		'_User' => 'user.alhelp.net',
		'_Api' => 'api.alhelp.net',
	),

	'COOKIE_EXPIRE' => 0, // Cookie有效期
	'COOKIE_DOMAIN' => '.alhelp.net', // Cookie有效域名
	'COOKIE_PATH' => '/', // Cookie路径
	// 'COOKIE_PREFIX' => 'tcyun', // Cookie前缀 避免冲突
	'COOKIE_HTTPONLY' => '', // Cookie httponly设置

	'SESSION_OPTIONS' => array('domain' => '.alhelp.net', 'expire' => 500),

	'SHOW_ERROR_MSG' => true,
	'LOG_RECORD' => false,
	/* 模块相关配置 */
	'AUTOLOAD_NAMESPACE' => array(
		'Addons' => ONETHINK_ADDON_PATH,
	), //扩展模块列表
	// 'DEFAULT_MODULE' => 'Api',
	// 'DEFAULT_CONTROLLER'    =>  'User/Login', // 默认控制器名称
	// 'DEFAULT_ACTION'        =>  'index', // 默认操作名称

	'TMPL_TEMPLATE_SUFFIX' => '.php',
	'MODULE_DENY_LIST' => array(
		'Common',
		'Module',
		'Admin',
	),
	//'MODULE_ALLOW_LIST'  => array('Home','Admin'),

	/* 系统数据加密设置 */
	'DATA_AUTH_KEY' => '18u2U_DfS<;j6C[cipn}|tr9*Bl">$+mw)T%eOMz', //默认数据加密KEY

	/* 用户相关设置 */
	'USER_MAX_CACHE' => 1000, //最大缓存用户数
	'USER_ADMINISTRATOR' => 1, //管理员用户ID

	'URL_HTML_SUFFIX' => '',

	/* URL配置 */
	'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL' => 2, //URL模式
	'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
	'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符
	/* 全局过滤配置 */
	'DEFAULT_FILTER' => '', //全局过滤函数

	//'LOAD_EXT_CONFIG' => 'db',

	// 'DB_TYPE'   => 'mysqli', // 数据库类型
	// 'DB_HOST'   => 'localhost', // 服务器地址
	// 'DB_NAME'   => 'acbooking', // 数据库名
	// 'DB_USER'   => 'root', // 用户名
	// 'DB_PWD'    => '1q2w3e',  // 密码
	// 'DB_PORT'   => '3306', // 端口
	// 'DB_PREFIX' => '', // 数据库表前缀

	/* 文档模型配置 (文档模型核心配置，请勿更改) */
	'DOCUMENT_MODEL_TYPE' => array(
		2 => '主题',
		1 => '目录',
		3 => '段落',
	),

	/* 图片上传相关配置 */
	'PICTURE_UPLOAD' => array(
		'mimes' =>array('image/jpg',
			'image/jpeg',
			'image/png',
			'image/pjpeg',
			'image/gif',
			'image/bmp',
			'image/x-png'), //允许上传的文件MiMe类型
		'maxSize' => 2 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
		'exts' => 'jpg,gif,png,jpeg,bmp', //允许上传的文件后缀
		'autoSub' => false, //自动子目录保存文件
		'subName' => '', //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'rootPath' => './Images/', //保存根路径
		'savePath' => date('Y').'/'.date('m').'/'.date('d').'/', //保存路径
		'saveName' => array(
			'uniqid',
			'',
		), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveExt' => '', //文件保存后缀，空则使用原后缀
		'replace' => false, //存在同名是否覆盖
		'hash' => true, //是否生成hash编码
		'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
	), //图片上传相关配置（文件上传类配置）

	/* 文件上传相关配置 */
	'DOWNLOAD_UPLOAD' => array(
		'mimes' => '', //允许上传的文件MiMe类型
		'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
		'exts' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,mp3,lrc,pdf,ppt,xls', //允许上传的文件后缀
		'autoSub' => true, //自动子目录保存文件
		'subName' => '', //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'rootPath' => './Uploads/File/', //保存根路径
		'savePath' => '', //保存路径
		'saveName' => array(
			'uniqid',
			'',
		), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveExt' => '', //文件保存后缀，空则使用原后缀
		'replace' => false, //存在同名是否覆盖
		'hash' => true, //是否生成hash编码
		'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
	), //下载模型上传配置（文件上传类配置）

	//邮件配置
	//  	'THINK_EMAIL' => array(
	// 	'SMTP_HOST'   => 'smtp.qq.com', //SMTP服务器
	// 	'SMTP_PORT'   => '25', //SMTP服务器端口
	// 	'SMTP_USER'   => '1624220890@qq.com', //SMTP服务器用户名
	// 	'SMTP_PASS'   => '', //SMTP服务器密码
	// 	'FROM_EMAIL'  => '1624220890@qq.com', //发件人EMAIL
	// 	'FROM_NAME'   => '新助邦', //发件人名称
	// 	'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
	// 	'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
	// ),

	/*	'THINK_EMAIL' => array(
		'SMTP_HOST'   => 'smtp.vip.163.com', //SMTP服务器
		'SMTP_PORT'   => '25', //SMTP服务器端口
		'SMTP_USER'   => 'xinzhubang@vip.163.com', //SMTP服务器用户名
		'SMTP_PASS'   => '15001276577', //SMTP服务器密码
		'FROM_EMAIL'  => 'xinzhubang@vip.163.com', //发件人EMAIL
		'FROM_NAME'   => 'xinzhubang@vip.163.com', //发件人名称
		'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
		'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
	*/
	'THINK_EMAIL' => array(
		'SMTP_HOST' => 'smtp.163.com', //SMTP服务器
		'SMTP_PORT' => '25', //SMTP服务器端口
		'SMTP_USER' => 'long151473@163.com', //SMTP服务器用户名
		'SMTP_PASS' => 'long123456', //SMTP服务器密码
		'FROM_EMAIL' => 'long151473@163.com', //发件人EMAIL
		'FROM_NAME' => 'long151473@163.com', //发件人名称
		'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
		'REPLY_NAME' => '', //回复名称（留空则为发件人名称）
	),
	//需求类型
	'DEMAND_TYPE' => array(
		'1' => '资料',
		'2' => '答疑',
		'3' => '授课',
		'4' => '直播课',
	),

	//学科门类
	'SUBJECT' => array(
		'01' => '哲学',
		'02' => '经济学',
		'03' => '法学',
		'04' => '教育学',
		'05' => '文学',
		'06' => '历史学',
		'07' => '理学',
		'08' => '工学',
		'09' => '农学',
		'10' => '医学',
		'11' => '军事学',
		'12' => '管理学',
		'13' => '艺术学',
	),

	//快递运费类型
	'SHIPPING' => array(),

	//考试类型
	'PROFESSIONTYPE' => array(
		'1' => '非统考',
		'2' => '统考',
		'3' => '公共课',
	),

	//统考门类
	'UNIFIED' => array(
		'Computer' => '计算机',
		'Law' => '法硕',
		'Psychology' => '心理学',
		'Education' => '教育学',
		'Financial' => '金融联考',
		'Medicine' => '西医综合',
		'History' => '历史',
		'Agronomy' => '农学',
	),

	//公共课门类
	'PUBLISHTYPE' => array(
		'English' => '英语',
		'Math' => '数学',
		'Political' => '政治',
	),

	//范文类型
	'ESSAYCLASS' => array(
		'1' => '协议',
		'2' => '合同',
		'3' => '学习计划',
	),

	'MOBILE_CONFIG' => array( //手机配置
		'USER' => '102274', //账号
		'PASSWORD' => '987456', //密码
		'URL' => "http://36.101.204.2:18988/GetXmlString.aspx",
	),

	'WEIBO_CONFIG' => array( //微博配置
		// 'WB_AKEY' => '3663857479',
		// 'WB_SKEY' => 'eb8ca6c29529cda7d6aa7bc7d0c11663',
		'WB_AKEY' => '3505924954',
		'WB_SKEY' => 'd416ccba34cbbb685e8348604eb7c36f',
		'WB_CALLBACK_URL' => 'http://v1.alhelp.net/User/Weibo/callback.html',
	),

	'BANK_LIST' => array(
		"0" => array(
			"title" => '中国银行',
			"img" => __ROOT__ . "/Public/Home/images/bank/zg.jpg",
			"code" => "BOCB2C",
		),
		"1" => array(
			"title" => '中国建设银行',
			"img" => __ROOT__ . "/Public/Home/images/bank/zg.jpg",
			"code" => "CCB",
		),
		"2" => array(
			"title" => '中国工商银行',
			"img" => __ROOT__ . "/Public/Home/images/bank/gs.jpg",
			"code" => "ICBCB2C",
		),
		"3" => array(
			"title" => '中国农业银行',
			"img" => __ROOT__ . "/Public/Home/images/bank/ny.jpg",
			"code" => "ABC",
		),
		"4" => array(
			"title" => "中国邮政储蓄银行",
			"img" => __ROOT__ . "/Public/Home/images/bank/yz.jpg",
			"code" => "PSBC-DEBIT",
		),
		"5" => array(
			"title" => "交通银行",
			"img" => __ROOT__ . "/Public/Home/images/bank/jt.jpg",
			"code" => "COMM",
		),
		"6" => array(
			"title" => "招商银行",
			"img" => __ROOT__ . "/Public/Home/images/bank/zs.jpg",
			"code" => "CMB",
		),
	),
);

return $array;
