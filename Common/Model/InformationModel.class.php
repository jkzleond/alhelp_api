<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class InformationModel extends ViewModel
{
	
	public $viewFields = array (
			'information' => array (
					'id', 
					'cid', 
					'path', 
					'title', 
					'add_time', 
					'status', 
					'type', 
					'_type' => 'left' 
			), 
			'category' => array (
					'title' => 'c_title', 
					'_on' => 'category.id=information.cid' 
			) 
	);
}
