<?php
/**
 * Description of CategoryModel
 *
 * @author zhuangjian
 */
namespace Common\Model;
use Think\Model\ViewModel;
class ListenViewModel extends ViewModel{
    public $viewFields = array(
        'listen'=>array('*','_type'=>"LEFT"),
        'category'=>array('title'=>'c_title','_on'=>'category.id=listen.cid'),   
      );
}
