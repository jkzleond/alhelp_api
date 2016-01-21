<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class BidModel extends ViewModel{
      
    public $viewFields = array(     
          'bid'=>array('*','_type'=>'LEFT'),    
          'demand'=>array('id'=>'did','member_id','role_type','member_name','demand_type','profes_type','description','content_demand','cost','city','university','college','major_code','praise_num','major', '_on'=>'demand.id=bid.service_demand_id','_type'=>'LEFT'),   
		  'member'=>array('nickname'=>'member_name', '_on'=>'member.id=bid.service_member_id'),    
    ); 
}
