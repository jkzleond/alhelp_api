<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class InformationViewModel extends ViewModel{
      
     public $viewFields = array(     
          'information'=>array('id','cover','score','title'),    
         
     ); 
}
