<?php
/* $Id:topuser.php 2010/4/26 22:24 xiaogang $ */

function get_top_users($site_id) {
	global $db;
	$query = $db->query("SELECT u.*, up.* FROM game_users u LEFT JOIN game_userproperty up ON up.uid = u.uid WHERE u.siteid = '{$site_id}' ORDER BY up.money DESC LIMIT 10");
	$list = array();
	while ($row = $db->fetch_array($query)) {
		$list[] = $row;
	}
	return $list;
}

require_once './include/common.inc.php';

// $sql = "SELECT u.*, u_p.* FROM game_users u
// 	LEFT JOIN game_userproperty u_p ON u.uid = u_p.uid
// 	WHERE u.siteid = '$siteid' AND u.unstalled = 0
// 	ORDER BY u_p.usermoney
// 	LIMIT 10";
// $query = $db->query($sql);

// $users = array();
// while($row = $db->fetch_array($query)) {
// 	$users[] = $row;
// }
$site_id = gp('site_id', 100);
$smarty->assign('top_user', get_top_users($site_id));
$smarty->display('topuser.html');
