<?php

namespace Api\Model;

use Think\Model;

class Demand2Model extends BaseModel {
	protected $page_size = 10;
	protected $tableName = 'demand';

	public function demands($demand_type = 1, $role_type = 1, $page_num = 1, $uid = null) {
		$page_num = ($page_num == 0 ? 1 : $page_num);
		if (!$uid) {
			$condition = array (
					'demand_type' => $demand_type,
					'role_type' => $role_type 
			);
		} else {
			$condition = array (
					'member_id' => $uid,
					'demand_type' => $demand_type,
					'role_type' => $role_type 
			);
		}
		if ($demand_type === 0) {
			$condition ['demand_type'] = array (
					'in',
					'1,2,3' 
			);
		}
		if ($role_type === 0) {
			unset ( $condition ['role_type'] );
		}
		
		$_GET ["p"] = $page_num;
		$condition['status'] = array('neq',-1);
		
		$options = array (
				'where' => $condition 
		);
		
		$this->options = $options;
		$count = $this->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc' 
		);
		$data = $this->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		foreach ( $data as &$demand ) {
			$demand ['city_string'] = M ( 'school' )->getFieldById ( $demand ['city'], 'title_fix' );
			$demand ['university_string'] = M ( 'school' )->getFieldById ( $demand ['university'], 'title_fix' );
			$demand ['college_string'] = M ( 'school' )->getFieldById ( $demand ['college'], 'title_fix' );
			$demand ['major_string'] = M ( 'school' )->getFieldById ( $demand ['major'], 'title_fix' );
			$demand ['avator'] = GetSmallAvatar ( $demand ['member_id'] );

			//备注
			$profes_type = $demand['profes_type'];
			if($profes_type == 1){
				$demand['memo'] = '非统考 '.$demand['university_string'].' '.$demand['college_string'].' '.$demand['major_string'];
			}elseif($profes_type == 2){
				$demand['memo'] = '统考 '.$demand['major_string'];
			}elseif($profes_type == 3){
				$demand['memo'] = '公共课';
			}
		}
		$page_count = ceil ( $page->totalRows / $page->listRows );
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		if ($page_count > 1 && $page_num < $page_count) {
			if ($uid) {
				$data ['next_page'] = $this->url ( '/v1/demands/self/' . $demand_type . '/role/' . $role_type . '/page/' . ++ $page_num );
			} else {
				$data ['next_page'] = $this->url ( '/v1/demands/' . $demand_type . '/role/' . $role_type . '/page/' . ++ $page_num );
			}
		}
		return $data;
	}

	public function get_by_id($id) {
		$this_demand = $this->where ( array (
				'id' => $id 
		) )->find ();
		
		if (! $this_demand) {
			return 5997;
		}
		$this_demand ['city_string'] = M ( 'school' )->getFieldById ( $this_demand ['city'], 'title_fix' );
		$this_demand ['university_string'] = M ( 'school' )->getFieldById ( $this_demand ['university'], 'title_fix' );
		$this_demand ['college_string'] = M ( 'school' )->getFieldById ( $this_demand ['college'], 'title_fix' );
		$this_demand ['major_string'] = M ( 'school' )->getFieldById ( $this_demand ['major'], 'title_fix' );
		$this_demand_comments = M ( 'demand_comment' )->where ( array (
				'demand_id' => $id 
		) )->select ();
		$this_demand_explains = M ( 'demand_explain' )->where ( array (
				'demand_id' => $id 
		) )->select ();
		$this_demand ['comments'] = $this_demand_comments;
		$this_demand ['explains'] = $this_demand_explains;
		
		$this_demand ['avator'] = GetSmallAvatar ( $this_demand ['member_id'] );

		//备注
		$profes_type = $this_demand['profes_type'];
		if($profes_type == 1){
			$this_demand['memo'] = '非统考 '.$this_demand['university_string'].' '.$this_demand['college_string'].' '.$this_demand['major_string'];
		}elseif($profes_type == 2){
			$this_demand['memo'] = '统考 '. $this_demand['major_string'];
		}elseif($profes_type == 3){
			$this_demand['memo'] = '公共课';
		}

		return $this_demand;
	}

	public function collaborates($demand_id, $page_num = 1) {
		$page_num = ($page_num == 0 ? 1 : $page_num);
		
		$this_demand = M ( 'demand' )->where ( array (
				'id' => $demand_id 
		) )->find ();
		if (! $this_demand) {
			return 5997;
		}
		$uid = $this_demand ['member_id'];
		$role = $this_demand ['role_type'];
		$condition = array (
				'status' => 1 
		);
		if ($role == 1) {
			$condition ['to_member_id'] = $uid;
		} else {
			$condition ['from_member_id'] = $uid;
		}
		
		$_GET ["p"] = $page_num;
		
		$options = array (
				'where' => $condition 
		);
		
		$order = M ( 'orders' );
		
		$order->options = $options;
		$count = $order->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc' 
		);
		$data = $order->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		foreach ( $data as &$order ) {
			if ($role == 1) {
				$member_id = $order ['from_member_id'];
			} else {
				$member_id = $order ['to_member_id'];
			}
			$order ['member'] = M ( 'member' )->where ( array (
					'id' => $member_id 
			) )->find ();
			$order ['member'] ['avator'] = GetSmallAvatar ( $member_id );
		}
		
		$page_count = ceil ( $page->totalRows / $page->listRows );
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($page_count > 1 && $page_num < $page_count) {
			$data ['next_page'] = $this->url ( '/v1/demands/collaborates/' . $demand_id . '/page/' . ++ $page_num );
		}
		return $data;
	}

	public function listens_by_demand_id($demand_id, $page_num = 1) {
		$page_num = ($page_num == 0 ? 1 : $page_num);
		
		$this_demand = M ( 'demand' )->where ( array (
				'id' => $demand_id 
		) )->find ();
		if (! $this_demand) {
			return 5997;
		}
		
		$_GET ["p"] = $page_num;
		$condition = array (
				'demand_id' => $demand_id 
		);
		$options = array (
				'where' => $condition 
		);
		
		$order = M ( 'apply_to_listen' );
		
		$order->options = $options;
		$count = $order->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc' 
		);
		$data = $order->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		
		foreach ( $data as &$listen ) {
			$listen ['nickname'] = M ( 'member' )->getFieldById ( $listen ['member_id'], 'nickname' );
		}
		
		$page_count = ceil ( $page->totalRows / $page->listRows );
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($page_count > 1 && $page_num < $page_count) {
			$data ['next_page'] = $this->url ( '/v1/demand/listens/' . $demand_id . '/page/' . ++ $page_num );
		}
		return $data;
	}
	
	// 获取类似公开课，$public_id: 公开课id
	public function public_similar($public_id, $page_num = 1) {
		$this_public = M ( 'demand' )->where ( array (
				'id' => $public_id 
		) )->find ();
		if (! $this_public) {
			return 3023;
		}
		$_GET ["p"] = $page_num;
		$condition = array (
				'major' => $this_public ['major'],
				'demand_type' => 4 
		);
		$condition['status'] = array('neq',-1);
		$options = array (
				'where' => $condition 
		);
		
		$order = M ( 'demand' );
		
		$order->options = $options;
		$count = $order->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc' 
		);
		$data = $order->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		
		foreach ( $data as &$public ) {
			$public ['city_string'] = M ( 'school' )->getFieldById ( $public ['city'], 'title_fix' );
			$public ['university_string'] = M ( 'school' )->getFieldById ( $public ['university'], 'title_fix' );
			$public ['college_string'] = M ( 'school' )->getFieldById ( $public ['college'], 'title_fix' );
			$public ['major_string'] = M ( 'school' )->getFieldById ( $public ['major'], 'title_fix' );
			$public ['avator'] = GetSmallAvatar ( $public ['member_id'] );

			//备注
			$profes_type = $public['profes_type'];
			if($profes_type == 1){
				$public['memo'] = '非统考 '.$public['university_string'].' '.$public['college_string'].' '.$public['major_string'];
			}elseif($profes_type == 2){
				$public['memo'] = '统考 '.$public['major_string'];
			}elseif($profes_type == 3){
				$public['memo'] = '公共课';
			}
		}
		
		$page_count = ceil ( $page->totalRows / $page->listRows );
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($page_count > 1 && $page_num < $page_count) {
			$data ['next_page'] = $this->url ( '/v1/public/similar/' . $public_id . '/page/' . ++ $page_num );
		}
		return $data;
	}

	public function classify_list() {
		return M ( 'unified_classify' )->select ();
	}

	public function unified_list($classify_id) {
		$this_classify = M ( 'unified_classify' )->where ( array (
				'id' => $classify_id 
		) )->find ();
		if (! $this_classify) {
			return 3025;
		}
		$data = M ( 'unified' )->where ( array (
				'cid' => $classify_id 
		) )->select ();
		return $data;
	}

	public function unified_by_id($unified_id) {
		$this_unified = M ( 'unified' )->where ( array (
				'id' => $unified_id 
		) )->find ();
		if (! $this_unified) {
			return 3026;
		}
		return $this_unified;
	}

	public function get_by_unified($role, $demand_type, $classify_id, $unified_id, $page_num = 1) {
		$condition = array (
				'college' => $classify_id,
				'major' => $unified_id,
				'role_type' => $role,
				'demand_type' => $demand_type 
		);
		$condition['status'] = array('neq',-1);
		if ($classify_id == 0) {
			unset ( $condition ['college'] );
			unset ( $condition ['major'] );
		} else {
			$this_classify = M ( 'unified_classify' )->where ( array (
					'id' => $classify_id 
			) )->find ();
			if (! $this_classify) {
				return 3025;
			}
			
			if ($unified_id == 0) {
				$condition ['major'] = $classify_id;
			} else {
				$this_unified = M ( 'unified' )->where ( array (
						'id' => $unified_id 
				) )->find ();
				if (! $this_unified) {
					return 3026;
				}
				$condition ['major'] = $unified_id;
			}
		}
		
		if (! in_array ( $role, array (
				0,
				1,
				2 
		) )) {
			return 3027;
		}
		if ($role == 0) {
			unset ( $condition ['role_type'] );
		}
		if (! in_array ( $demand_type, array (
				0,
				1,
				2,
				3,
				4 
		) )) {
			return 3012;
		}
		if ($demand_type == 0) {
			unset ( $condition ['demand_type'] );
		}
		
		$page_num = ($page_num == 0 ? 1 : $page_num);
		
		$_GET ["p"] = $page_num;
		
		$options = array (
				'where' => $condition 
		);
		
		$demand = M ( 'demand' );
		
		$demand->options = $options;
		$count = $demand->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc' 
		);
		$data = $demand->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		foreach ( $data as &$demand ) {
			$demand ['city_string'] = M ( 'school' )->getFieldById ( $demand ['city'], 'title_fix' );
			$demand ['university_string'] = M ( 'school' )->getFieldById ( $demand ['university'], 'title_fix' );
			$demand ['college_string'] = M ( 'school' )->getFieldById ( $demand ['college'], 'title_fix' );
			$demand ['major_string'] = M ( 'school' )->getFieldById ( $demand ['major'], 'title_fix' );
			$demand ['member'] = M ( 'member' )->where ( array (
					'id' => $demand ['member_id'] 
			) )->find ();
			$demand ['avator'] = GetSmallAvatar ( $demand ['member_id'] );

			//备注
			$profes_type = $demand['profes_type'];
			if($profes_type == 1){
				$demand['memo'] = '非统考 '.$demand['university_string'].' '.$demand['college_string'].' '.$demand['major_string'];
			}elseif($profes_type == 2){
				$demand['memo'] = '统考 '.$demand['major_string'];
			}elseif($profes_type == 3){
				$demand['memo'] = '公共课';
			}
		}
		
		$page_count = ceil ( $page->totalRows / $page->listRows );
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($page_count > 1 && $page_num < $page_count) {
			$data ['next_page'] = $this->url ( '/v1/demands/by/classify/role/' . $role . '/type/' . $demand_type . '/classify/' . $classify_id . '/unified/' . $unified_id . '/page/' . ++ $page_num );
		}
		return $data;
	}

	public function get_by_school($role, $demand_type, $province_id, $university_id, $college_id, $page_num = 1) {
		$condition = array (
				'role_type' => $role,
				'city' => $province_id,
				'university' => $university_id,
				'college' => $college_id,
				'demand_type' => $demand_type 
		);
		$condition['status'] = array('neq',-1);
		
		// if ($province_id == 0) {
		// unset($condition['city']);
		// unset($condition['university']);
		// unset($condition['college']);
		// } else {
		// $this_city = M('school')->where(array('id' => $province_id))->find();
		// if (!$this_city) {
		// return 3008;
		// }
		
		// if ($university_id == 0) {
		// unset($condition['university']);
		// unset($condition['college']);
		// } else {
		// $this_university = M('school')->where(array('id' =>
		// $university_id))->find();
		// if (!$this_university) {
		// return 3009;
		// }
		// if ($college_id == 0) {
		// unset($condition['college']);
		// } else {
		// $this_college = M('school')->where(array('id' =>
		// $college_id))->find();
		// if (!$this_college) {
		// return 3010;
		// }
		// }
		// }
		// }
		
		if (empty ( $province_id )) {
			unset ( $condition ['city'] );
		} else {
			$this_city = M ( 'school' )->where ( array (
					'id' => $province_id 
			) )->find ();
			if (! $this_city) {
				return 3008;
			}
		}
		
		if (empty ( $university_id )) {
			unset ( $condition ['university'] );
		} else {
			$this_university = M ( 'school' )->where ( array (
					'id' => $university_id 
			) )->find ();
			if (! $this_university) {
				return 3009;
			}
		}
		if ($college_id == 0) {
			unset ( $condition ['college'] );
		} else {
			$this_college = M ( 'school' )->where ( array (
					'id' => $college_id 
			) )->find ();
			if (! $this_college) {
				return 3010;
			}
		}
		
		if (! in_array ( $role, array (
				0,
				1,
				2 
		) )) {
			return 3027;
		}
		if ($role == 0) {
			unset ( $condition ['role_type'] );
		}
		if (! in_array ( $demand_type, array (
				0,
				1,
				2,
				3,
				4 
		) )) {
			return 3012;
		}
		if ($demand_type == 0) {
			unset ( $condition ['demand_type'] );
		}
		
		$page_num = ($page_num == 0 ? 1 : $page_num);
		
		$_GET ["p"] = $page_num;
		
		$options = array (
				'where' => $condition 
		);
		$demand = M ( 'demand' );
		
		$demand->options = $options;
		$count = $demand->count ();
		$page = new \Think\Page ( $count, $this->page_size );
		
		$options = array (
				'where' => $condition,
				'order' => 'id desc' 
		);
		$data = $demand->limit ( $page->firstRow . ',' . $page->listRows )->select ( $options );
		foreach ( $data as &$demand ) {
			$demand ['city_string'] = M ( 'school' )->getFieldById ( $demand ['city'], 'title_fix' );
			$demand ['university_string'] = M ( 'school' )->getFieldById ( $demand ['university'], 'title_fix' );
			$demand ['college_string'] = M ( 'school' )->getFieldById ( $demand ['college'], 'title_fix' );
			$demand ['major_string'] = M ( 'school' )->getFieldById ( $demand ['major'], 'title_fix' );
			$demand ['member'] = M ( 'member' )->where ( array (
					'id' => $demand ['member_id'] 
			) )->find ();
			$demand ['avator'] = GetSmallAvatar ( $demand ['member_id'] );

			//备注
			$profes_type = $demand['profes_type'];
			if($profes_type == 1){
				$demand['memo'] = '非统考 '.$demand['university_string'].' '.$demand['college_string'].' '.$demand['major_string'];
			}elseif($profes_type == 2){
				$demand['memo'] = '统考 '.$demand['major_string'];
			}elseif($profes_type == 3){
				$demand['memo'] = '公共课';
			}
		}
		
		$page_count = ceil ( $page->totalRows / $page->listRows );
		
		$data = array (
				'list' => $data,
				'count' => count ( $data ),
				'next_page' => null 
		);
		
		if ($page_count > 1 && $page_num < $page_count) {
			$data ['next_page'] = $this->url ( '/v1/demands/by/school/role/' . $role . '/type/' . $demand_type . '/province/' . $province_id . '/university/' . $university_id . '/college/' . $college_id . '/page/' . ++ $page_num );
		}
		return $data;
	}
}