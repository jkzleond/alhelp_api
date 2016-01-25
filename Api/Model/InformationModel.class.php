<?php

namespace Api\Model;

use Think\Model;

class InformationModel extends BaseModel {
	protected $page_size = 20;
	protected $tableName = 'information';

	public function article($id) {
		$data = $this->where ( array (
				'id' => $id 
		) )->find ();
		$attr = $this->getFile ( $data ['id'] );
		if (empty ( $attr )) {
			$attr = null;
		}
		$data ['getAttachment'] = $attr;
		$comment = M ( 'comment' );
		$row = $comment->alias ( 'c' )->join ( "left join member m on m.id=c.member_id" )->where ( array (
				'c.table' => 'information',
				'c.table_id' => $data ['id'],
				'c.status' => 1 
		) )->field ( "c.*,m.nickname" )->order ( 'add_time desc' )->select ();
		if (empty ( $row )) {
			$row = null;
		}
		$data ['comments'] = $row;
		// echo $comment->getLastSql();
		return $data;
	}

	public function articles($type = 'information', $page_num = 1, $uid = null, $index = false) {
		if ($index) {
			$page_num = 1;
			$this->page_size = 3;
		}
		
		$page_num = ($page_num == 0 ? 1 : $page_num);
		
		$condition = array (
				'member_id' => $uid,
				'type' => $type 
		);
		if (! $uid) {
			unset ( $condition ['member_id'] );
		}
		
		$_GET ["p"] = $page_num;
		
		$options = array (
				'where' => $condition 
		);
		
		$this->options = $options;
		$count = $this->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc',
				"field" => "id,title,type,add_time,update_time,cover,school_id
				,member_id,member_nickname,profes_type,city,university,college,
				major,major_code,collect_num" 
		);
		
		$data = $this->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		if ($type == 'group') {
			/*
			 * foreach ($data AS &$row){
			 * //获取附件
			 * $row ['getAttachment'] = $this->getFile ( $row['id'] );
			 * }
			 */
		}
		
		if ($index) {
			return $data;
		}
		
		$page_count = ceil ( $page->totalRows / $page->listRows );
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($page_count > 1 && $page_num < $page_count) {
			if ($uid) {
				$data ['next_page'] = $this->url ( '/v1/articles/self/' . $type . '/page/' . ++ $page_num );
			} else {
				$data ['next_page'] = $this->url ( '/v1/articles/' . $type . '/page/' . ++ $page_num );
			}
		}
		
		return $data;
	}

	private function getFile($id) {
		if (empty ( $id )) {
			return false;
		}
		$map ['table_id'] = $id;
		$map ['table'] = 'information';
		return get_info ( 'attachments', $map );
	}
}