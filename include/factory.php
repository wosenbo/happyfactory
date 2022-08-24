<?php
function get_user_factories($uid) {
	global $db;
	$query = $db->query("SELECT uf.*, f.factoryname FROM game_userfactory uf LEFT JOIN game_factorys f ON f.factid = uf.factid WHERE uf.uid = '{$uid}'");
	$list = array();
	while ($row = $db->fetch_array($query)) {
		$list[] = $row;
	}
	return $list;
}

function get_user_factory($uid, $cate_id) {
	global $db;
	return $db->fetch_first("SELECT uf.*, f.factoryname, f.makecateid FROM game_userfactory uf LEFT JOIN game_factorys f ON f.factid = uf.factid WHERE uf.uid = '{$uid}' AND f.makecateid = '{$cate_id}'");
}

function get_user_factory_by_id($uid, $fact_id){
	global $db;
	return $db->fetch_first("SELECT uf.*, f.factoryname, f.makecateid FROM game_userfactory uf LEFT JOIN game_factorys f ON f.factid = uf.factid WHERE uf.uid = '{$uid}' AND f.factid = '{$fact_id}'");
}

function get_factory_level($fact_id, $level = 1) {
	global $db;
	return $db->fetch_first("SELECT f.factoryname, fl.* FROM game_factorys f LEFT JOIN game_factorylevel fl ON f.factid = fl.factid WHERE f.factid = '{$fact_id}' AND fl.level = '{$level}'");
}
