<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class ExtendViewModel extends ViewModel{
      
      public $viewFields = array(     
          'extend'=>array('*','_type'=>'LEFT'),
          'member'=>array("nickname","balance",'_on'=>'extend.uid=member.id'),
          ); 
}
