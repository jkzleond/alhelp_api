<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class DemandViewModel extends ViewModel
{
	
	public $viewFields = array (
			'demand' => array (
					'*' 
			), 
			'member' => array (
					'serve_num', 
					'nickname' => 'member_name', 
					'is_realname', 
					'frozen_money', 
					'demand_num', 
					'nickname', 
					'fans_num', 
					'question_num', 
					'follow_num', 
					'_on' => 'demand.member_id=member.id' 
			) 
	);
}
