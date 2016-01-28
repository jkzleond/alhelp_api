<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-1-10
 * Time: 下午8:04
 */

namespace Api\Controller;


class ImessageController extends ApiBaseController
{
    /**
     * 发悄悄话或群聊天
     * im/message/:type/:to_id\d
     */
    public function message_post() {
        $this->check_token();
        $to_id = I('get.to_id', null, 'intval');
        if (!$to_id) {
            $this->error(1001);
        }
        $type = I('get.type', 'single');

        $message_info = $this->get_request_data();
        $message = D('Imessage');
        $created = $message->create($message_info);
        if (!$created) $this->error(1500);

        if ($type == 'single') {
            $message->is_to_group = 0;
        } elseif ($type == 'group') {
            $message->is_to_group = 1;
        }
        $message->from_member_id = $this->uid;
        $message->to_id = $to_id;
        $success = $message->add();

        if (!$success) $this->error(1500);

        $this->success();
    }

    /**
     * 获取聊天记录
     */
    public function history_get() {
        $this->check_token();
        $to_id = I('get.to_id', null, 'intval');
        if (!$to_id) {
            $this->error(1001);
        }
        $type = I('get.type', 'single');
        $page_num = I('get.p', 1, 'intval');
        $page_size = I('get.ps', 10, 'intval');

        $message = D('Imessage');
        $history = $message->get_histroy($this->uid, $to_id, $type, $page_num, $page_size);
        $total_rows = $message->get_histroy_total($this->uid, $to_id, $type);
        $total_pages = ceil($total_rows/$page_size);

        $this->success(array(
            'list' => $history,
            'count' => count($history),
            'total_rows' => $total_rows,
            'total_pages' => $total_pages
        )); 
    }

    /**
     * 获取最近联系人列表
     * im/message/rct_contacts
     */
    public function recent_contacts_get() {
        $this->check_token();
        $page_num = I('get.p', 1, 'intval');
        $page_size = I('get.ps', 10, 'intval');
        $recent_concat_list = D('Imessage')->getRecentContacts($this->uid); //$this->uid

        $this->success($recent_concat_list);
    }
}