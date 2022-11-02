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
