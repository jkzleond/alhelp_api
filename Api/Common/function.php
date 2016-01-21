<?php
/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 * '字段名1'=>array(映射关系数组),
 * '字段名2'=>array(映射关系数组),
 * ......
 * )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 * array(
 * array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 * ....
 * )
 *
 */
function int_to_string(&$data, $map = array('gender'=>array(1=>'男',0=>'女'),'status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿')))
{
	if ($data === false || $data === null)
	{
		return $data;
	}
	$data = ( array ) $data;
	foreach ( $data as $key => $row )
	{
		foreach ( $map as $col => $pair )
		{
			if (isset ( $row [$col] ) && isset ( $pair [$row [$col]] ))
			{
				$data [$key] [$col . '_text'] = $pair [$row [$col]];
			}
		}
	}
	return $data;
}