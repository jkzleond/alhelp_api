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
    public function getRecentContact($uid, $page_num=1, $page_size=10) {

        $get_rct_total_sql = <<<SQL
        select count(1) as total from (
              (
                select id as message_id, to_member_id as concat_id from imessage
                where from_member_id = '$uid' and to_member_to != '$uid'
              )
              union
              (
                select id as message_id, from_member_id as concat_id from imessage
                where to_member_id = '$uid' and from_member_to != '$uid'
              )
            ) rec_m
        group by concat_id
        order by message_id desc
SQL;
        $this->query($get_rct_total_sql);
        $get_recent_message_id_sql = <<<SQL
        select max(rec_m.message_id) as message_id, rec_m.concat_id from (
              (
                select id as message_id, to_member_id as concat_id from imessage
                where from_member_id = '$uid' and to_member_to != '$uid'
              )
              union
              (
                select id as message_id, from_member_id as concat_id from imessage
                where to_member_id = '$uid' and from_member_to != '$uid'
              )
            ) rec_m
        group by concat_id
        order by message_id desc
SQL;
        $total_rows = $this->query
        $page = \Think\Page();

        $concat_list = $this->field('rec_m.concat_id, mb.nickname, mb.avatar, m.content as message_content, m.mime_type, m.goods_id')
                                ->table($get_recent_message_id_sql)->alias('rec_m')
                                ->join('left join message as m on m.id = rec_m.message_id')
                                ->join('left join member as mb on mb.id = rec_m.concat_id')
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