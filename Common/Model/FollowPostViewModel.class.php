<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowPostViewModel
 * 评论视图模型
 *
 * @author Auser
 */
class FollowPostViewModel extends ViewModel
{
	
	public $viewFields = array (
		'follow_post' => array ( 
			'member_id', 
			'member_post_id', 
			'status', 
			'type', 
			'is_auto' 
		)
		, 
		'member_post' => array ( //说说
			'id', 
			'praise_num', 
			'replies_num', 
			'fid', 
			'f_number', 
			'mess_type', 
			'content', 
			'member_name', 
			'member_id', 
			'add_time', 
			'community_id', 
			'time_top', 
			'time_hot', 
			'time_announcement', 
			'last_comment_time', 
			'_on' => 'member_post.id=follow_post.member_post_id', 
			'_type' => 'left' 
		), 
		'fmember_post' => array (//评论
			'_table' => 'member_post', 
			'content' => 'fcontent', 
			'member_name' => 'fmember_name', 
			'member_id' => 'fmember_id', 
			'add_time' => 'fadd_time', 
			'community_id', 
			'_on' => 'member_post.fid>0  and fmember_post.id=member_post.fid' 
		), 
		'member' => array (
			'_table' => 'member', 
			'is_vip', 
			'_on' => 'member.id=member_post.member_id' 
		) 
	);

}
