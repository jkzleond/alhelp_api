<?php
namespace Common\Behavior;

class LoadRouteBehavior extends \Think\Behavior {
	public function run(&$param) {
		$r_file = C('API_ROUTE_FILE');
		if ($r_file) {
			$r_file = explode(',', $r_file);

			$routes = array();

			foreach ($r_file as $r) {
				$route = include APP_PATH . 'Api/Conf/' . $r . '.php';
				$routes = array_merge($routes, $route);
			}
            
			C('URL_ROUTE_RULES', $routes);
		}

		// echo 'iii';die;
		// dump(M());die;
		// require_once APP_PATH . 'Common/Common/core.php';

		// die('iii');
		// dump(C('URL_ROUTE_RULES'));
		// exit;
	}
}