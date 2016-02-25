<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-2-1
 * Time: 下午4:42
 */

namespace Common\Behavior;

/**
 * html5 ajax 跨域
 * Class AjaxCrossDomainBehavior
 * @package Common\Behavior
 */
class AjaxCrossDomainBehavior
{
    public function run(&$param) {

        //处理跨域策略
        $url_parts = !empty($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : parse_url($_SERVER['HTTP_ORIGIN']);
        $domain = empty($url_parts['port']) ? $url_parts['scheme'].'://'.$url_parts['host'] : $url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'];
        $allow_list = C('CROSS_DOMAIN_ALLOW');
        if (in_array($domain, $allow_list) or in_array('*', $allow_list) or $allow_list == '*') {
            header('Access-Control-Allow-Headers:x-auth-token, content-type');
            header('Access-Control-Allow-Origin:'.$domain);
            header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
        }

        //响应OPTIONS请求
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('HTTP/1.1 202');
            header('STATUS:202');
            exit();
        }

    }
}