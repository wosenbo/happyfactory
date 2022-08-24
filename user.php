<?php
/**
 * HappyFactory.User
 * $Id: user.php 2010/4/21 20:09 xiaogang $
 */

function get_user($uid) {
	global $db;
	return $db->fetch_first("SELECT u.*, up.* FROM game_users u LEFT JOIN game_userproperty up ON up.uid = u.uid WHERE u.uid = '{$uid}'");
}

require_once './include/api.php';

switch (gp('action')) {
	case 'info':
		$user = get_user($uid);
		if (!$user) {
			json_error('用户不存在');
		}
		json_result(['user' => $user]);
		break;
	
	default:
		// code...
		break;
}

// switch($action) {
// 	case 'getJsonData':
// 		$result = json_encode(get_user_info($my_uid));
// 		echo $result;
// 		break;
	
// 	case 'getMoney':
// 		$user = get_user_info($my_uid);
// 		$money = isset($user['money']) ? $user['money'] : 0;
// 		echo $money;
// 		break;

// 	default:
// 		echo 'Action not found';
// }

// function get_user_info($userid)
// {
// 	global $db;
// 	$user = $db->fetch_first("SELECT * FROM game_userproperty WHERE uid = $uid");
// 	return $user;
// }


/*
require_once './include/common.inc.php';

switch($_POST['a'])
{
	case 'get_info':
		$arr = $db->fetch_first("SELECT * FROM game_userproperty WHERE uid = $uid");
		$result = json_encode($arr);
		echo $result;
		break;
	
	case 'get_user_money':
		$arr = $db->fetch_first("SELECT uid,money FROM game_userproperty WHERE uid = $uid");
		echo ($arr) ? $arr['money'] : '0';
		break;
	
	case 'get_user_empiric':
		$arr = $db->fetch_first("SELECT u_p.empiricnow,u_l.empiric FROM game_userproperty u_p ".
								"LEFT JOIN game_userlevel u_l ON u_p.level = u_l.level ".
								"WHERE u_p.uid = $uid");
		echo ($arr) ? $arr['empiricnow'] .'/'. $arr['empiric'] : '0/0';
		break;
	
	default:
		echo 'ERROR';
		break;
}
*/
