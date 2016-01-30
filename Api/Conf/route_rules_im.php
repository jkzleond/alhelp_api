<?php
/*
	IM 相关路由
*/
return array(
	'v1/im/group$' => 'Group/group',//POST:创建群
	'v1/im/group/:id\d$' => 'Group/group',//GET:获取指定ID群信息|PUT:更新指定ID群信息|DELETE:解散指定ID群
	'v1/im/group/:id\d/member$' => 'Group/member', //POST:添加群成员|DELETE:删除群成员
	'v1/im/groups/[:uid]/[:is_owner]' => 'Group/group_list', //获取指定用户所在群或所有群列表 uid:用户ID
	'v1/im/message/:type/:to_id\d$' => 'Imessage/message', //POST:发悄悄话或发群聊|
	'v1/im/message/history/:type/:to_id\d/[:p]/[:ps]' => 'Imessage/history',//GET:获取与某个用户或某个群的聊天记录
    'v1/im/message/no_read/[:p]/[:ps]' => 'Imessage/no_read_msg', //GET:获取未读消息
    'v1/im/message/no_read_total$' => 'Imessage/no_read_msg_total', //GET:获取未读消息总条数
    'v1/im/message/mark_read/:type/:from_id$' => 'Imessage/mark_read', //PUT:将消息标记已读
	'v1/im/message/rct_contacts/[:p]/[:ps]' => 'Imessage/recent_contacts' //GET:获取当前用户的最近联系人
);
