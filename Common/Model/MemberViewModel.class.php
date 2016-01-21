<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class MemberViewModel extends ViewModel
{
	
	public $viewFields = array (
			'member' => array (
					'nickname' => 'm_nickname', 
					'id' => 'member_id', 
					'_type' => 'LEFT' 
			), 
			'card' => array (
					"id", 
					"type", 
					"signature", 
					"nickname", 
					"content", 
					"status", 
					"add_time", 
					"update_time", 
					'_on' => 'card.member_id=member.id' 
			) 
	);
}
