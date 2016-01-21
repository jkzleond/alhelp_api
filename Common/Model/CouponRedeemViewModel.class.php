<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of demandCommentViewModel
 * 评论视图模型
 * @author Auser
 */
class CouponRedeemViewModel extends ViewModel{
      
     public $viewFields = array(     
          'coupon_redeem'=>array('*'),    
          'member'=>array('nickname', '_on'=>'member.id=coupon_redeem.member_id'),
		  'coupon_code'=>array('price','code','_on'=>'coupon_code.id=coupon_redeem.cid'),  
          'coupon'=>array('title','_on'=>'coupon.id=coupon_code.cid'),    		  
     ); 
}
