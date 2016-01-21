<?php
namespace Api\Controller;
use Think\Controller\RestController;

class ApiBaseController extends RestController {
	
	public $pageSize = 10;

	public function __construct() {

		//加载错误代码配置
		L(include APP_PATH . 'Api/Conf/error_code.php');
		parent::__construct();
		if (MODULE_NAME == 'ApiBase') {
			$this->sendHttpStatus(404);
			exit;
		}
       //var_dump($_GET);
		$this->_type = 'html';

		//获取token
		if ($this->is_check_token) {
			$this->check_token();
		}

	}

	/**
	 * 是否检查token的有效性
	 * @var boolean
	 */
	protected $is_check_token = false;

	/**
	 * token
	 * @var array
	 */
	public static $token = null;

	/**
	 * 获取用户请求token
	 * @return string
	 */
	public function __get($name) {

		if (!self::$token) {
			return null;
		}

		if ($name == 'uid') {
			return self::$token->mid;
		} else if ($name == 'role') {
			return self::$token->role;
		}
	}

	/**
	 * 魔术方法 有不存在的操作的时候执行(覆盖，移除不必要的筛选)
	 * @access public
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (method_exists($this, $method . '_' . $this->_method)) {
			$fun = $method . '_' . $this->_method;
			$this->$fun();
		} elseif (method_exists($this, '_empty')) {
			// 如果定义了_empty操作 则调用
			$this->_empty($method, $args);
		} else {
			E(L('_ERROR_ACTION_') . ':' . ACTION_NAME);
		}
	}

	/**
	 * 检查token是否有效，并存储
	 * @param  boolean $exit 如果验证为无效，是否退出程序
	 * @return array/false
	 */
	protected function check_token($exit = true) {
		if (self::$token === null) {
			self::$token = UserController::get_token_info($this->get_header("X_Auth_Token"));
			if (!self::$token && $exit) {
				//无效token终止程序

				$this->error(1401);
			}
		} else if (self::$token === false) {
			return false;
		}

		self::$token = new \Api\Model\TokenModel(self::$token);

		return self::$token;
	}

	/**
	 * 获取请求的header参数
	 * @param  string  $name 参数名
	 * @param  boolean $json 目标参数是否json格式(如果是则自动解析为数组)
	 * @param  boolean $sys  是否系统参数
	 * @return string/json
	 */
	public function get_header($name, $json = false, $sys = false) {
		if ($sys) {
			$http = '';
		} else {
			$http = 'HTTP_';
		}

		$name = strtoupper($name);

		$data = I("server.{$http}{$name}");
		if ($json) {
			$data = json_decode($data, true);
		}

		return $data;
	}

	/**
	 * 获取客户端提交的数据
	 * @return array
	 */
	protected function get_request_data($name = "") {
		static $_data = array();

		if (empty($_data) && $_data !== null) {

			$_data = file_get_contents('php://input', 'r');
			// exit(dump($_data));
			if ($_data) {
				$_data = json_decode($_data, true);
			}
		}

		if (is_array($_data)) {
			if (empty($name)) {
				return $_data;
			} else if (array_key_exists($name, $_data)) {
				return $_data[$name];
			}
		}

		return null;
	}

	/**
	 * 返回错误消息
	 * @param  int $code     状态码
	 * @param  int $err_code 错误码
	 */
	public function return_error($code, $err_code) {
		$this->response(array('error' => $err_code), 'json', $code);
	}

	/**
	 * 设置要输出的header
	 * @param string/array		$name   header名称(如果是数组则标识多个header)
	 * @param string/boolean	$handle header值(如果$name是数组，则表示是否允许多个重复名称的header)
	 */
	public function set_header($name, $value = "") {
		if (headers_sent()) {
			return;
		}

		if (is_array($name)) {
			foreach ($name as $n => $v) {
				header("{$n}: {$v}", $value ? false : true);
			}
		} else {
			header("{$name}: {$value}");
		}

	}

	/**
	 * 生成API URL
	 * @param  string $path   地址
	 * @return string
	 */
	public function url($path) {
		return C("APP_DOMAIN.Api") . $path;
	}

	/**
	 * 检查body数据的完整性
	 * @param  array 	$body   用户提交的body数据
	 * @param  array 	$fields 搞定包含的字段
	 * @return boolean         	是否完整
	 */
	public static function check_body_fields($body, $fields) {
		if (empty($body)) {
			return false;
		}
		$keys = array_keys($body);

		$result = array_diff($fields, $keys);

		if (!count($result)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 输出成功数据
	 * @param  array 	$data 要输出的数据内容
	 */
	public function success($data = null) {
		if (empty($data)) {
			$data = null;
		}

		$this->response(
			array(
				'success' => true,
				'data' => $data,
			),
			'json'
		);
	}
	/**
	 * 输出错误数据
	 * @param  integer $code 错误码
	 */
	public function error($code) {
		$this->response(
			array(
				'success' => false,
				'code' => strval($code),
				'message' => L("E_" . $code),
			),
			'json'
		);
	}
	
	/**
	 * 输出错误数据
	 * @param  integer $code 错误码
	 */
	public function errorMsg($code,$msg) {
		$this->response(
				array(
						'success' => false,
						'code' => strval($code),
						'message' =>  L("E_" . $code).'=>'.$msg,
				),
				'json'
				);
	}

	/**
	 * 空操作返回404
	 */
	public function _empty() {
		exit($this->sendHttpStatus(404));
	}
	
	/**
	 * 通用分页列表数据集获取方法
	 *
	 * 可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
	 * 可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
	 * 可以通过url参数r指定每页数据条数,例如: index.html?r=5
	 *
	 * @param sting|Model  $model   模型名或模型实例
	 * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
	 * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
	 * 请求参数中如果指定了_order和_field则据此排序(优先级第二);
	 * 否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
	 *
	 * @param array        $base    基本的查询条件
	 * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
	 * @author 朱亚杰 <xcoolcc@gmail.com>
	 *
	 * @return array|false
	 * 返回数据集
	 */
	protected function lists($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true,$page_num=1) {
		$options = array();
		$REQUEST = (array) I('request.');
		if (is_string($model)) {
			$model = M($model);
		}
	
		$OPT = new \ReflectionProperty($model, 'options');
		$OPT->setAccessible(true);
	
		$pk = $model->getPk();
		if ($order === null) {
			//order置空
		} else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array(
				'desc',
				'asc',
		))) {
			$options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
		} elseif ($order === '' && empty($options['order']) && !empty($pk)) {
			$options['order'] = $pk . ' desc';
		} elseif ($order) {
			$options['order'] = $order;
		}
		unset($REQUEST['_order'], $REQUEST['_field']);
	
		$options['where'] = array_filter(array_merge((array) $base, /*$REQUEST,*/(array) $where), function ($val) {
			if ($val === '' || $val === null) {
				return false;
			} else {
				return true;
			}
		});
		if (empty($options['where'])) {
			unset($options['where']);
		}
		$options = array_merge((array) $OPT->getValue($model), $options);
		$total = $model->where($options['where'])->count();
		
		$pageCount=ceil ( $total / $this->pageSize );
		if ($pageCount<$_GET['p']) {
			$_GET['p']=$pageCount;
		}
		
		$page = new \Think\Page($total, $this->pageSize);
	
		$options['limit'] = $page->firstRow . ',' . $page->listRows;
	
		$model->setProperty('options', $options);
	
		$result = $model->field($field)->select();
		if (IsDebug()) {
			echo $model->getLastSql();
		}
		//echo $model->getLastSql();
		return array($page->totalRows,$page->listRows,$pageCount,$result);
	}
}