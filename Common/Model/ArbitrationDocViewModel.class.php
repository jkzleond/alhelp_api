<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ArbitrationDocViewModel extends ViewModel{
      
     public $viewFields = array(     
          'refund'=>array('id','order_id','refund_reason','refund_reason','refund_project','refund_price','refund_trans','refund_status','status','agree_time','is_verify'),   
          'orders'=>array('from_member_id','to_member_id','order_number','total','_on'=>'orders.id=refund.order_id'),
		  'member'=>array('nickname'=>'stu_name','_on'=>'member.id=orders.from_member_id'),
		  'user'=>array('_table'=>'member','nickname'=>'tea_name','_on'=>'user.id=orders.to_member_id'),
		   
     );
      
}
