<?php
namespace Common\Widget;
use Think\Controller;
/***
*选择商品插件
*
*/
class GoodsWidget extends Controller {
    
    public function index(){
    	
        $this->display(T('Common@Widget/Goods/index'));
    }
}
/**
* 调用方法
* {:W('Common/Goods/index')} 
*
*/
