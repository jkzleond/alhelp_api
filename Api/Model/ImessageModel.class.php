<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-1-10
 * Time: 下午8:28
 */

namespace Api\Model;
use \Think\Model;
use \Think\Model\RelationModel;

class ImessageModel extends RelationModel
{   
    protected $_auto = array(
        array('add_time', 'date', Model::MODEL_INSERT, 'function', 'Y-m-d H:i:s')
    );

    /**
     * 获取指定用户的最尽联系人
     */
    public function get_recent_contacts($uid, $page_num=1, $page_size=10) {
        $get_recent_message_id_sql = <<<SQL
        (
          select max(rec_m.message_id) as message_id, rec_m.contact_id, rec_m.is_to_group,
          count( case when rec_m.is_read = 0 then 1 end ) as no_read_count
           from (
                (
                  select id as message_id, to_id as contact_id, is_to_group, 1 as is_read from imessage
                  where from_member_id = '$uid' and to_id != '$uid' and is_to_group != 1 and type = 1
                )
                union
                (
                  select id as message_id, from_member_id as contact_id, is_to_group, is_read from imessage
                  where to_id = '$uid' and from_member_id != '$uid' and is_to_group != 1 and type = 1
                )
                union
                (
                  select m.id as message_id, m.to_id as contact_id, m.is_to_group,
                  if( m.add_time > gm.last_read_time or gm.last_read_time is null, '0', '1' ) as is_read
                  from imessage as m
                  left join group_member as gm on gm.group_id = m.to_id and m.is_to_group = 1 
                  where m.is_to_group = 1 and m.type = 1 and gm.member_id = '$uid' 
                )
              ) rec_m
          group by contact_id, is_to_group
        )
SQL;

        $concat_list = $this->field("rec_m.contact_id, rec_m.is_to_group, rec_m.no_read_count,
          case when rec_m.is_to_group = 0 then 
            mb.nickname
          else
            g.name
          end as name,
          case when rec_m.is_to_group = 0 then
            mb.avatar
          else
            g.image
          end as avatar,
          case when rec_m.is_to_group = 1 then
            concat(mb.nickname, ':', m.content)
          else
            m.content
          end as msg_content, 
          m.mime_type, 
          m.goods_id")
                                ->table($get_recent_message_id_sql)->alias('rec_m')
                                ->join('left join imessage as m on m.id = rec_m.message_id')
                                ->join('left join member as mb on (mb.id = rec_m.contact_id and rec_m.is_to_group = 0) or (mb.id = m.from_member_id and rec_m.is_to_group = 1)')
                                ->join('left join `group` as g on g.id = rec_m.contact_id and rec_m.is_to_group = 1')
                                ->order('m.add_time desc')
                                ->select();
        if (!$concat_list) return null;

        foreach ($concat_list as &$concat) {
            //非群联系人头像
            if ($concat['is_to_group'] == 0) {
                $concat['avatar'] = GetSmallAvatar($concat['concat_id']);
            }

            if ( $concat['goods_id'] ){
                $concat['goods'] = D('Demand2')->get_by_id($concat['goods_id']);
            }
        }

        return $concat_list;

    }

    /**
     * 获取最近联系人总数
     * @param  int|string  $uid 用户ID
     * @return int
     */
    public function get_recent_contacts_total($uid) {
        $get_rct_total_sql = <<<SQL
        select count(distinct contact_id, is_to_group) as total from (
              (
                select id as message_id, to_id as contact_id, is_to_group from imessage
                where from_member_id = '$uid' and to_id != '$uid' and type = 1
              )
              union
              (
                select id as message_id, from_member_id as contact_id, is_to_group from imessage
                where to_id = '$uid' and from_member_id != '$uid' and is_to_group != 1 and type =1
              )
              union
              (
                select id as message_id, to_id as contact_id, is_to_group from imessage
                where from_member_id != '$uid' and is_to_group = 1 and to_id in (
                  select group_id from group_member where member_id = '$uid'
                ) and type = 1
              )
            ) rec_m
SQL;
        $count_result = $this->query($get_rct_total_sql);
        return !empty($count_result) ? $count_result[0]['total'] : 0;
    }

    /**
     * 获取用户与某用户或某群的聊天记录
     * @param  int|string  $uid       用户ID
     * @param  int|string  $to_id     其他用户ID
     * @param  string  $type      聊天类型
     * @param  integer $page_num  页码
     * @param  integer $page_size 每页条目数
     * @return array|bool         
     */
    public function get_history($uid, $to_id, $type='single', $page_num=1, $page_size=10) {

        $where = null;

        if ($type == 'single') {
            $where = array(
                array(
                    'from_member_id' => $uid,
                    'to_id' => $to_id,
                    'is_to_group' => 0,
                    'type' => 1
                ),
                array(
                    'from_member_id' => $to_id,
                    'to_id' => $uid,
                    'is_to_group' => 0,
                    'type' => 1
                ),
                '_logic' => 'OR'
            );
        } else {
            $where = array(
                'to_id' => $to_id,
                'is_to_group' => 1,
                'type' => 1
            );
        }

        $this->where($where);

        if ($page_num) {
            $this->page($page_num, $page_size);
        }

        $messages = $this->order('add_time desc')->select();
        return $messages;
    }

    /**
     * 获取历史消息总数
     * @param  int|string $uid    用户ID
     * @param  int|string $to_id  其他用户ID
     * @param  int|string $type   消息类型 单用户或群
     * @return int
     */
    public function get_history_total($uid, $to_id, $type='single') {
        $where = null;

        if ($type == 'single') {
            $where = array(
                array(
                    'from_member_id' => $uid,
                    'to_id' => $to_id,
                    'is_to_group' => 0,
                    'type' => 1
                ),
                array(
                    'from_member_id' => $to_id,
                    'to_id' => $uid,
                    'is_to_group' => 0,
                    'type' => 1
                ),
                '_logic' => 'OR'
            );
        } else {
            $where = array(
                'to_id' => $to_id,
                'is_to_group' => 1,
                'type' => 1
            );
        }

        $total = $this->where($where)
                      ->count();
        return $total;
    }

    /**
     * 获取发给某用户的未读信息
     * @param $uid
     * @param string|null $type
     * @param string|int|null $from_id
     * @param int $page_num
     * @param int $page_size
     * @return array
     */
    public function get_no_read($uid, $type=null, $from_id=null, $page_num=1, $page_size=10) {
        $group_in = M('group_member')->where(array('member_id' => $uid))->getField('group_id', true);

        $single_condition = array(
            'm.to_id' => $uid,                          //非群
            'm.is_to_group' => 0,
            'm.is_read' => 0,
            'm.type' => 1
        );
        $group_condition = array(
            'm.to_id' => array('in', $group_in),        //群
            'm.is_to_group' => 1,
            'm.type' => 1,
            '_string' => 'm.add_time > gm.last_read_time or gm.last_read_time is null' //群未读通过最后一次阅读群消息时间来标记
        );
        $condition = null;

        if ($from_id) {
            $single_condition['m.from_member_id'] = array(
                array('neq', $uid),
                array('eq', $from_id)
            );
            $group_condition['m.from_member_id'] = array(
                array('neq', $uid),
                array('eq', $from_id)
            );
        } else {
            $single_condition['m.from_member_id'] = array('neq', $uid);
            $group_condition['m.from_member_id'] = array('neq', $uid);
        }

        if ($type == 'single' or empty($group_in)) {
            //获取用户发来的未读消息,或用户没有加入任何群
            $condition = $single_condition;
        } elseif ($type == 'group') {
            $condition = $group_condition;
        } else {
            $condition = array(
                $single_condition,
                $group_condition,
                '_logic' => 'OR'
            );
        }

        $this->alias('m')->join('left join group_member gm on gm.group_id = m.to_id and m.is_to_group = 1')
            ->where($condition);

        if ($page_num) {
            $this->page($page_num, $page_size);
        }

        $no_read = $this->field("m.id, m.mime_type, if(m.is_to_group = 1, '1','0') as is_to_group, if(m.is_read = 1, '1', '0') as is_read,
        case when length(m.content) > 10 and m.mime_type = 0 then
        concat(left(m.content, '10'), '...')
        when m.mime_type = 0 then
        m.content
        when m.mime_type = 1 then
        '[图片]'
        when m.mime_type = 2 then
        '[声音]'
        end as content,
        case when m.mime_type = 1 or m.mime_type = 2 then
        m.content
        else
        null
        end as src,
        case when m.is_to_group = 1 then
        m.to_id
        else
        m.from_member_id
        end as from_member_id, m.add_time
        ")->order('add_time desc')->select();

        return $no_read;
    }

    /**
     * 获取未读总条数
     * @param string|int $uid
     * @param string     $start_time 起始时间
     * @return int
     */
    public function get_no_read_total($uid, $start_time=null) {
        $group_in = M('group_member')->where(array('member_id' => $uid))->getField('group_id', true);

        $condition = array(
            array(
                'm.to_id' => $uid,                          //非群
                'm.from_member_id' => array('neq', $uid),
                'm.is_to_group' => 0,
                'm.is_read' => 0,
                'm.type' => 1
            ),
            '_logic' => 'OR'
        );

        if ( !empty($group_in) ){
            $condition[] = array(
                'm.to_id' => array('in', $group_in),        //群
                'm.from_member_id' => array('neq', $uid),
                'm.is_to_group' => 1,
                'm.type' => 1,
                '_string' => 'm.add_time > gm.last_read_time or gm.last_read_time is null'
            );
        }

        if (!empty($start_time)) {
            $condition = array(
                $condition,
                'm.add_time' => array('gt', $start_time)
            );
        }

        $total = $this->table(array('imessage' => 'm'))
            ->join('left join group_member gm on gm.group_id = m.to_id and m.is_to_group = 1')
            ->where($condition)
            ->count();
        return $total;
    }
}