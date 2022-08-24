<?php
function extract_materials($data){
	$materials = [];
	foreach (explode(',', $data) as $item) {
		list($pid, $amount) = explode(':', $item);
		$materials[] = ['pid' => intval($pid), 'amount' => intval($amount)];
	}
	return $materials;
}

function check_material($uid, $materials){
	global $db;
	$check_pass = true;
	foreach($materials as $material){
		$row = $db->fetch_first("SELECT count FROM game_warehouses WHERE uid = '{$uid}' AND pid = '{$material['pid']}'");
		if(!$row || $row['count'] < $material['amount']){
			$check_pass = false;
			break;
		}
	}
	return $check_pass;
}

function get_task($taskid){
	global $db;
	return $db->fetch_first("SELECT * FROM game_tasks WHERE taskid = '{$taskid}'");
}

function wh_reduce_count($uid, $pid, $count) {
	global $db;
	$count = intval($count);
	if ($count === 0) {
		return;
	}
	$operator = $count > 0 ? '+' : '-';
	$count = abs($count);
	$db->query("UPDATE game_warehouses SET count = count {$operator} {$count} WHERE uid = '{$uid}' AND pid = '{$pid}'");
}

function wh_clean($uid){
	global $db;
	$db->query("DELETE FROM game_warehouses WHERE uid = '{$uid}' AND count <= 0");
}

require_once './include/common.inc.php';
require_once './include/user.func.php';

$action = gp('action');

switch($action){
	case 'accept':
		$taskid = gp('taskid', 0);
		$task = get_task($taskid);
		$task['items'] = extract_materials($task['items']);
		$reward_money = rand($task['rewardmin'], $task['rewardmax']);
		if(!check_material($uid, $task['items'])){
			$message = '任务完成失败，缺少所需物资';
			$smarty->assign('message', $message);
			$smarty->display('task.html');
			exit;
		}
		foreach($task['items'] as $material){
			wh_reduce_count($uid, $material['pid'], -$material['amount']);
		}
		wh_clean($uid);
		update_money($uid, $reward_money);
		$db->query(sprintf("INSERT INTO game_tasklog (uid, taskid, name, reward, dateline) VALUES (%d, %d, '%s', %d, %d)",
							$uid, $taskid, $task['name'], $reward_money, time()));
		$message = "任务完成，获得 <strong>{$reward_money}</strong> 金钱奖励！";
		$smarty->assign('message', $message);
		$smarty->display('task.html');
		break;
	
	default:
		$tasklogQuery = $db->query("SELECT tl.*,u.username FROM game_tasklog tl ".
								   "LEFT JOIN game_users u ON tl.uid=u.uid ORDER BY tl.id DESC LIMIT 5");
		while($tasklog = $db->fetch_array($tasklogQuery))
		{
			$taskLogList[] = array(
								   'name' => $tasklog['name'],
								   'username' => $tasklog['username'],
								   'reward' => $tasklog['reward']
								   );
		}
		$smarty->assign('taskLogList', $taskLogList);
	
		$query = $db->query("SELECT * FROM game_tasks");
		while($task = $db->fetch_array($query))
		{
			$taskList[] = array(
								'taskid' => $task['taskid'],
								'name' => $task['name'],
								'description' => $task['description'],
								'rewardmin' => $task['rewardmin'],
								'rewardmax' => $task['rewardmax']
								);
		}
		$smarty->assign('taskList', $taskList);
		$smarty->display('task.html');
		break;
}
