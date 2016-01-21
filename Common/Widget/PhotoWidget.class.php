<?php
namespace Common\Widget;
use Think\Controller;
class PhotoWidget extends Controller {
    public function index($info){
        $this->display(T('Common@Widget/Photo/index'));
    }
}

/*
	试图调用方法
	{:W('Common/Photo/index',array($info))} 
*/
?>