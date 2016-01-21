<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class CommViewModel extends ViewModel
{
	
	public $viewFields = array (
		'comment' => array (
			'praise_num', 
			'member_id', 
			'add_time', 
			'content', 
			'id', 
			'pid', 
			'table' => 'comtable', 
			'content', 
			'_type' => 'LEFT' 
		), 
		'member' => array (
			'nickname', 
			'_on' => 'comment.member_id=member.id' 
		) 
	);
}
