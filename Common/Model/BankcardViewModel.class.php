<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class BankcardViewModel extends ViewModel{
      
      public $viewFields = array(     
          'bankcard'=>array('*','_type'=>'LEFT'),
          'member'=>array("nickname","balance",'_on'=>'bankcard.member_id=member.id'),
          ); 
}
