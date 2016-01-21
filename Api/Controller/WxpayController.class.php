<?php
namespace Api\Controller;

class WxpayController extends ApiBaseController {

	public function pay_get() {

		$order = I('get.order', '');

		$m = M('orders');

		// $map['from_member_id'] = $this->uid;
		$map['order_number'] = $order;
		// $map['status'] = 0;

		$info = $m->where($map)->find();

		if (empty($info)) {
			//未支付订单不存在
			$this->error(5015);

		}

		$total = $info['total'] * 100;
		$describe = 'testname';

		require_once APP_PATH . "SDK/Payment/wechat/lib/WxPay.Api.php";

		$input = new \WxPayUnifiedOrder();

		//设置公众账号ID
		$input->SetAppid(\WxPayConfig::APPID);

		//设置商户号
		$input->SetMch_id(\WxPayConfig::MCHID);

		//设置随机字符串
		$input->SetNonce_str(\WxPayApi::getNonceStr());

		//设置商品描述
		$input->SetBody($describe);

		//设置商户订单号
		$input->SetOut_trade_no($info['order_number']);

		//设置总金额
		$input->SetTotal_fee($total);

		//设置终端IP
		$input->SetSpbill_create_ip(get_client_ip());

		//设置通知地址
		$input->SetNotify_url(U('Wxpay/callback@api'));

		//设置交易类型
		$input->SetTrade_type('APP');

		$order = \WxPayApi::unifiedOrder($input);

		$this->success($order);
	}

	/**
	 *
	 */
	public function callback() {

	}

}