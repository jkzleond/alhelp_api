<?php
namespace Common\Widget;
use Think\Controller;
class CurrencyWidget extends Controller {
    public function index($currency=''){
    	$data['result']=get_result('currency');
    	$data['currency']=$currency;
    	$this->assign($data);
        $this->display(T('Common@Widget/Currency/index'));
    }
}

/*
	试图调用方法
	{:W('Common/Currency/index')} 
*/
?>