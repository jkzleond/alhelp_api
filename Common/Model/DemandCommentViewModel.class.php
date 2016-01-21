<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class DemandCommentViewModel extends ViewModel{
      
     public $viewFields = array(     
          'demand_comment'=>array('*'),    
          'member'=>array('nickname', '_on'=>'demand_comment.member_id=member.id'),
     ); 
}
