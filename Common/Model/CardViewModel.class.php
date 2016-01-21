<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class CardViewModel extends ViewModel{
      
      public $viewFields = array(     
          'card'=>array('*','_type'=>'LEFT'),    
          'member'=>array('nickname','serve_num','demand_num','fans_num','question_num','follow_num','_on'=>'card.member_id=member.id'),
          ); 
}
