<?php
require_once './include/api.php';
require_once './include/user.func.php';


/**
 * $Id: study.php 2010/3/10 00:33 xiaogang $
 */




$action = gp('action');

switch ($action) {	
	case 'upgrade':
		$type = gp('type');
		$confirm = intval(gp('confirm', 0));
		$f_level = 'elevel';
		$f_price = 'eprice';
		if ($type != 1) {
			$f_level = 'clevel';
			$f_price = 'cprice';
		}
		$user_study = get_user_study($uid);
		if ($user_study[$f_level] >= 5) {
			json_error('无需升级');
		}
		$new_level = $user_study[$f_level] + 1;
		$level_info = $db->fetch_first("SELECT {$f_price} AS price, {$f_level} AS level FROM game_studylevel WHERE {$f_level} = '{$new_level}'");
		if (!$confirm) {
			json_result(['level_info' => $level_info]);
		}
		if ($level_info['price'] > $userData['money']) {
			json_error('金币不足');
		}
		update_money($uid, -$level_info['price']);
		$db->query("UPDATE game_userstudy SET {$f_level} = '{$new_level}' WHERE uid = '{$uid}'");
		json_success();
		break;

	default:
		// $userStudy = $db->fetch_first("SELECT * FROM game_userstudy WHERE uid=$uid");
		$userStudy = get_user_study($uid);
		$query = $db->query("SELECT * FROM game_studys ORDER BY level");
		while($study = $db->fetch_array($query))
		{
			$studyLevels[] = array('efficiency'=>$study['efficiency'], 'cost'=>$study['cost'], 'level'=>$study['level']);
		}
		$smarty->assign('elevel', $userStudy['elevel']);
		$smarty->assign('clevel', $userStudy['clevel']);
		$smarty->assign('studyLevels', $studyLevels);
		$smarty->display('study.html');
}
?>