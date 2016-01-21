<?php
/**
 * Description of CategoryModel
 *
 * @author zhuangjian
 */
namespace Home\Model;
use Think\Model\ViewModel;
class CommentViewModel extends ViewModel{
    public $viewFields = array(
        'comment'=>array('id'=>'com_id','pid'=>'com_pid','table'=>'com_tablename','table_id'=>'com_table_id','content'=>'com_content','status'=>'com_status','add_time'=>'com_adtime','_type'=>"LEFT"),
        'member'=>array('nickname','id'=>'member_id','_on'=>'member.id=comment.member_id'),   
      );
}
