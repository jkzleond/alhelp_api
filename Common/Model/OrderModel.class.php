<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of OrderView
 * 订单模型框里
 * @author Auser
 */
class OrderModel extends ViewModel{
      
      public $viewFields = array(     
          'orders'=>array('id','total','to_member_id','order_number','_type'=>'LEFT'),    
          'order_product'=>array('type','order_id','table_name','table_id','_on'=>'order_product.order_id=orders.id'),
		 
      );
      
}
