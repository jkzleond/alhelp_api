<?php
namespace Api\Model;

/**
 * token模型
 */
class TokenModel {

	public function __construct($token_data) {
		foreach ($token_data as $key => $value) {
			if ($key !== 'session') {
				$this->$key = $value;
			}
		}
		$this->session = $token_data['session'];
	}

	public $token;
	public $mid;
	public $create_time;
	public $expires;
	public $req_ip;
	public $role;
	private $session;
}