<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class MemberModel extends ViewModel
{
	
	public $viewFields = array (
			'member' => array (
					'id', 
					'pid', 
					'reg_time', 
					'_type' => 'LEFT' 
			), 
			'user' => array (
					'_table' => 'member', 
					"nickname", 
					'_on' => 'user.id=member.pid' 
			) 
	);
}
