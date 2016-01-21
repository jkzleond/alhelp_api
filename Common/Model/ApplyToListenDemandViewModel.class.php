<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ApplyToListenDemandViewModel extends ViewModel{
      
     public $viewFields = array(     
          'apply_to_listen'=>array('*'),   
          'demand'=>array('demand_type', '_on'=>'apply_to_listen.demand_id=demand.id'),
     );
      
}
