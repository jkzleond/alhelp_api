<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class CommunityContentViewModel extends ViewModel{
      
     public $viewFields = array(     
        'community_content'=>array('*','_type'=>'LEFT'),   
         'member'=>array('nickname', '_on'=>'community_content.member_id=member.id'),
        
         
     );
      
}
