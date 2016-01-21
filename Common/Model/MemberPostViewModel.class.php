<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class MemberPostViewModel extends ViewModel
{
	
	public $viewFields = array (
			'member_post' => array (
					'id', 
					'praise_num', 
					'replies_num', 
					'content', 
					'member_name', 
					'member_id', 
					'add_time', 
					'community_id' 
			) 
	);
}
