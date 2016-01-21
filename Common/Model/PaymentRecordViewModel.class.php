<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class PaymentRecordViewModel extends ViewModel{
      
     public $viewFields = array(     
         'payment_record'=>array('*','_type'=>'LEFT'),   
         'member'=>array('nickname'=>'from_member_name', '_on'=>'member.id=payment_record.from_member_id','_type'=>'LEFT'),
		 'user'=>array('_table'=>'member','nickname'=>'to_member_name', '_on'=>'user.id=payment_record.to_member_id'),
        
         
     );
      
}
