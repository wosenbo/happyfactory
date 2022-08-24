<?php
/**
 * $Id: user.inc.php 2010/3/10 23:56 xiaogang $
 */

switch($action) {
	case 'setLock':
		$lock = isset($lock) ? (int) $lock : 0;
		$userId = isset($userId) ? (int) $userId : 0;
		if($lock) {
			$db->query("UPDATE game_users SET status = '$lock' WHERE uid = '$userId'");
		} else {
			$db->query("UPDATE game_users SET status = 0 WHERE uid = '$userId'");
		}
		$my->redirect('admin.php?m=user');
		break;
	
	default:
		$userList = array();
		$query = $db->query("SELECT * FROM game_users");
		while($row = $db->fetch_array($query)) {
			$userList[] = $row;
		}
		$totalPlayer = $db->num_rows($query);
		$smarty->assign('totalPlayer', $totalPlayer);
		$smarty->assign('userList', $userList);
		$smarty->display('admin_user.html');
}

?>