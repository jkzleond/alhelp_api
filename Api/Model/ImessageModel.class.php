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
    public function getRecentContacts($uid, $page_num=1, $page_size=10) {
        $get_rct_total_sql = <<<SQL
        select count(distinct contact_id) as total from (
              (
                select id as message_id, to_id as contact_id from imessage
                where from_member_id = '$uid' and to_id != '$uid'
              )
              union
              (
                select id as message_id, from_member_id as contact_id from imessage
                where to_id = '$uid' and from_member_id != '$uid' and is_to_group != 1
              )
              union
              (
                select id as message_id, from_member_id as contact_id from imessage
                where from_member_id != '$uid' and is_to_group = 1 and to_id in (
                  select group_id from group_member where member_id = '31527'
                )
              )
            ) rec_m
SQL;
        $count_result = $this->query($get_rct_total_sql);
        $total_rows = !empty($count_result) ? $count_result[0]['total'] : 0;

        if(!$total_rows) return null;

        $get_recent_message_id_sql = <<<SQL
        (
          select max(rec_m.message_id) as message_id, rec_m.contact_id from (
                (
                  select id as message_id, to_id as contact_id from imessage
                  where from_member_id = '$uid' and to_id != '$uid'
                )
                union
                (
                  select id as message_id, from_member_id as contact_id from imessage
                  where to_id = '$uid' and from_member_id != '$uid' and is_to_group != 1
                )
                union
                (
                  select id as message_id, from_member_id as contact_id from imessage
                  where from_member_id != '$uid' and is_to_group = 1 and to_id in (
                    select group_id from group_member where member_id = '31527'
                  )
                )
              ) rec_m
          group by contact_id
          order by message_id desc
        )
SQL;
        
       // $page = \Think\Page();

        $concat_list = $this->field('rec_m.contact_id, mb.nickname, mb.avatar, m.content as message_content, m.mime_type, m.goods_id')
                                ->table($get_recent_message_id_sql)->alias('rec_m')
                                ->join('left join imessage as m on m.id = rec_m.message_id')
                                ->join('left join member as mb on mb.id = rec_m.contact_id')
                                ->join('left join demand as d on d.id = m.goods_id')
                                ->select();

        if (!$concat_list) return null;

        foreach ($concat_list as &$concat) {
            if ( $concat['goods_id'] ){
                $concat['goods'] = D('Demand2')->get_by_id($concat['goods_id']);
            }
        }

        return $concat_list;

    }
}