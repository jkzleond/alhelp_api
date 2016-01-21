<?php
namespace Api\Controller;

class ShopCartController extends ApiBaseController {
	private $cart = null;
	private $cart_id = '';

	public function __construct() {
		parent::__construct();
		$this->check_token();
		$cart = query_sql('', "select * from cart where member_id=" . $this->uid);
		$cart = $cart[0];
		if ($cart) {
			$this->cart_id = $cart['id'];
			$this->cart = json_decode(urldecode($cart['json_content']), true);
		} else {
			$this->cart = null;
		}
	}

	//购物车列表
	public function lists_get() {
		$this->success($this->cart === null ? array() : $this->cart);
	}

	//添加商品
	public function add_cart_post() {
		$body = $this->get_request_data('cart');
		if (!in_array($body['table'], array('bid', 'demand', 'book', 'learning_periods'))) {
			//5012商品类型不存在
			$this->error(5012);
		}
		if ($this->check_body_fields($body, array('table', 'id', 'num'))) {
			$item = array();
			if ($body['num'] < 1) {
				$body['num'] = 1;
			} else {
				$body['num'] = intval($body['num']);
			}
			if ($body['table'] == 'bid') {
				//获取服务信息
				$bidInfo = get_info(D('Common/bid'), array('id' => $body['id']));
				if (empty($bidInfo)) {
					//5013商品不存在
					$this->error(5013);
				}
				$item['business'] = $bidInfo['service_member_id'];
				if ($bidInfo['demand_type'] == 1) {
					$price = $bidInfo['cost'];
				} else {
					//获取价格
					$periods = get_result('learning_periods', array(
						'bid_id' => $body['id'],
						'status' => array('gt', '-1'),
					));
					$price = 0;
					foreach ($periods as $val) {
						$price = $price + $val['price'];
					}
				}

				$item['id'] = $bidInfo['id'];
				$item['name'] = $bidInfo['description'];
				$item['price'] = $price;
				$item['brand'] = 'xzb';
				$item['thumb'] = '';
				$item['num'] = $body['num'];
				$item['shipping'] = '';

			} else if ($body['table'] == 'demand') {

				//获取服务资料信息
				$demandInfo = get_info('demand', array('id' => $body['id']));

				if (empty($demandInfo)) {

					//5013商品不存在
					$this->error(5013);

				}

				$item['business'] = $demandInfo['member_id'];

				$item['id'] = $demandInfo['id'];
				$item['name'] = $demandInfo['description'];
				$item['price'] = $demandInfo['cost'];
				$item['brand'] = 'xzb';
				$item['thumb'] = '';
				$item['num'] = $body['num'];
				$item['shipping'] = '';

			} else {

				$info = get_info($body['table'], array('id' => $body['id']));

				if (empty($info)) {

					//5013商品不存在
					$this->error(5013);

				}

				$item['business'] = $info['member_id'];

				$item['id'] = $info['id'];
				$item['name'] = $info['title'];
				$item['price'] = $info['price'];
				$item['brand'] = 'xzb';
				$item['thumb'] = $info['cover'];
				$item['num'] = $body['num'];
				$item['shipping'] = '';
			}

			$item['id'] = $body['table'] . '_' . $item['id'];
			$item['price'] = number_format($item['price'], 2);

			if ($this->hasCart($item['business'], $item['id'])) {

				$this->cart[$item['business']][$item['id']]['num'] += $body['num'];

				if (execute_sql('', "update cart set json_content='" . urlencode(json_encode($this->cart)) . "' where id=" . $this->cart_id)) {
					$this->success();
				}

			} else {

				$this->cart[$item['business']][$item['id']] = $item;

				if (!empty($this->cart_id)) {

					if (execute_sql('', "update cart set json_content='" . urlencode(json_encode($this->cart)) . "' where id=" . $this->cart_id)) {
						$this->success();
					}

				} else {

					if (execute_sql('', "insert into cart(member_id,json_content) values(" . $this->uid . ",'" . urlencode(json_encode($this->cart)) . "')")) {
						$this->success();
					}

				}

			}

			//5014添加购物车失败
			$this->error(5014);

		} else {
			//缺少参数
			$this->error(1001);
		}

	}

	//修改购物车数量(删除)
	public function modNum_put() {

		$body = $this->get_request_data('cart');

		if ($this->check_body_fields($body, array('business', 'id', 'num'))) {

			if ($this->hasCart($body['business'], $body['id'])) {

				$body['num'] = intval($body['num']);

				if ($body['num'] > 0) {

					$this->cart[$body['business']][$body['id']]['num'] = intval($body['num']);

				} else {

					unset($this->cart[$body['business']][$body['id']]);

					if (empty($this->cart[$body['business']])) {
						unset($this->cart[$body['business']]);
					}

				}

				execute_sql('', "update cart set json_content='" . urlencode(json_encode($this->cart)) . "' where id=" . $this->cart_id);

				$this->success();

			} else {
				//修改产品不存在
				$this->error(5011);
			}
		} else {
			//缺少参数
			$this->error(1001);
		}
	}

	//生成订单
	public function create_order_get() {

		if (empty($this->cart)) {
			//5016购物车没有商品
			$this->error(5016);
		}

		$id = intval(I('get.id', ''));

		$m = M();

		$map['id'] = $id;
		$map['member_id'] = $this->uid;

		$resource = $m->table('address')->where($map)->find();

		if (empty($resource)) {
			//5015地址不存在
			$this->error(5015);
		}

		$order_number = getordcode();

		$total = 0;

		while (list($business, $commodity) = each($this->cart)) {
			list($id, $commodity) = each($commodity);
			$total += $commodity['price'];

			$pro_arr = explode('_', $id);

			$order_product[] = array(
				'order_id' => $order_number,
				'table_name' => $pro_arr[0],
				'table_id' => $pro_arr[1],
				'title' => $commodity['name'],
				'price' => $commodity['price'],
				'quantity' => $commodity['num'],
				'status' => 1,
				'add_time' => date('Y-m-d H:i:s'),
			);

		}

		p($order_product);

		$m->table('order_product')->addAll($order_product);

		$this->success();

	}

	//判断商品是否存在
	private function hasCart($business, $id) {
		return array_key_exists($id, $this->cart[$business]);
	}
}