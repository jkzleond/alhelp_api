<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class OrderCommentBookModel extends ViewModel{
      
    public $viewFields = array(     
        'order_comments'=>array('*'),  
		'book'=>array('title'=>'description','_on'=>'book.id=order_comments.book_id'), 
		'member'=>array('nickname'=>'from_member_name','_on'=>'member.id=order_comments.member_id'), 
		'user'=>array('_table'=>'member','nickname'=>'to_member_name','_on'=>'user.id=order_comments.to_member_id'),
		
    ); 
}
