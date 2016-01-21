<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class ExtendRewardViewModel extends ViewModel{
      
      public $viewFields = array(     
          'extend_reward'=>array('id','from_member_id','to_member_id','role','table_name','table_id','status','add_time','set_name','_type'=>'LEFT'),
		  'setting'=>array('set_val'=>'coin','remark','_on'=>'setting.set_name=extend_reward.set_name','_type'=>'LEFT'),
          'member'=>array("nickname"=>'from_member_name','_on'=>'member.id=extend_reward.from_member_id'),
		  'user'=>array('_table'=>'member',"nickname"=>'to_member_name','_on'=>'user.id=extend_reward.to_member_id'),
       ); 
}
