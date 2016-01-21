<?php
namespace Common\Model;
use Think\Model\ViewModel;
/**
 * Description of CommunityModel
 *
 * @author Auser
 */
class CommunityContentModel extends ViewModel
{
	
	public $viewFields = array (
		'community_content' => array (
			'*', 
			'_type' => 'LEFT' 
		), 
		'member' => array (
			'avatar', 
			'nickname', 
			'_on' => 'community_content.member_id=member.id' 
		) 
	);

}
