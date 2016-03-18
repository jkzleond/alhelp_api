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

        $new_msg = array(
            'id' => $message->getLastInsID(),
            'content' => $message_info['content'],
            'mime_type' => $message_info['mime_type'],
            'filename' => $message_info['filename'],
            'type' => 1,
            'is_to_group' => $type = 'single' ? 0 : 1,
            'from_member_id' => $this->uid,
            'to_id' => $to_id,
            'is_read' => 0,
            'add_time' => date('Y-m-d H:i:s')
        );

        $from_member = M('member')->field('id, nickname, avatar')->find($this->uid);
        $from_member['avatar'] = GetSmallAvatar($this->uid);
        $new_msg['from_member'] = $from_member;
        $to_im_server = I('get.tis', '1', 'intval');
        if ($to_im_server) {
            @urlopen('http://localhost:5000/send_message', json_encode($new_msg));
        }
        $this->success($new_msg);
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
        $type = I('get.type', null);
        $from_id = I('get.from_id', null, 'intval');
        $page_num = I('get.p', null, 'intval');
        $page_size = I('get.ps', 10, 'intval');
        $message_model = D('Imessage');
        $no_read = $message_model->get_no_read($this->uid, $type, $from_id, $page_num, $page_size);
        $total_rows = $message_model->get_no_read_total($this->uid, $type, $from_id);
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
        $uid = I('get.uid', $this->uid, 'intval');
        $recent_concat_list = D('Imessage')->get_recent_contacts($uid);
        $this->success(array(
            'list' => $recent_concat_list,
            'count' => count($recent_concat_list)
        ));
    }

    /**
     * 获取好友列表
     */
    public function friend_list_get() {
        $follow_model = M('Follow');
        $uid = I("get.uid", 0, 'intval');

        if ($uid <= 0) {
            $this->check_token();
            $uid = $this->uid;
        }

        //构造sql
        //互相关注is_mutual
        $options = array(
            'alias' => 'f',
            'join' => array(
                "LEFT JOIN follow f2 ON f2.to_member_id = f.from_member_id AND f2.from_member_id = f.to_member_id",
                "INNER JOIN member m on f.to_member_id = m.id",
            ),
            'where' => array("f.from_member_id" => $uid),
            'field' => "UNIX_TIMESTAMP(f.add_time) as add_time,m.id AS member_id,m.nickname, IF(f2.id IS NOT NULL, '1', '0') as is_mutual",
            'order' => "nickname desc"
        );

        $page_num = I("get.page", 1, 'intval');
        $page_size = I('get.ps', 20, 'intval');

        $follow_model->options = $options;
        unset($follow_model->options['group']);
        unset($follow_model->options['field']);
        $total_rows = $follow_model->count();
        $total_pages = 1;

        if ($page_num) {
            $follow_model->page($page_num, $page_size);
            $total_pages = ceil($total_rows / $page_size);
        }

        $data = $follow_model->select($options);

        foreach ($data as &$value) {
            $value["avatar"] = GetSmallAvatar($value['to_member_id']);
            //$value["community"] = D("Community")->communities($value["to_member_id"]);
        }

        $data = array(
            'list' => $data,
            'count' => count($data),
            'total_pages' => $total_pages,
            'total_rows' => $total_rows,
            'next_page' => null
        );

        if ($total_pages > 1 && $page_num < $total_pages) {
            $data['next_page'] = $this->url("/v1/follow/page/" . ($page_num + 1));
        }

        $this->success($data);
    }

    /**
     * 标记已读
     */
    public function mark_read_put() {
        $this->check_token();
        $type = I('get.type');
        $from_id = I('get.from_id');
        if (!$from_id) {
            $this->error(1001);
        }

        $mark_success = false;

        if ($type == 'single') {
            $single_condition = array(
                'to_id' => $this->uid,
                'is_to_group' => 0
            );

            if ($from_id) {
                $single_condition['from_member_id'] = $from_id;
            }

            $mark_success = M('Imessage')->where($single_condition)->setField('is_read', 1);
        } else if($type == 'group') {
            $group_condition = array(
                'member_id' => $this->uid
            );

            if ($from_id) {
                $group_condition['group_id'] = $from_id;
            }
            $mark_success = M('GroupMember')->where($from_id)->setField('last_read_time', date('Y-m-d H:i:s'));
        } else {
            //按消息id标记
            $ids = $this->get_request_data('ids');
            $mark_success = M('Imessage')->where(array(
                'id' => array('in', $ids)
            ))->setField('is_read', 1);
            $group_msgs = M('Imessage')->where(array(
                'id' => array('in', $ids),
                'is_to_group' => '1'
            ))->order('add_time desc')->select();
            if(!empty($group_msgs)){
                $last_group_msg_time = $group_msgs[0]['add_time'];
                $group_ids = array();
                foreach ($group_msgs as $group_msg) {
                    $group_ids[] = $group_msg['to_id'];
                }
                $mark_success = M('GroupMember')->where(array(
                    'member_id' => $this->uid,
                    'group_id' => array('in', $group_ids)
                ))->setField('last_read_time', $last_group_msg_time);
            }
        }

        if ($mark_success === false) {
            $this->error(1500);
        }

        $this->success();
    }

    /**
     * 检查是否有新状态检查(长连接,检查新消息等,有新状态则调用相应接口获取数据)
     */
    public function sync_check() {
        set_time_limit(100);
        $this->check_token();
        $last_time = I('get._', 0, 'intval');
        $last_time = date('Y-m-d H:i:s', $last_time);
        $start_time = time();
        while ( true ) {
            $elapsed = time() - $start_time;
            $no_read_total = D('Imessage')->get_no_read_total($this->uid, null, null, $last_time);
            if ($no_read_total or $elapsed >= 60) {
                $this->success(array(
                    'has_new' => $no_read_total > 0,
                    '_' => time()
                ));
            }
            sleep(1);
        }
    }
}