<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class OrderCommentModel extends ViewModel{
      
    public $viewFields = array(     
        'order_comments'=>array('*','_type'=>'LEFT'),    
        'member'=>array('nickname', '_on'=>'member.id=order_comments.member_id','_type'=>'LEFT'),
		
		'order_product'=>array('table_id', '_on'=>'order_product.id=order_comments.order_id','_type'=>'LEFT'),
		'demand'=>array('member_id'=>'to_member_id','description','university','college','major','city','demand_type', '_on'=>'demand.id=order_comments.book_id'),
		
    ); 
}
