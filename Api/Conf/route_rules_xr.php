<?php
return array(
	"v1/find_password" => array("User/find_password"), //找回密码
	"v1/set_pwd" => array("User/set_pwd"), //设置密码
	"v1/set_user_status" => array("User/set_user_status"), //修改用户状态

	"v1/create_test_listen" => array("Demand/create_test_listen"),
	"v1/demands/signType" => array("Demand/signType"), //签约类型列表
	"v1/create_sign" => array("Demand/create_sign"), //申请签约
	"v1/create_contract" => array("Demand/create_contract"),
	"v1/edit_contract" => array("Demand/edit_contract"),
	"v1/yes_contract" => array("Demand/yes_contract"),

	"v1/demands/info/:id$" => array("Demand/info"), //答疑详情
	"v1/demands/bookinfo/:id$" => array("Demand/bookinfo"),
	"v1/demands/recommend/:type/:id$" => array("Demand/recommend"), //推荐
	"v1/demands/collaborates/:demand_id$" => array("Demand/demandCollaborate"), //获取交易成功合作者列表
	"v1/demands/collaborates/:demand_id/page/:page$" => array("Demand/demandCollaborate"), //获取交易成功合作者列表
	"v1/demands/delete/:id$" => array("Demand/demandDelete"), //删除
	"v1/demands/down/:id$" => array("Demand/demandDown"), //上架
	"v1/demands/up/:id$" => array("Demand/demandUp"), //下架
	"v1/demands/explain" => array("Demand/demandExplain"), //补充说明
	"v1/demands/demandExplainLists/:id$" => array("Demand/demandExplainLists"), //补充说明列表
	"v1/demands/listen/lists" => array("Demand/listenLists"), //试听列表
	"v1/demand/listens/:demand_id$" => "Demand2/listens",
	"v1/demand/listens/:demand_id/page/:page$" => "Demand2/listens",
	"v1/demands/bid$" => array("Demand/bidInfo"), //协议详情
	"v1/demands/bid/lists/:id$" => array("Demand/bidList"), //协议列表

	"v1/address/lists" => array('Address/addressList'), //收货人列表
	"v1/address/info/:id$" => array('Address/addressInfo'), //收货人详情
	"v1/areas/lists/:id$" => array('Address/areas'), //获取省市县地址
	"v1/address/add" => array('Address/addressAdd'), //添加收货人
	"v1/address/delete/:id$" => array('Address/addressDelete'), //删除收货人
	"v1/address/edit" => array('Address/addressEdit'), //修改收货人
	"v1/address/default/:id$" => array('Address/addressSetDefault'), //设置默认收货人

	"v1/alipay/:order$" => array('Alipay/pay'), //支付宝支付
	"v1/wxpay/:order$" => array('Wxpay/pay'), //微信支付
	"v1/remainingSum/:order$" => array('RemainingSum/pay'), //账号余额支付

	"v1/cart/lists" => array('ShopCart/lists'), //购物车列表
	"v1/cart/add_cart" => array('ShopCart/add_cart'), //添加购物车
	"v1/cart/modNum" => array('ShopCart/modNum'), //修改商品数量 (删除)
	"v1/cart/combine/:id$" => array('ShopCart/create_order'), //订单生成

	"v1/collection/lists" => array('collection/lists'), //收藏列表
	"v1/collection/add" => array('collection/add'), //添加收藏

	"v1/comment/add" => array('Comment/add'), //添加评论
	"v1/comment/list" => array('Comment/list'), //评论列表
	"v1/comment/praise/:id$" => array('Comment/praise'), //评论点赞

);
