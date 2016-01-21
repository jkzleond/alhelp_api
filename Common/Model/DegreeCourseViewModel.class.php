<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class DegreeCourseViewModel extends ViewModel{
      
      public $viewFields = array(   
          'school'=>array('_type'=>'LEFT'),    	  
          'degree_course'=>array('*','_on'=>"degree_course.code=substring(school.code,1,4)"),    
         
          ); 
}
