<?php

// OLD CODES

require_once './include/api.php';
require_once './include/factory.php';
require_once './include/user.func.php';

$action = gp('action');

switch($action) {
	case 'buy':
		$fact_id = gp('factid', 0);
		$fact_level = get_factory_level($fact_id);
		if ($fact_level['price'] > $userData['money']) {
			json_error('金币不足');
		}
		$user_fact = get_user_factory_by_id($uid, $fact_id);
		if ($user_fact) {
			json_error('不能重复购买');
		}
		update_money($uid, -$fact_level['price']);
		$db->query("INSERT INTO game_userfactory (factid, uid, status) VALUES ('{$fact_id}', '{$uid}', 1)");
		$user_study = get_user_study($uid);
		if (!$user_study) {
			$db->query("INSERT INTO game_userstudy (uid, elevel, clevel) VALUES ('{$uid}', 1, 1)");
		}
		json_success();
		break;

	default:
		$factoryList = array();
		$query = $db->query("SELECT * FROM game_factorys");
		while($row = $db->fetch_array($query)) {
			$count = $db->fetch_first("SELECT COUNT(*) FROM game_userfactory WHERE uid = '$uid' AND factid = '". $factory['factid'] ."'");
			$row['count'] = $count;
			$factoryList[] = $row;
		}
		$smarty->assign('factoryList', $factoryList);
		$smarty->display('factory_list.html');
}
