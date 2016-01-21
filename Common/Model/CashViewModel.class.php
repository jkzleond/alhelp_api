<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class CashViewModel extends ViewModel{
      
      public $viewFields = array(     
          'cash'=>array('*','_type'=>'LEFT'),
		  'bankcard'=>array('card_name','card_num','card_type','_on'=>'cash.card_id=bankcard.id'),
          'member'=>array("nickname",'balance'=>'total','balance_frozen','_on'=>'cash.member_id=member.id'),
          ); 
}
