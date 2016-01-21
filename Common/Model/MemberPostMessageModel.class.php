<?php
namespace Common\Model;
use Think\Model\RelationModel;

use Think\Model\ViewModel;

/**
 * Description of MemberPostMessageModel
 * 
 *
 * @author Auser
 */
class MemberPostMessageModel extends ViewModel
{
	
	public $viewFields = array (
			'message' => array (
					'_table' => 'member_post_message', 
					'id' => 'message_id', 
					'type', 
					'from_member_id', 
					'to_member_id', 
					'add_time', 
					'is_read' 
			),  //'_type' => 'LEFT' 
			'to_member' => array (
					'_table' => 'member', 
					//	'_as' => 'to_member', 
					'nickname', 
					'nickname' => 'to_nickname', 
					'_on' => 'message.to_member_id=to_member.id' 
			), 
			
			'old_post' => array ( //被转发的说说
					'_table' => 'member_post', 
					'id' => 'old_post_id', 
					'content' => 'old_ontent', 
					'_on' => 'old_post.id=message.m_p_id' 
			), 
			
			'form_member' => array (
					'_table' => 'member', 
					//	'_as' => 'to_member', 
					'nickname' => 'form_nickname', 
					
					'_on' => 'message.from_member_id=form_member.id', 
					'_type' => 'LEFT' 
			), 
			
			'new_post' => array ( //新的说说
					'_table' => 'member_post', 
					'id' => 'post_id', 
					'id' => 'new_post_id', 
					'content', 
					'content' => 'new_content', 
					'_on' => 'new_post.id=message.new_id' 
			) 
	);
}
