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

/**
 * 发送http请求
 * @param $url
 * @param $data
 * @param $headers
 * @param $cookie
 * @return array|null
 */
function urlopen($url, $data, $headers=null, $cookie=null) {
	$ch = curl_init();
	$data_str = '';

	if (is_array($data)) {
		foreach ( $data as $key => $value ) {
			$data_str .= $key.'='.$value.'&';
		}
		$data_str = rtrim($data_str, '&');
	} else {
		$data_str = $data;
	}


	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER  => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HTTPHEADER => !empty($headers) ? $headers : array(),
		CURLOPT_POST => !empty($data) ? 1 : 0,
		CURLOPT_POSTFIELDS => $data_str,
		CURLOPT_COOKIE => $cookie
	));

	$response = curl_exec($ch);
	curl_close($ch);
	if (!$response) {
		return null;
	}
	$resp_arr = array();
	$parts = explode("\r\n\r\n", $response);
	$resp_arr['header'] = $parts[0];
	$resp_arr['body'] = $parts[1];
	preg_match('/HTTP.* (?P<status>\d{3}) .*/Ui', $resp_arr['header'], $match);
	$resp_arr['status_code'] = $match['status'];
	return $resp_arr;
}