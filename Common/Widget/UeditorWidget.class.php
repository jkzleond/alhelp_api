<?php
namespace Common\Widget;
use Think\Controller;
class UeditorWidget extends Controller {
    public function index($editor_id='test',$content){
    	$data['editor_id']=$editor_id;
    	$data['content']=$content;
    	$this->assign($data);
        $this->display(T('Common@Widget/Ueditor/index'));
    }
}

/*
	试图调用方法
	{:W('Common/Ueditor/index')} 
*/
?>