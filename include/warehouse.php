<?php
function get_warehouse_count($uid, $pid) {
	global $db;
	$row = $db->fetch_first("SELECT count FROM game_warehouses WHERE uid='{$uid}' AND pid='{$pid}'");
	return $row ? intval($row['count']) : 0;
}

function get_warehouses($uid, $cate_id = 0) {
	global $db;
	$sqladd = '';
	$cate_id = intval($cate_id);
	if($cate_id){
		$sqladd .= " AND p.cateid = '{$cate_id}'";
	}
	$query = $db->query("SELECT w.*, p.pic, p.productname, p.price FROM game_warehouses w INNER JOIN game_products p ON p.pid = w.pid WHERE w.uid = '{$uid}'".$sqladd);
	$list = array();
	while ($row = $db->fetch_array($query)) {
		$list[] = $row;
	}
	return $list;
}

function get_warehouse_by_pid($uid, $pid) {
	global $db;
	return $db->fetch_first("SELECT w.*, p.productname, p.pic, p.price FROM game_warehouses w INNER JOIN game_products p ON p.pid = w.pid WHERE w.uid = '{$uid}' AND w.pid = '{$pid}'");
}

function get_warehouse($uid, $pid) {
	global $db;
	return $db->fetch_first("SELECT w.count, SUM(w.count * p.price) AS total_price FROM game_warehouses w INNER JOIN game_products p ON p.pid = w.pid WHERE w.uid = '{$uid}' AND w.pid = '{$pid}'");
}

function wh_count_product($uid, $pid) {
	global $db;
	$row = $db->fetch_first("SELECT COUNT(*) totalnum FROM game_warehouses WHERE uid = '{$uid}' AND pid = '{$pid}'");
	return $row ? intval($row['totalnum']) : 0;
}

function add_warehouse($uid, $pid, $amount) {
	global $db;
	$count = wh_count_product($uid, $pid);
	if ($count) {
		$db->query("UPDATE game_warehouses SET count = count + {$amount} WHERE uid = '{$uid}' AND pid = '{$pid}'");
	} else {
		$db->query("INSERT INTO game_warehouses (uid, pid, count) VALUES ('{$uid}', '{$pid}', '{$amount}')");
	}
}

function update_warehouse_count($uid, $pid, $count) {
	global $db;
	$count = intval($count);
	if ($count === 0) {
		return;
	}
	$operator = $count > 0 ? '+' : '-';
	$count = abs($count);
	$db->query("UPDATE game_warehouses SET count = count {$operator} {$count} WHERE uid = '{$uid}' AND pid = '{$pid}'");
}
