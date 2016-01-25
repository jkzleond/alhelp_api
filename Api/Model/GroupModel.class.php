<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-1-10
 * Time: 下午6:49
 */

namespace Api\Model;
use \Think\Model\RelationModel;

class GroupModel extends RelationModel {

    protected $_auto = array(
        array('add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s')
    );

    /**
     * 添加群成员
     * @param $gid      群ID
     * @param $uid_list 成员ID列表
     * @return bool
     */
    public function addMembers($gid, $uid_list) {
        $group_member = M('group_member');
        $exits_member_list = $group_member->where(array(
            'group_id' => $gid,
            'member_id' => array('IN', $uid_list)
        ))->getField('member_id', true);
        $add_member_list = !empty($exits_member_list) ? array_diff($uid_list, $exits_member_list) : $uid_list ;
        if (empty($add_member_list)) {
            return true;
        }
        $data = array();
        foreach ($add_member_list as $member_id) {
            $data[] = array(
                'group_id' => $gid,
                'member_id' => $member_id
            );
        }
        $this->startTrans();
        $result = $group_member->addAll($data);
        if (!$result) {
            $this->rollback();
            return false;
        }
        $update_group_success = $this->where(array('id' => $gid))->setInc('member_num', count($data)); //更改群成员数
        if (!$update_group_success) {
            $this->rollback();
            return false;
        }
        return $this->commit();
    }

    /**
     * 删除群成员
     * @param $gid
     * @param $uid_list
     * @return bool
     */
    public function removeMembers($gid, $uid_list) {
        $group_member = M('group_member');
        $this->startTrans();
        $delete_rows = $group_member->where(array('group_id' => $gid, 'member_id' => array('IN', $uid_list)))->delete();
        $dec_member_num_success = $this->where(array('id' => $gid))->setDec('member_num', $delete_rows === false ? 0 : $delete_rows);
        if (!$dec_member_num_success) {
            $this->rollback();
            return false;
        }
        return $this->commit();
    }
}