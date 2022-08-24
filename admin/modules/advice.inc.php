<?php
/**
 * $Id: advice.inc.php 2010/3/10 00:04 xiaogang $
 */

$advId = isset($advid) ? (int) $advid : 0;

switch($action) {
	case 'replay':
		if(!$operation) {
			$advice = $db->fetch_first("SELECT * FROM game_advices WHERE id = '$advId'");
			$smarty->assign('advice', $advice);
			$smarty->display('admin_advice_reply_form.html');
		} else {
			$content = isset($content) ? htmlspecialchars($content) : '';
			$db->query("UPDATE game_advices SET reply = '$content', status = 1 WHERE id = '$advId'");
			$my->redirect('admin.php?action=advice');
		}
		break;
	
	case 'delete':
		if(!empty($advId)) {
			$db->query("DELETE FROM game_advices WHERE id = '$advId'");
		}
		$my->redirect('admin.php?m=advice');
		break;
	
	default:
		$adviceList = array();
		$query = $db->query("SELECT a.*, u.username FROM game_advices a
			LEFT JOIN game_users u ON a.uid = u.uid");
		while($row = $db->fetch_array($query)) {
			$adviceList[] = $row;
		}
		$smarty->assign('adviceList', $adviceList);
		$smarty->display('admin_advice.html');
}

?>