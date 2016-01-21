<?php
/**
+------------------------------------------------------------------------------
 * 用户行为标签
+------------------------------------------------------------------------------
 */
return array(
	'app_init' => array(
			'Common\\Behavior\\ErrorHandlerRegisterBehavior', //注册错误处理器
			'Common\\Behavior\\LoadRouteBehavior', // //初始化加载API路由规则
	),
	'setScore' => array(
		'Common\Behavior\ScoreBehavior', // 积分
	),
	"app_begin" => array("Common\Behavior\GetcodingBehavior"),
)
;
