<?php
namespace Common\Model;
use Think\Model\ViewModel;
/**
 * Description of CommunityModel
 *
 * @author Auser
 */
class CommunityTeaManModel extends ViewModel
{
	
	public $viewFields = array (
		'community' => array (
			'member_id', 
			'table_id', 
			'title', 
			'_type' => 'LEFT' 
		), 
		'card' => array (
			'signature', 
			'nickname', 
			'content', 
			'type' => 'card_type', 
			'_on' => 'community.member_id=card.member_id and card.type=2 and card.status=1', 
			'_type' => 'LEFT' 
		), 
		'member' => array (
			'serve_num', 
			'demand_num', 
			'nickname', 
			'fans_num', 
			'question_num', 
			'follow_num', 
			'community_id', 
			'_on' => 'member.id=community.member_id' 
		) 
	);

}
