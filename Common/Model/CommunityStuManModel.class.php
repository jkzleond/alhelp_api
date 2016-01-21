<?php
namespace Common\Model;
use Think\Model\ViewModel;
/**
 * Description of CommunityModel
 *
 * @author Auser
 */
class CommunityStuManModel extends ViewModel
{
	
	public $viewFields = array (
		'community' => array (
			'member_id', 
			'_type' => 'LEFT' 
		), 
		'card' => array (
			'signature', 
			'nickname', 
			'content', 
			'type' => 'card_type', 
			'_on' => 'community.member_id=card.member_id and card.type=1 and card.status=1', 
			'_type' => 'LEFT' 
		), 
		'member' => array (
			'serve_num', 
			'demand_num', 
			'nickname', 
			'fans_num', 
			'question_num', 
			'follow_num', 
			'_on' => 'member.id=community.member_id' 
		) 
	);

}
