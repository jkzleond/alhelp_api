<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class DemandBidModel extends ViewModel{
      
      public $viewFields = array(     
          'bid'=>array('id'=>'bid_id','content','demand_id','phone','price','qq'=>'bid_qq','add_time'=>'addtime','_type'=>'LEFT'),
          'demand'=>array("*",'_on'=>'bid.service_demand_id=demand.id'),
          ); 
}
