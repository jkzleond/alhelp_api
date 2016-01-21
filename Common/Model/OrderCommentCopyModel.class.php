<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class OrderCommentCopyModel extends ViewModel{
      
    public $viewFields = array(     
        'order_comments'=>array('*'),  
		'order_product'=>array('table_name','table_id','_on'=>'order_product.order_id=order_comments.order_id'), 
		'member'=>array('nickname'=>'from_member_name','_on'=>'member.id=order_comments.member_id'), 
		'user'=>array('_table'=>'member','nickname'=>'to_member_name','_on'=>'user.id=order_comments.to_member_id'),
        'demand'=>array('description','demand_type','profes_type','university','college','major','city', '_on'=>'demand.id=order_product.table_id'),		
	
    ); 
}
