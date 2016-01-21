<?php
namespace Common\Widget;
use Think\Controller;
/**
 * Description of Professionwidget
 * 专业选择插件
 * @author Auser
 */
class ProfessionWidget extends Controller
{
	
	/*
       * $param 参数 
       * 
       * 模板中调用方法 {:W('Common/Profession/index')} 
       */
	public function index($type = 1, $city = 0, $university = 0, $college = 0, $major = 0, $is_share = 0, $e = array())
	{
		if ('' == $type)
		{
			$type = 1;
		}
		
		//专业类型
		$profession_type = array (
			'1' => '非统考', 
			'2' => '统考', 
			'3' => '公共课' 
		)//'4' => '专业圈' 
		;
		//获取所有学校
		

		$data ['type'] = $type;
		$data ['city'] = $city;
		$data ['university'] = $university;
		$data ['college'] = $college;
		$data ['major'] = $major;
		$data ['profession_type'] = $profession_type;
		
		$this->assign ( $data );
		$this->display ( T ( 'Common@Widget/Profession/index' ) );
	
	}
	
	public function get_school_arr($pid = 0, $type = 1)
	{
		$map ['pid'] = $pid;
		$map ['type'] = $type;
		
		$model = M ( 'school' );
		$result = $model->where ( $map )->select ();
		return $result;
	}
	
	//获取学校
	public function getSchool()
	{
		
		$listSchool = get_field ( 'school', array (
			'status' => 1 
		), 'id,path,code,title,address,telephone,website,postal_code,type,pid,initials' );
		
		return $listSchool;
	}

}
