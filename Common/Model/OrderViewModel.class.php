<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of OrderView
 * 订单模型框里
 * @author Auser
 */
class OrderViewModel extends ViewModel{
      
      public $viewFields = array(     
          'orders'=>array('*','_type'=>'LEFT'),    
          'order_product'=>array('id','type','order_id','table_name','table_id','title','price','quantity', '_on'=>'orders.id=order_product.order_id','_type'=>'LEFT'),
		  'bid'=>array('status'=>'bid_status','_on'=>'bid.id=order_product.table_id','_type'=>'LEFT'), 
		  'member'=>array('nickname', 'qq','_on'=>'member.id=orders.from_member_id'),    
      );
      
}
