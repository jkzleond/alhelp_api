<?php
namespace Api\Model;
use Think\Model;

class OrderModel extends BaseModel {

	protected $tableName = 'orders';

	/**
	 * 订单列表
	 * @param  string $uid  用户id
	 * @param  integer $role 角色
	 * @param  string $type 订单类型
	 * @param  string $expire 期限
	 */
	public function orders($uid, $type, $expire_string = '') {
		$condition = array('_string' => "from_member_id = {$uid} OR to_member_id = {$uid} ", 'status' => $type);
		if ($type == 99) {
			unset($condition['status']);
		}
		if ($type == 88) {
			$condition = array("from_member_id" => $uid);
		}
		if ($type == 77) {
			$condition = array("to_member_id" => $uid);
		}
		if ($expire_string) {
			$expire_count = intval(substr($expire_string, 0, -1));
			$expire_type  = substr($expire_string, -1);
			$expire_types = array('d', 'w', 'm', 'y', 'c');
			if (!in_array($expire_type, $expire_types) or !$expire_count) {
				return 3018;
			}

			if ($expire_type == 'd') {
				$expire_day = $expire_count;
			} else if ($expire_type == 'w') {
				$expire_day = $expire_count * 7;
			} else if ($expire_type == 'm') {
				$expire_day = $expire_count * 31;
			} else if ($expire_type == 'y') {
				$expire_day = $expire_count * 366;
			} else if ($expire_type == 'c') {
				$expire_day = $expire_count * 366 * 100;
			}
			$now         = time();
			$expire_time = $now - 3600 * 24 * $expire_day;

			$now_date_string    = date('Y-m-d H:i:s', $now);
			$expire_time_string = date('Y-m-d H:i:s', $expire_time);

			$condition['add_time'] = array(array('EGT', $expire_time_string), array('ELT', $now_date_string));
		}
		$data = $this->where($condition)->select();
		foreach ($data as &$key) {
			// $order_history  = M('order_status_history')->where(array('order_id' => $key['id']))->select();
			// $key['history'] = $order_history;

			// $order_comments  = M('order_comments')->where(array('order_id' => $key['id']))->select();
			// $key['comments'] = $order_comments;

			$order_products = M('order_product')->where(array('order_id' => $key['id']))->select();
			$products       = array();
			foreach ($order_products as $product) {
				$this_product = M($product['table_name'])->where(array('id' => $product['table_id']))->find();
				if ($this_product) {
					if ($product['table_name'] == 'demand') {
						array_push($products, array('type' => 'demand', 'title' => $this_product['description'], 'image' => $this_product['image']));
					}
					if ($product['table_name'] == 'book') {
						array_push($products, array('type' => 'book', 'title' => $this_product['title'], 'image' => $this_product['cover']));
					}
					if ($product['table_name'] == 'learning_period') {
						array_push($products, array('type' => 'learning_period', 'title' => $this_product['title'], 'image' => null));
					}
					if ($product['table_name'] == 'bid') {
						array_push($products, array('type' => 'bid', 'title' => $this_product['siging_remark'], 'image' => null));
					}
				}
			}
			$key['products'] = $products;
		}
		return $data;
	}

	/**
	 * 获取指定id订单详情
	 * @param string $id 订单id
	 */
	public function order($id, $uid) {
		$order = $this->where(array('id' => $id, '_string' => "from_member_id = {$uid} or to_member_id = {$uid}"))->find();
		if (!$order) {
			return 3017;
		}
		$order_history  = M('order_status_history')->where(array('order_id' => $id))->select();
		$order_comments = M('order_comments')->alias('comment')
			->field('comment.*, member.nickname')
			->join('LEFT JOIN __MEMBER__ ON member.id = comment.member_id')
			->where(array('comment.order_id' => $id))
			->select();
		$order_products          = M('order_product')->where(array('order_id' => $id))->select();
		$order['history']        = $order_history;
		$order['order_comments'] = $order_comments;

		foreach ($order_products as &$product) {
			if ($product['table_name'] == "learning_period") {
				$product['table_name'] = "learning_periods";
			}

			// $product           = M($product['table_name'])->where(array('id' => $product['table_id']))->find();
			// $product['detail'] = $product;

			$this_product = array();
			if ($product['table_name'] == 'bid') {
				$this_product = M($product['table_name'])->alias('product')
					->field('member.nickname, product.*')
					->join('LEFT JOIN __MEMBER__ ON product.service_member_id = member.id')
					->where(array('product.id' => $product['table_id']))
					->find();
			} else {
				$this_product = M($product['table_name'])->alias('product')
					->field('member.nickname, product.*')
					->join('LEFT JOIN __MEMBER__ ON member.id = product.member_id')
					->where(array('id' => $product['table_id']))
					->find();
			}

			if ($this_product) {
				if ($product['table_name'] == 'demand') {
					$product['product_type'] = 'demand';
					$product['nickname']     = $this_product['nickname'];
					$product['product_id']   = $product['table_id'];
					// array_push($products, array('type' => 'demand', 'title' => $this_product['description'], 'image' => $this_product['image']));
				}
				if ($product['table_name'] == 'book') {
					$product['product_type'] = 'book';
					$product['nickname']     = $this_product['nickname'];
					$product['product_id']   = $product['table_id'];
					// array_push($products, array('type' => 'book', 'title' => $this_product['title'], 'image' => $this_product['cover']));
				}
				if ($product['table_name'] == 'learning_period') {
					$product['product_type'] = 'learning_periods';
					$product['nickname']     = $this_product['nickname'];
					$product['product_id']   = $product['table_id'];
					// array_push($products, array('type' => 'learning_period', 'title' => $this_product['title'], 'image' => null));
				}
				if ($product['table_name'] == 'bid') {
					$product['product_type'] = 'bid';
					$product['nickname']     = $this_product['nickname'];
					$product['product_id']   = $product['table_id'];
					// array_push($products, array('type' => 'bid', 'title' => $this_product['siging_remark'], 'image' => null));
				}
			}
		}
		$order['products'] = $order_products;
		return $order;
	}

	public function product_detail($type, $id) {
		$this_product = M($type)->where(array('id' => $id))->find();
		if (!$this_product) {
			return 3022;
		} else {
			return $this_product;
		}
	}

	/**
	 * 生成订单
	 * @param array $manifest 购物清单
	 * @return array|null
	 */
	public function gen_order($manifest) {
		$this->startTrans();
		$order_num = getordcode();
		$business = array();
		$orders = array();

		//查询收货地址
		$address = M('address')->alias('addr')
					->field('addr.name, addr.phone, addr.postcode, addr.address, p.id as province, c.id as city, a.id as area')
					->join('lfet join area p on p.id = addr.province')
					->join('lfet join area c on c.id = addr.city')
					->join('left join area a on a.id = addr.area')
					->where(array('addr.id' => $manifest['address_id']))
					->find();

		foreach ($manifest['items'] as $item) {

			$item['total'] = 0;
			//查询产品信息
			foreach ( $item['products'] as &$product) {
				$product_info = M($product['table_name'])->find($product['table_id']);

				if ($item['business_id'] != $product_info['member_id']) {
					$this->rollback();
					return false;
				}

				$product['title'] = $product_info['title'];
				$product['price'] = $product_info['price'];
				$product['member_id'] = $product_info['member_id'];
				$item['total'] += $product['price'] * $product['quantity'];
			}

			$group_num = getordercode();
			$order_data = array(
				'order_num' => $order_num,
				'group_num' => $group_num,
				'from_member_id' => $manifest['uid'],
				'to_member_id' => $item['business_id'],
				'total' => $item['total'],
				'province' => $address['province'],
				'city' => $address['city'],
				'area' => $address['area'],
				'address' => $address['address'],
				'postcode' => $address['postcode'],
				'name' => $address['name'],
				'phone' => $address['phone'],
				'content' => $item['remark'],
				'shipping_template_id' => $item['shipping_template_id'],
				'shipping' => $item['shipping_price'],
				'add_time' => date('Y-m-d H:i:s')
			);

			$new_order_id = M('Orders')->add($order_data);
			if ( !$new_order_id ) {
				$this->rollback();
				return false;
			}

			$order['id'] = $new_order_id;
			$orders[] = $order_data;

			foreach ($item['products'] as $product) {
				//添加订单商品
				$product_data[] = array(
						'order_id' => $new_order_id,
						'table_name' => $product['table_name'],
						'table_id' => $product['table_id'],
						'title' => $product['title'],
						'price' => $product['price'],
						'quantity' => $product['quantity'],
						'status' => 1,
						'add_time' => date('Y-m-d H:i:s')
				);
			}
		}

		$add_product_success = M('order_product')->addAll($product_data);
		if (!$add_product_success) {
			$this->rollBack();
			return false;
		}

		return $this->commit() ? $orders : false;
	}
}