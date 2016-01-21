<?php
namespace Common\Model;
use Think\Model\ViewModel;

/**
 * Description of FollowModel
 * 关注视图模型
 *
 * @author Auser
 */
class OrderDocViewModel extends ViewModel{
      
    public $viewFields = array(     
          'order_product'=>array('*','_type'=>'LEFT'),    
          'demand'=>array('id'=>'did','member_id','role_type','member_name','demand_type','profes_type','description','content_demand','cost','city','university','college','major_code','praise_num','major', '_on'=>'demand.id=order_product.table_id','_type'=>'LEFT'),
          'orders'=>array('from_member_id', '_on'=>'orders.id=order_product.order_id','_type'=>'LEFT'),
		  'member'=>array('nickname'=>'b_nickname','email'=>'b_email','_on'=>'member.id=orders.from_member_id'),
    ); 
}
