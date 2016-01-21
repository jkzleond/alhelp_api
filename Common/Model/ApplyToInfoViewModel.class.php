<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ApplyToInfoViewModel extends ViewModel
{
	
	public $viewFields = array (
		'apply_to_info' => array (
			'*' 
		), 
		'member' => array (
			'nickname', 
			'praise_num', 
			'fans_num', 
			'_on' => 'member.id=apply_to_info.member_id' 
		), 
		'information' => array (
			'title', 
			'_on' => 'information.id=apply_to_info.table_id' 
		), 
		'category' => array (
			'type', 
			'title' => 'c_title', 
			'_on' => 'category.id=information.cid' 
		) 
	);

}
