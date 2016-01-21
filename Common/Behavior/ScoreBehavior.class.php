<?php
namespace Common\Behavior;
use Think\Behavior;
/**
 * Description of scoreBehavior
 * 积分设置
 * @author Auser
 * 用法
 * $param=array('uid'=>$this->info['id'],'uname'=>$this->info['nickname'],'action'=>MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,'ruleId'=>7,'field'=>'score','isLimit'=>true,'isdate'=>true);
 * tag('setScore',$param);
 * 
 */
class ScoreBehavior extends Behavior
{
	
	public function run(&$_data)
	{
		$this->_score ( $_data );
	}
	
	/**
	 * 改变用户积分
	 * 配置操作行为必须和标签名称一致
	 * $data  
	 * uid 当前用户id 
	 * uname  当前用户名
	 * ruleId 规则id号
	 * field  用户更新的积分 或者 金币字段
	 * isdate 是否启用时间限制
	 */
	private function _score($_data)
	{
		if (empty ( $_data ['uid'] ) || empty ( $_data ['ruleId'] ) || empty ( $_data ['field'] ))
		{
			return false;
		}
		if (! isset ( $_data ['isdate'] ))
		{
			$_data ['isdate'] = false;
		}
		//获取积分
		$res = M ( 'score_rule' )->where ( array (
			'id' => $_data ['ruleId'] 
		) )->find ();
		if ($res ['type'] == 1)
		{
			$type = 'score';
		} else if ($res ['type'] == 2)
		{
			$type = '_score';
		} else if ($res ['type'] == 3)
		{
			$type = 'coin';
		} else if ($res ['type'] == 4)
		{
			$type = '_coin';
		}
		if (empty ( $res ) || $res ['score'] == 0)
			return false;
		
		//检测次数限
		if ($_data ['isLimit'] && $this->_check_num ( $_data ['uid'], $_data ['action'], $_data ['isdate'] ))
		{
			return false;
		} else
		{
			//更新用户积分  
			$result = M ( 'member' )->where ( array (
				'id' => $_data ['uid'] 
			) )->setInc ( $_data ['field'], $res ['score'] );
			$now_score = M ( 'member' )->where ( 'id=' . $_data ['uid'] )->getField ( 'score' );
			$level_info = get_result ( 'member_level', array (
				'status' => 1, 
				'score' => array (
					'elt', 
					$now_score 
				) 
			), 'id', 'score desc' );
			if ($level_info)
			{
				M ( 'member' )->where ( array (
					'id=' . $_data ['uid'] 
				) )->setField ( 'level', $level_info [0] ['id'] );
			}
			if ($result)
			{
				$data = array (
					'uid' => $_data ['uid'], 
					'table_name' => $_data ['tablename'], 
					'table_id' => $_data ['tableid'], 
					'uname' => $_data ['uname'], 
					'type' => $type, 
					'action' => $_data ['action'], 
					'rule_id' => $res ['id'], 
					'score' => $res ['score'], 
					'msg' => $res ['title'], 
					'create_time' => time () 
				);
				$score_log_mod = D ( 'score_log' );
				$score_log_mod->add ( $data );
			}
			return true;
		}
	
	}
	
	/**
	 * 检查次数限制
	 */
	private function _check_num($uid, $action, $isnum = false)
	{
		$return = false;
		$score_log = D ( 'score_log' );
		//登入每天一次
		$where ['uid'] = $uid;
		$where ['action'] = $action;
		$beginToday = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) );
		$endToday = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 1, date ( 'Y' ) ) - 1;
		if ($isnum)
		{
			$where ['create_time'] = array (
				'BETWEEN', 
				array (
					$beginToday, 
					$endToday 
				) 
			);
		}
		$res = $score_log->where ( $where )->find ();
		if (! empty ( $res ))
		{
			return true;
		} else
		{
			return false;
		}
	}

}
