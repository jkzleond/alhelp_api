<?php

namespace Api\Controller;

class MessageController extends ApiBaseController {
	// protected $is_check_token = true;
	public function message_list_get() {
		$this->check_token ();
		$type = intval ( I ( 'get.type' ) );
		$page = intval ( I ( 'get.page' ) );
		$page = empty ( $page ) ? 1 : $page;
		
		$uid = $this->uid;
		$map ['status'] = 1;
		$map ['from_member_id'] = 0;
		$map ['to_member_id'] = $uid;
		// $map ['_string'] = " message.table is not null ";
		
		$options = array (
				"alias" => "sl",
				"where" => $map,
				"field" => "sl.*",
				"order" => " add_time desc" 
		);
		
		$_GET ["p"] = $page;
		$model = M ( 'message' );
		$total = $model->where ( $options ['where'] )->count ();
		
		$pageCount = ceil ( $total / $this->pageSize );
		if ($pageCount < $_GET ['p']) {
			$_GET ['p'] = $pageCount;
		}
		
		$pageObj = new \Think\Page ( $total, $this->pageSize );
		
		$options ['limit'] = $pageObj->firstRow . ',' . $pageObj->listRows;
		
		$model->setProperty ( 'options', $options );
		
		$row = $model->select ();
		
		// int_to_string ( $row, array (
		// 'type' => array (
		// 'coin' => '获得金币',
		// '_coin' => '消耗金币'
		// )
		// ) );
		// if (IsDebug()) {
		// echo $model->getLastSql();
		// }
		
		// $coin = M ( 'member' )->getFieldById ( $uid, 'coin' );
		$data = array (
				'list' => $row,
				'count' => $total,
				'next_page' => null 
		);
		if ($pageCount > 1 && $page < $pageCount) {
			$data ['next_page'] = $this->url ( '/v1/message/list/' . type . '/' . ++ $page );
		}
		$this->success ( $data );
	}
}