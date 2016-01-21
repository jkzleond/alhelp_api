<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class BidViewModel extends ViewModel{
      
      public $viewFields = array(     
          'bid'=>array('*','_type'=>'LEFT'),    
          'demand'=>array('id'=>'did','member_id','role_type','member_name','demand_type','profes_type','description','content_demand','cost','city','university','college','major_code','praise_num','major', '_on'=>'bid.demand_id=demand.id'),
          'member'=>array('nickname'=>'b_nickname','qq'=>'demand_qq','phone'=>'demand_phone','email'=>'b_email','_on'=>'bid.demand_member_id=member.id'),
          ); 
}
