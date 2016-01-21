<?php
namespace Common\Widget;
use Think\Controller;
class PositionWidget extends Controller {
    public function index($product_id){
    	$data['result']=get_result('product_position',array('product_id'=>$product_id));
    	$this->assign($data);
        $this->display(T('Common@Widget/Position/index'));
    }
}

/*
	试图调用方法
	{:W('Common/Position/index')} 
*/
?>