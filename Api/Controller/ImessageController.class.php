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
        $page_num = I('get.p', null, 'intval');
        $page_size = I('get.ps', 10, 'intval');

        $message = D('Imessage');
        $history = $message->get_history($this->uid, $to_id, $type, $page_num, $page_size);
        $total_rows = $message->get_history_total($this->uid, $to_id, $type);
        $total_pages = $page_num ? ceil($total_rows/$page_size) : ( $total_rows > 0 ? 1 : 0 );

        $this->success(array(
            'list' => $history,
            'count' => count($history),
            'total_rows' => $total_rows,
            'total_pages' => $total_pages
        )); 
    }

    /**
     * 获取未读消息
     */
    public function no_read_msg_get() {
        $this->check_token();
        $page_num = I('get.p', null, 'intval');
        $page_size = I('get.ps', 10, 'intval');
        $message_model = D('Imessage');
        $no_read = $message_model->get_no_read($this->uid, $page_num, $page_size);
        $total_rows = $message_model->get_no_read_total($this->uid);
        $total_pages = $page_num ? ceil($total_rows/$page_size) : ( $total_rows > 0 ? 1 : 0 );
        $this->success(
            array(
                'list' => $no_read,
                'count' => count($no_read),
                'total_rows' => $total_rows,
                'total_pages' => $total_pages
            )
        );
    }

    /**
     * 获取未读消息总条数
     */
    public function no_read_msg_total_get() {
        $this->check_token();
        $total = D('Imessage')->get_no_read_total($this->uid);
        $this->success(array(
            'total' => $total
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
        $recent_concat_list = D('Imessage')->get_recent_contacts($this->uid);
        $this->success($recent_concat_list);
    }

    /**
     * 标记已读
     */
    public function mark_read_put() {
        $this->check_token();
        $type = I('get.type', 'single');
        $from_id = I('get.from_id');
        if (!$from_id) {
            $this->error(1001);
        }

        $mark_success = false;

        if ($type == 'single') {
            $mark_success = M('Imessage')->where(array(
                'from_member_id' => $from_id,
                'to_id' => $this->uid,
                'is_to_group' => 0
            ))->setField('is_read', 1);
        } else {
            $mark_success = M('GroupMember')->where(array(
                'member_id' => $this->uid,
                'group_id' => $from_id
            ))->setField('last_read_time', date('Y-m-d H:i:s'));
        }

        if ($mark_success === false) {
            $this->error(1500);
        }

        $this->success();
    }
}