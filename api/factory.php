<?php
require_once '../include/factory.php';
require_once '../include/api.php';

switch (gp('action')) {
	case 'buy':
		$fact_id = gp('fact_id', 0);
		$fact_lvl = get_factory_level($fact_id);
		if ($fact_lvl['price'] > $userData['money']) {
			json_error('余额不足');
		}
		$user_facts = get_user_factories($uid);
		if (count($user_facts) > 0) {
			json_error('无须重复购买');
		}
		break;
	case 'user':
		json_result(['factories' => get_user_factories($uid)]);
		break;
}

// $action = isset($_GET['action']) ? trim($_GET['action']) : '';1

// switch ($action) {

// case 'user_factories':
// $sql = "SELECT uf.*, f.factoryname, f.pic, f.pic2, f.makecateid FROM game_userfactory uf
// 	LEFT JOIN game_factorys f ON f.factid = uf.factid
// 	WHERE uf.uid = '{$uid}'";
// $result = $db->query($sql);
// $factories = array();
// while ($row = $db->fetch_array($result)) {
// 	$factories[] = $row;
// }
// die(json_encode(array(
// 	'factories' => $factories,
// )));
// break;

// }


/*
require_once './include/common.inc.php';

switch($action) {
	case 'buy':
		$factoryId = isset($factoryid) ? (int) $factoryid : 0;
		$factory = $db->fetch_first("SELECT f.factoryname,fl.price FROM game_factorys f
			LEFT JOIN game_factorylevel fl ON f.factid = fl.factid
			WHERE f.factid = '$factoryId' AND fl.level = 1");
		
		if($factory['price'] > $userData['money']) {
			echo '没有足够的金钱进行交易！';
		} else {
			$exists = $db->result_first("SELECT COUNT(*) FROM game_userfactory WHERE factid = '$factoryId' AND uid = '$uid'");
			if($exists > 0) {
				echo '无须重复购买！';
			} else {
				$db->query("INSERT INTO game_userfactory(factid,uid) VALUES ('$factoryId','$uid')");
				$db->query("UPDATE game_userproperty SET money = money + ". $factory['price'] ." WHERE uid = '$uid'");
				echo '购买成功！';
			}
		}
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
*/

?>