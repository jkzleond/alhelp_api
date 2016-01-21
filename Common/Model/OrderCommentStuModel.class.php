<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class OrderCommentStuModel extends ViewModel{
      
    public $viewFields = array(     
        'order_comments'=>array('*'),  
		'orders'=>array('to_member_id','_on'=>'orders.id=order_comments.order_id'), 
		//'order_product'=>array('table_name','table_id','_on'=>'order_product.id=order_comments.order_id'), 
		//'demand'=>array('description','member_name','university','college','major','city','demand_type', '_on'=>'order_comments.book_id=demand.id'),
    ); 
}
