<?php
namespace Api\Controller;

require_once APP_PATH . "SDK/Payment/alipay_server/lib/alipay_core.function.php";

require_once APP_PATH . "SDK/Payment/alipay_server/lib/alipay_rsa.function.php";

class AlipayController extends ApiBaseController {

	private $alipay_config = array();

	public function __construct() {
		parent::__construct();

		require_once APP_PATH . "SDK/Payment/alipay_server/alipay.config.php";

		var_dump(GetSmallAvatar(1));

		$this->alipay_config = $alipay_config;
	}

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

		$config = array(
			'service' => 'mobile.securitypay.pay', //接口名称，固定值。
			'partner' => $this->alipay_config['partner'], //合作者身份ID
			'_input_charset' => $this->alipay_config['input_charset'], //参数编码字符集
			'sign_type' => $this->alipay_config['sign_type'], //签名方式
			'notify_url' => U('Alipay/callback@api'), //服务器异步通知页面路径
			'out_trade_no' => $info['order_number'], //商户网站唯一订单号
			'subject' => 'testname', //商品名称
			'payment_type' => 1, //支付类型
			'seller_id' => $this->alipay_config['seller_email'], //卖家支付宝账号
			'total_fee' => $info['total'], //总金额
			'body' => 'describe', //商品详情
		);

		$this->success($config);

	}

	/**
	 * 获取请求签名
	 * @param $para_temp 请求的参数数组
	 * @return 签名
	 */
	public function getSign($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = argSort($para_filter);

		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);

		$isSgin = false;
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
		case "RSA":
			$isSgin = rsaSign($prestr, trim($this->alipay_config['private_key_path']));
			break;
		default:
			$isSgin = false;
		}

		return $isSgin;
	}

	/**
	 * 回调地址
	 */
	public function callback() {
		require_once APP_PATH . "SDK/Payment/alipay_server/lib/alipay_notify.class.php";

		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($this->alipay_config);

		$verify_result = $alipayNotify->verifyNotify();

		if ($verify_result) {
			//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];

			M('test')->where(array('order' => $out_trade_no))->save(array('content' => json_encode(I()), 'pay_time' => time()));

			//支付宝交易号
			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];

			if ($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			} else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "success"; //请不要修改或删除

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		} else {
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}

	}

}