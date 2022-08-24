<?php
/* $Id:invite.php 2010/4/25 22:34 xiaogang $ */

require_once './include/common.inc.php';

define('INVITE_AWARD', 5000);

$action = gp('action');

switch($action) {
	case 'send':
		$uids = $_POST['ids'];
		foreach($uids as $userId) {
			$db->query("REPLACE INTO game_invites(uid,inviter,dateline) VALUES (
				'$userId','$uid','$timestamp')");
		}
		$my->redirect('invite.php');
		break;
		
	case 'come':
		$my->redirect('index.php');
		break;

	default:
		// $total = $db->result_first("SELECT COUNT(*) FROM game_invites WHERE status = 1 AND inviter = '$uid'");
		$total = 0;
		
		$award = INVITE_AWARD * $total;
		
		$smarty->assign('invite_count', $total);
		$smarty->assign('invite_award', $award);
		$smarty->display('invite.html');
		break;
}

?>