<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-1-21
 * Time: 上午10:48
 */

namespace Api\Controller;


class OrderController extends ApiBaseController {
    /**
     * 生成订单
     */
    public function gen_order_post() {
        $this->check_token();
        $manifest = $this->get_request_data('manifest');
        $manifest['uid'] = $this->uid;
    }
}