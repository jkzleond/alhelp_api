<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ApplyToListenViewModel extends ViewModel{
      
     public $viewFields = array(     
          'apply_to_listen'=>array('*'),   
          'member'=>array('nickname','praise_num','fans_num','_on'=>'apply_to_listen.member_id=member.id'),
     );
      
}
