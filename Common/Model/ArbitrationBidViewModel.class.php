<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ArbitrationBidViewModel extends ViewModel{
      
     public $viewFields = array(     
          'arbitration'=>array('id'=>'a_id','content','remark'),   
          'orders'=>array('bid_id','status','add_time','_on'=>'orders.id=arbitration.order_id'),
		  'bid'=>array('_on'=>'bid.id=orders.bid_id'),
		  'user'=>array('_table'=>'member','nickname'=>'stu_name','_on'=>'user.id=orders.from_member_id'),
		  'member'=>array('nickname'=>'tea_name','_on'=>'user.id=orders.to_member_id'),
		  'demand'=>array('city','university','college','major','description','_on'=>'demand.id=bid.service_demand_id'),
		   
     );
      
}
