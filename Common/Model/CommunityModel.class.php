<?php
namespace Common\Model;
use Think\Model\ViewModel;
/**
 * Description of CommunityModel
 *
 * @author Auser
 */
class CommunityModel extends ViewModel{
   
      public $viewFields = array(     
          'community'=>array('id','member_id','table_type','table_id','default_cir','status','add_time'),    
          'card'=>array('signature','nickname','content','type'=>'card_type', '_on'=>'community.member_id=card.member_id','_type'=>'LEFT'),
          'member'=>array('avatar','email', '_on'=>'community.member_id=member.id','_type'=>'LEFT'),
        );
      
      //根据圈子id 查找所在的城市 学校  学院  
      public function sel_circel($id){
          $result = array(1=>'university',2=>'college',3=>'major_code',4=>'major');
          if(empty($id)){
              return false;
          }
          $res = M('community')->find($id);
          if(!empty($res)){
              return false;
          }
          $file=$result[$res['critype']];
          if($res['table_type']=='school'){
              
              
          }else{
              
          }
          
      }
      
      
}
