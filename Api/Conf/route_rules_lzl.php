<?php
return array(
	"v1/tokens" => "User/token", //获取token
	"v1/user_exists" => "User/user_exists", //用户是否存在
	"v1/switch_role" => "User/switchRole", //切换用户角色
	"v1/fans$" => "Fans/fans", //粉丝列表(全部)
	"v1/fans/page/:page" => "Fans/fans", //粉丝列表(分页)
	"v1/follow$" => "Fans/follow", //获取关注我的人(全部)
	"v1/follow/page/:page" => "Fans/follow", //获取关注我的人(分页)
	"v1/fans/:uid" => "Fans/fans", //粉丝列表(全部)
	"v1/fans/:uid/page/:page" => "Fans/fans", //粉丝列表(分页)
	"v1/follow/:uid" => "Fans/follow", //获取关注我的人(全部)
	"v1/follow/:uid/page/:page" => "Fans/follow", //获取关注我的人(分页)
	"v1/they" => "They/index", //他人操作
	"v1/upload/image/new" => "Upload/images", //上传图片//"Index/index",//
	
	
    "v1/upload/image" => "Index/upload_image", //上传图片	
		
	"v1/talks/similarity/:id" => "Talks/talk_similarity", //获取相似度的说说
	"v1/talks/list/master/page/:page" => "Talks/talk_mslist", //获取我的说说列表
	"v1/talks/list/page/:page" => "Talks/talk_list", //获取我的说说列表
	"v1/talks/list$" => "Talks/talk_list", //获取我的说说列表    
	"v1/talks/list/fans/page/:page" => "Talks/talk_fans_list", //获取我的粉丝说说列表
	"v1/talks/list/fans$" => "Talks/talk_fans_list", //获取我的粉丝说说列表
	"v1/talks/list/follow/page/:page" => "Talks/talk_follow_list", //获取我的粉丝说说列表
	"v1/talks/list/follow$" => "Talks/talk_follow_list", //获取我的粉丝说说列表
	"v1/talks/list/:uid/page/:page" => "Talks/talk_list", //获取某人说说列表/所有列表
	"v1/talks/list/:uid" => "Talks/talk_list", //获取某人说说列表/所有列表
	"v1/talks/listby/:type/:type_id/page/:page" => "Talks/talk_where", //通过大学、学院、专业获取说说列表	
	"v1/talks/listby/:type/:type_id" => "Talks/talk_where", //通过大学、学院、专业获取说说列表
	"v1/talks/:id/comment" => "Talks/talks_comment", //回复说说
	"v1/talks/:id/praise" => "Talks/praise", //说说点赞
	"v1/talks/:id/top" => "Talks/top", //说说置顶
	"v1/talks/:id/top_off" => array("Talks/top", "is_top=0"), //取消说说置顶
	"v1/talks/:id/hot" => "Talks/hot", //说说设置热门
	"v1/talks/:id/hot_off" => array("Talks/hot", "is_hot=0"), //取消说说热门
	"v1/talks/:id/ann" => "Talks/announce", //说说设置公告
	"v1/talks/:id/ann_off" => array("Talks/announce", "is_ann=0"), //取消说说公告
	"v1/talks/:id/del" => "Talks/talk_delete", //删除说说
	"v1/talks/:id" => "Talks/talks_details", //获取说说详情
	"v1/talks" => "Talks/add_talk", //发表说说

	"v1/security/verify/sms" => "VerifySms/verify_code", //短信验证码
	"v1/logout" => "User/logout", //退出登录
	"v1/first_third_login" => "User/first_third_login", //第三方第一次登陆
	"v1/release" => "Service/release", //发布服务、需求
	"v1/chat/goods$" => "Goods/add", //添加商品
	"v1/chat/goods/a/:a_user_id/b/:b_user_id" => "Goods/goods", //获取最新商品类型
	"v1/chat/goods/u/:user_id/t/:type_id" => "Goods/goods_list", //获取指定用户指定类型的商品列表	
	"v1/unlock" => "User/unlock", //激活账号
	"v1/zoom_image" => "Index/image_resize",
	"v1/post_day" => "Index/getPubMedDistanceDate", //获取考研剩余天数
);