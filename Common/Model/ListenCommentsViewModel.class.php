<?php
namespace Common\Model;
use Think\Model\ViewModel;


/**
 * Description of ApplyToListenViewModel
 *
 * @author Auser
 */
class ListenCommentsViewModel extends ViewModel{
    public $viewFields = array(  
	    'listen_comments'=>array('id','demand_id','rating','pid','member_id','content','add_time','aid'=>'order_id','_type'=>'LEFT'),   
        'member'=>array('nickname','_on'=>'member.id=listen_comments.member_id'),    
    );
      
}
