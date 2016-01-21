<?php
namespace Common\Widget;
use Think\Controller;
/**
*日历价格（价格设置，秒杀设置）
*$product_id   对应的产品id
*$id 
**/
class ProductPriceWidget extends Controller {
    public function index($product_id,$Month='',$moudel=''){	
	    if(!$product_id){$product_id=0;}	
		$map['product_id']=$product_id; 
		
		$result=get_result('product_price',$map);
		
		if($Month){
		$Montht = $Month;
		}else{
		$Montht = date('Y-m-d');
		}
		
		$Monthts = date('t', strtotime($Montht));
		
		 
		foreach($result as $k=> $row){		
 			
		 	
		   $data[] = array( 
				'id' => $row['id'],//事件id 
				'title' => $row['type1_price'],//事件标题 
				'start' => date('Y-m-d',$row['day']),//事件开始时间
				'name'  => date('Y-m-d H:i',$row['day']),//事件开始时间
				'textColor'=>'#0FDECF' //事件的背景色 
			); 
		} 	
		
		for($im=1;$im<=$Monthts;$im++){		 	
		
		    $map['day']=strtotime(date('Y-m-d',$row['day'])); 			
 			$description_info=get_info('product_description',$map);
 			
		   if($description_info){
			   $data[] = array( 
					'id' => $description_info['id'],//事件id 
					'title' => $row['type1_price'],//事件标题 
					'start' => date('Y-m-d',$description_info['day']),//事件开始时间
					'name'  => date('Y-m-d H:i',$description_info['day']),//事件开始时间
					'textColor'=>'#0FDECF' //事件的背景色 
				); 
			}	
			else{	
			
				$data[] = array( 
					'id' => '0',//事件id 
					'title' => '设定价格',//事件标题 
					'start' => date('Y-m', strtotime($Montht))."-".$im,//事件开始时间
					'name'  => date('Y-m', strtotime($Montht))."-".$im,//事件开始时间
					'textColor'=>'#0FDECF' //事件的背景色 
				); 	
				
			}
					 
		}
		

		$datas['data'] = json_encode($data); 
		$this->assign($map);
		$this->assign($datas);
        $this->display(T('Common@Widget/ProductPrice/index'));
    }
   
}

/*
	试图调用方法
	{:W('Common/ProductPrice/index')} 
*/
?>