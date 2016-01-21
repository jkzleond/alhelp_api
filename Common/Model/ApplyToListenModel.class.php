<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ApplyToListenModel extends ViewModel{
      
     public $viewFields = array(     
          'apply_to_listen'=>array('*'),    
          'demand'=>array('id'=>'did','member_id'=>'service_demand_id','role_type','member_name','demand_type','profes_type','description','content_demand','cost','city','university','college','major_code','praise_num','major','mobile','qq', '_on'=>'apply_to_listen.demand_id=demand.id'),
		  'member'=>array('nickname'=>'b_nickname','phone','QQ', '_on'=>'apply_to_listen.member_id=member.id'),
         
     );
      
}
