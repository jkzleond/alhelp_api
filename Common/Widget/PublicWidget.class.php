<?php
namespace Common\Widget;
use Think\Controller;

class PublicWidget extends Controller{
    
    /*潜水模板插件
    *视图调用 {:W('Common/Public/diving'))}
    *
    */
    public function diving(){
        $this->display(T('Common@Widget/Public/diving'));
    }
    
}
