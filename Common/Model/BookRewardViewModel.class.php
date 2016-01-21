<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class BookRewardViewModel extends ViewModel{
      
      public $viewFields = array(     
          'book'=>array('id','member_id','cover','coin'=>'score','title'),    
         
          ); 
}
