<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class ContractBidViewModel extends ViewModel{
      
      public $viewFields = array(    
           'contract'=>array('id'=>'contract_id','_type'=>'LEFT'),	  
          'bid'=>array('*','_on'=>'bid.id=contract.bid_id'), 
          
        ); 
}
