<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class EssayViewModel extends ViewModel{
      
     public $viewFields = array(     
         'essay'=>array('*','_type'=>'LEFT'),   
         'essay_type'=>array('is_general', '_on'=>'essay_type.id=essay.eid'),
        
         
     );
      
}
