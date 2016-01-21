<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class FollowModel extends ViewModel{
      
      public $viewFields = array(     
          'follow'=>array('id','from_member_id','to_member_id','status','add_time'),    
          'member'=>array('nickname'=>'from_nickname', '_on'=>'follow.from_member_id=member.id','_type'=>'LEFT'),
          'users'=>array('_table'=>"member",'nickname'=>'to_nickname','_as'=>'users', '_on'=>'follow.to_member_id=users.id','_type'=>'LEFT'),
          ); 
}
