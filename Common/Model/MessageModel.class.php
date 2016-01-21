<?php
namespace Common\Model;
use Think\Model\ViewModel;
/**
 * Description of MessageModel
 *
 * @author Auser
 */
class MessageModel extends ViewModel{
      
    public $viewFields = array(     
          'message'=>array('*','_type'=>'LEFT'),
          'member'=>array("level","nickname","praise_num",'_on'=>'message.from_member_id=member.id'),
          ); 
}
