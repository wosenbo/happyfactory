<?php
function update_money($uid, $money) {
	global $db;
	$money = intval($money);
	if ($money === 0) {
		return;
	}
	$operator = $money > 0 ? '+' : '-';
	$money = abs($money);
	$db->query("UPDATE game_userproperty SET money = money {$operator} {$money} WHERE uid = '{$uid}'");
}

function get_user_study($uid) {
	global $db;
	return $db->fetch_first("SELECT * FROM game_userstudy WHERE uid = '{$uid}'");
}
