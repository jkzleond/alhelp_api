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
        'member'=>array('nickname'=>'member_name', '_on'=>'member.id=order_comments.member_id','_type'=>'LEFT'),
		'order_product'=>array('table_id', '_on'=>'order_product.id=order_comments.order_id','_type'=>'LEFT'),
		'orders'=>array('to_member_id', '_on'=>'orders.id=order_product.order_id','_type'=>'LEFT'),
		
		'demand'=>array('description','university','college','major','city','demand_type', '_on'=>'demand.id=order_product.table_id'),
    ); 
}
