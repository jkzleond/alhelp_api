<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class BidStudyViewModel extends ViewModel{
      
      public $viewFields = array(     
          'bid'=>array('*'),    
          'learning_periods'=>array('member_id'=>'learn_member_id','title','start_time','end_time','price','add_time', '_on'=>'bid.id=learning_periods.bid_id'),
          ); 
}
