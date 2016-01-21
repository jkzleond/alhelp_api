<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of OrderView
 * 订单模型框里
 * @author Auser
 */
class OrderMemberViewModel extends ViewModel{
      
      public $viewFields = array(     
          'orders'=>array('*','_type'=>'LEFT'),    
          'member'=>array('nickname'=>'from_member_name','_on'=>'member.id=orders.from_member_id','_type'=>'LEFT'),
		  'user'=>array('_table'=>'member','nickname'=>'to_member_name','_on'=>'user.id=orders.to_member_id'),
		 
      );
      
}
