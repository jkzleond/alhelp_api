<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class OrderCommentViewModel extends ViewModel{
      
     public $viewFields = array(     
          'order_comments'=>array('*'),    
          'member'=>array('nickname', '_on'=>'order_comments.member_id=member.id'),
     ); 
}
