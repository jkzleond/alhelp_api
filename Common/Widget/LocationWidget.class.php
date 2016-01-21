<?php
namespace Common\Widget;
use Think\Controller;
class LocationWidget extends Controller {
    public function index($language_id=2){
    	$map=array(
    		'language_id'=>$language_id
    	);
    	$result=get_result('location_description',$map);
    	$continent=array();
    	$country=array();
    	$city=array();
        $location_id=array();
    	foreach($result as $row){
    		if($row['continent']==''){
    			continue;
    		}
    		$continent[$row['continent']]=$row['continent'];
    		$country[$row['continent']][]=$row['country'];
    		$city[$row['country']][]=$row['city'];
            $location_id[$row['city']]=$row['location_id'];
    	}

    	$new_country=array();
    	foreach($continent as $row){
    		$new_country[$row]=array_unique($country[$row]);
    	}

    	$data['continent_json']=json_encode($continent);
    	$data['country_json']=json_encode($new_country);
    	$data['city_json']=json_encode($city);
        $data['location_id']=json_encode($location_id);

    	$this->assign($data);
        $this->display(T('Common@Widget/Location/index'));
    }
}

/*
	试图调用方法
	{:W('Common/Location/index',array($language_id))} 
*/
?>