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
        $manifest = $this->get_request_data();

        if ( empty($manifest) or !isset($manifest['address_id']) or !isset($manifest['items']) ) {
            $this->error(1001);
        }

        $manifest['uid'] = $this->uid;
        $orders = D('Order')->gen_order($manifest);

        if ( empty($orders) ) {
        	$this->error(1500);
        } else {
        	$this->success(array(
                'list' => $orders,
                'count' => count($orders)
            ));
        }
    }
    
    /**
     * 订单支付
     */
    public function pay_put() {
        $this->check_token();

        $order_ids = $this->get_request_data('order_ids');

        if (empty($order_ids)) {
            $this->error(1001);
        }
        
        $order_model = D('Order');
        $orders = $order_model->where(array('id' => array('in', $order_ids)))->select();
        $user_model = M('member');
        $user = $user_model->field('balance')->find($this->uid);

        $total = 0;
        foreach ($orders as $order) {
            $total += $order['total'] + $order['shipping'];
        }

        $pay_type = I('get.pay_type', 'remain');

        if ( $pay_type == 'remain' ) {
            if ( $user['balance'] < $total ) {
                //余额不足
                $this->error(3020);
            }
            //将余额转到系统账户(确认收货后转入卖家账户)
            $user_model->startTrans();
            $dec_balance_success = $user_model->where(array('id' => $this->uid))->setDec('balance', $total);
            if ( !$dec_balance_success ) {
                $user_model->rollback();
                $this->error(1500);
            }
            $inc_balance_success = $user_model->where(array('id' => 1))->setInc('balance', $total);
            if ( !$inc_balance_success ) {
                $user_model->rollback();
                $this->error(1500);
            }

            //更改订单为已付款
            $update_order_success = $order_model->where(array('id' => array('in', $order_ids)))->setField('status', 1);
            if (!$update_order_success) {
                $user_model->rollback();
                return false;
            }

            $success = $user_model->commit();
            
            if ( !$success ) $this->error(1500);

            $this->success();
            
        } elseif ( $pay_type == 'alipay' ) {
            //TODO alipay
        } elseif ( $pay_type === 'wxpay' ) {
            //TODO wxpay
        }
    }
}