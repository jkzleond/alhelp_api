<?php
namespace Common\Widget;
use Think\Controller;
class LanguageWidget extends Controller {
    public function index($action,$parameter=array(),$language_id=''){
    	$data['result']=get_result('language');
    	$data['action']=$action;
    	$data['parameter']=$parameter;
    	$data['language_id']=$language_id;
    	$this->assign($data);
        $this->display(T('Common@Widget/Language/index'));
    }
}

/*
	试图调用方法
	{:W('Common/Language/index')} 
*/
?>