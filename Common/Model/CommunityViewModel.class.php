<?php
namespace Common\Model;
use Think\Model\ViewModel;
/**
 * Description of CommunityModel
 *
 * @author Auser
 */
class CommunityViewModel extends ViewModel{
   
      public $viewFields = array(     
          'community'=>array('id','member_id','table_type','table_id','default_cir','status','add_time','_type'=>'LEFT'),    
          'card'=>array('signature','nickname','content','type'=>'card_type', '_on'=>'community.member_id=card.member_id','_type'=>'LEFT'),
         
        );
      
     
      
      
}
